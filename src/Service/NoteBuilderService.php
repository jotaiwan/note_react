<?php

namespace  NoteReact\Service;

use NoteReact\DTO\NoteDTO;

use NoteReact\Util\LoggerTrait;

class NoteBuilderService
{
    use LoggerTrait;

    public function buildTableRows(array $rawNotes): array
    {
        $result = [];

        foreach ($rawNotes as $ticket => $notes) {
            foreach ($notes as $noteDTO) {
                $ticket = $noteDTO->ticket;
                $date   = $this->getNoteDates($noteDTO->date);
                $status = $noteDTO->status;
                $note   = $this->formatNote($noteDTO->note); // returns HTML fragments for <div class="code-block"> etc.
                // $note   = $noteDTO->note;

                $result[$ticket][] = [
                    'ticket'     => $ticket,
                    'status'     => $status,
                    'date'       => $date,
                    'note'       => $note,
                    'note_count' => count($notes),
                    'rowId'     => $noteDTO->rowId, // <-- add this
                ];
            }
        }

        return $result;
    }

    private function formatNote(string $raw): string
    {
        // \xdebug_break();
        // disable this line before react will handle \n in front end
        // $note = str_replace('\n', "\n", trim($raw));

        $note = $this->addSpecificWordBadge($raw);
        $note = $this->parseCustomBlocks($note);
        $note = $this->autoLink($note);
        // Note: do one time
        $note = preg_replace('/\\\"/', '"', $note);
        return nl2br($note);
        // return ($note);
    }

    private function addSpecificWordBadge($text)
    {
        $specificWords = ["切記", "注意", "重點", "重要", "必須", "總結", "提醒", "警告", "注意事項", "記住"];
        $pattern = '/' . implode('|', $specificWords) . '/u';

        $text = preg_replace_callback($pattern, function ($matches) {
            $word = $matches[0];
            return '&nbsp;<span class="badge bg-danger fs-4 px-3 py-2 text-white" style="font-size: 0.8rem;">' . $word .
                '</span>&nbsp;';
        }, $text);

        return $text;
    }

    private function parseCustomBlocks(string $text): string
    {
        // Escape inner content safely
        $escapeHtml = function ($string) {
            return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };

        // Replace {code} blocks
        $text = preg_replace_callback('/\{code\}([\s\S]*?)\{\/code\}/', function ($matches) use ($escapeHtml) {
            $content = trim($matches[1]);
            return '<div class="code-block">' . $escapeHtml($content) . '</div>';
        }, $text);

        $text = preg_replace_callback('/\{strikethrough\}([\s\S]*?)\{\/strikethrough\}/', function ($matches) use ($escapeHtml) {
            $content = trim($matches[1]);
            return '<span class="strikethrough">' . $escapeHtml($content) . '</span>';
        }, $text);

        // Replace *[bolded text]*
        $text = preg_replace_callback('/\*\[(.*?)\]\*/', function ($matches) use ($escapeHtml) {
            $content = trim($matches[1]);
            return '<b>' . $escapeHtml($content) . '</b>';
        }, $text);

        // Replace {warning-note} blocks
        $text = preg_replace_callback('/\{warning-note\}([\s\S]*?)\{\/warning-note\}/', function ($matches) use ($escapeHtml) {
            $content = trim($matches[1]);
            return '<div class="warning-note">' . $escapeHtml($content) . '</div>';
        }, $text);

        $text = preg_replace_callback('/\{blockquote\}([\s\S]*?)\{\/blockquote\}/', function ($matches) use ($escapeHtml) {
            $content = trim($matches[1]);
            //        return '<div class="blockquote">' . $escapeHtml($content) . '</div>';
            return '<div class="blockquote">' . $content . '</div>';
        }, $text);

        return $text;
    }

    private function autoLink($text)
    {
        $text = preg_replace_callback(
            '/\bhttps?:\/\/[^\s<]+/i',
            function ($matches) {
                $url = $matches[0];
                return '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($url) . '</a>';
            },
            $text
        );

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $text);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//text()[not(ancestor::a)]') as $node) {
            $replaced = preg_replace_callback('/\b([A-Z]+-\d+)\b/', function ($m) {
                $code = $m[1];
                return '<a href="https://viatorinc.atlassian.net/browse/' . $code . '" target="_blank" rel="noopener noreferrer">' . $code .
                    '</a>';
            }, $node->nodeValue);

            if ($replaced !== $node->nodeValue) {
                $frag = $dom->createDocumentFragment();
                $frag->appendXML($replaced);
                $node->parentNode->replaceChild($frag, $node);
            }
        }

        // Extract clean body content only
        $html = $dom->saveHTML();
        $html = preg_replace('~^.*<body>(.*)</body>.*$~s', '$1', $html);

        return ($html);
    }

    private function getNoteDates($noteDate)
    {
        // this should return AU + Etc/GMT+7
        // 這是 Etc/GMT+7 的時間
        $gmt7DateTime = $noteDate;
        $dtGmt7 = new \DateTime($gmt7DateTime);

        // 轉成 Sydney 時間
        $dtSydney = clone $dtGmt7;
        $dtSydney->setTimezone(new \DateTimeZone('Australia/Sydney'));
        $sydeyDate = $dtSydney->format('Y-m-d H:i:s');

        return ['ETC_GMT7' => $noteDate, 'SYD' => $sydeyDate];
    }

    private function sortNotesByDateDesc(array &$tickets)
    {
        foreach ($tickets as &$notes) {
            usort($notes, function ($a, $b) {
                return strtotime($b['date']) <=> strtotime($a['date']);
            });
        }
        unset($notes);
    }

    public function createHtmlNote($note)
    {
        $_note = str_replace('\n', "\n", $note);
        $_note = $this->addSpecificWordBadge($_note);
        $_note = $this->parseCustomBlocks($_note);
        $_note = $this->autoLink($_note);

        $_note = preg_replace('/\\\"/', '"', $_note);
        return '<pre style="margin:0; white-space: pre-wrap;">' . $_note . "</pre>";
    }
}
