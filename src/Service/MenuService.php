<?php
// src/Service/MenuService.php
namespace  NoteReact\Service;

use NoteReact\Mapping\UrlMapping;
use Symfony\Component\Validator\Constraints\Json;
use NoteReact\Service\StockService;

use NoteReact\Util\LoggerTrait;

class MenuService
{
    use LoggerTrait;

    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function getAllEmojis()
    {
        return self::getEmojis(); // Get emoji array from utility class
    }

    private static function getEmojis()
    {
        $emojis = static::getEmojiIconMapper();

        $emojiList = [];
        foreach ($emojis as $label => $info) {
            $emojiList[] = [
                'label' => $label,
                'type' => $info['type'],
                'value' => $info['value']
            ];
        }

        return $emojiList;
    }

    private static function getEmojiIconMapper()
    {
        $code = "<strong>" . htmlspecialchars("</>", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</strong>";
        $redStar = "<span class='red-star-small'>★</span>";
        $redRoundX = "<span class='emoji-round-red-x'>x</span>";
        $strikeThrough = "<span class='strikethrough text-larger'>AU</span>";

        return array(
            // Custom markup replacements
            // $redStar => ["type" => "tag", "value" => $redRoundX],
            // $code => ["type" => "tag", "value" => "{code}\n\n{/code}"],
            // "💬" => ["type" => "entity", "value" => "{blockquote}\n\n{/blockquote}"],

            // $redRoundX => ["type" => "tag", "value" => $redRoundX],
            // $strikeThrough => ["type" => "tag", "value" => "{strikethrough}\n\n{/strikethrough}"],
            // HTML entities
            "👀" => ["type" => "entity", "value" => "👀"],
            "🔍" => ["type" => "entity", "value" => "🔍"],
            "💥" => ["type" => "entity", "value" => "💥"],
            "🔥" => ["type" => "entity", "value" => "🔥"],
            "⚠️" => ["type" => "entity", "value" => "⚠️"],
            "✅" => ["type" => "entity", "value" => "✅"],
            "🚨" => ["type" => "entity", "value" => "🚨"],
            "🛟" => ["type" => "entity", "value" => "🛟"],
            "📅" => ["type" => "entity", "value" => "📅"],
            "🧠" => ["type" => "entity", "value" => "🧠"],
            "🙋" => ["type" => "entity", "value" => "🙋"],
            "😁" => ["type" => "entity", "value" => "😁"],
            "😄" => ["type" => "entity", "value" => "😄"],
            "😂" => ["type" => "entity", "value" => "😂"],
            "🤔" => ["type" => "entity", "value" => "🤔"],
            "😮‍💨" => ["type" => "entity", "value" => "😮‍💨"],
            "😵" => ["type" => "entity", "value" => "😵"],
            "😵‍💫" => ["type" => "entity", "value" => "😵‍💫"],
            "😅" => ["type" => "entity", "value" => "😅"],
            "😥" => ["type" => "entity", "value" => "😥"],
            "😩" => ["type" => "entity", "value" => "😩"],
            "🤪" => ["type" => "entity", "value" => "🤪"],
            "😭" => ["type" => "entity", "value" => "😭"],
            "😤" => ["type" => "entity", "value" => "😤"],
            "😱" => ["type" => "entity", "value" => "😱"],
            "🤯" => ["type" => "entity", "value" => "🤯"],
            "🤨" => ["type" => "entity", "value" => "🤨"],
            "🥵" => ["type" => "entity", "value" => "🥵"],
            "🐞" => ["type" => "entity", "value" => "🐞"],
            "🔴" => ["type" => "entity", "value" => "🔴"],
            "🔺" => ["type" => "entity", "value" => "🔺"],
            "🔻" => ["type" => "entity", "value" => "🔻"],
            "📍" => ["type" => "entity", "value" => "📍"],
            "❗" => ["type" => "entity", "value" => "❗"],
            "❓" => ["type" => "entity", "value" => "❓"],
            "🟠" => ["type" => "entity", "value" => "🟠"],
            "🔸" => ["type" => "entity", "value" => "🔸"],
            "🟡" => ["type" => "entity", "value" => "🟡"],
            "🟢" => ["type" => "entity", "value" => "🟢"],
            "🔹" => ["type" => "entity", "value" => "🔹"],
            "💪" => ["type" => "entity", "value" => "💪"],
            "👍" => ["type" => "entity", "value" => "👍"],
            "👉" => ["type" => "entity", "value" => "👉"],
            "👈" => ["type" => "entity", "value" => "👈"],
            "👇" => ["type" => "entity", "value" => "👇"],
            "👌" => ["type" => "entity", "value" => "👌"],
            "⛔️" => ["type" => "entity", "value" => "⛔️"],
            "🚫" => ["type" => "entity", "value" => "🚫"],
            "💡" => ["type" => "entity", "value" => "💡"],
            "📌" => ["type" => "entity", "value" => "📌"],
            "🧩" => ["type" => "entity", "value" => "🧩"],
            "🎉" => ["type" => "entity", "value" => "🎉"],
            "🙏" => ["type" => "entity", "value" => "🙏"],
            "🎯" => ["type" => "entity", "value" => "🎯"],
            "ℹ️" => ["type" => "entity", "value" => "ℹ️"],
            "🔼" => ["type" => "entity", "value" => "🔼"],
            "➡️" => ["type" => "entity", "value" => "➡️"],
            "⬅️" => ["type" => "entity", "value" => "⬅️"],
            "⬆️" => ["type" => "entity", "value" => "⬆️"],
            "⬇️" => ["type" => "entity", "value" => "⬇️"],
            "↔️" => ["type" => "entity", "value" => "↔️"],
            "↕️" => ["type" => "entity", "value" => "↕️"],
            "⤴️" => ["type" => "entity", "value" => "⤴️"],
            "⤵️" => ["type" => "entity", "value" => "⤵️"],
            "↩️" => ["type" => "entity", "value" => "↩️"],
            "↪️" => ["type" => "entity", "value" => "↪️"],
            "🔁" => ["type" => "entity", "value" => "🔁"],
            "🔄" => ["type" => "entity", "value" => "🔄"],
            "0️⃣" => ["type" => "entity", "value" => "0️⃣"],
            "1️⃣" => ["type" => "entity", "value" => "1️⃣"],
            "2️⃣" => ["type" => "entity", "value" => "2️⃣"],
            "3️⃣" => ["type" => "entity", "value" => "3️⃣"],
            "4️⃣" => ["type" => "entity", "value" => "4️⃣"],
            "5️⃣" => ["type" => "entity", "value" => "5️⃣"],
            "6️⃣" => ["type" => "entity", "value" => "6️⃣"],
            "7️⃣" => ["type" => "entity", "value" => "7️⃣"],
            "8️⃣" => ["type" => "entity", "value" => "8️⃣"],
            "9️⃣" => ["type" => "entity", "value" => "9️⃣"],
            "👥" => ["type" => "entity", "value" => "👥"],
            "🧑‍🤝‍🧑" => ["type" => "entity", "value" => "🧑‍🤝‍🧑"],
            "👨‍👩‍👧‍👦" => ["type" => "entity", "value" => "👨‍👩‍👧‍👦"],
            "👤" => ["type" => "entity", "value" => "👤"],
            "🕰️" => ["type" => "entity", "value" => "🕰️"],
            "🏷️" => ["type" => "entity", "value" => "🏷️"],
            "🔖" => ["type" => "entity", "value" => "🔖"],
            "🧪" => ["type" => "entity", "value" => "🧪"],
            "📎" => ["type" => "entity", "value" => "📎"],
            "🧬" => ["type" => "entity", "value" => "🧬"],
            "⚗️" => ["type" => "entity", "value" => "⚗️"],
            "🔒" => ["type" => "entity", "value" => "🔒"],
            "🔓" => ["type" => "entity", "value" => "🔓"],
            "☑️" => ["type" => "entity", "value" => "☑️"],
            "⬜" => ["type" => "entity", "value" => "⬜"],
            "⚡" =>  ["type" => "entity", "value" => "⚡"],
            "💨" =>  ["type" => "entity", "value" => "💨"],
            "🔑" => ["type" => "entity", "value" => "🔑"],
            "🗝️" => ["type" => "entity", "value" => "🗝️"],
            "🛡️" => ["type" => "entity", "value" => "🛡️"],
            "🕒" => ["type" => "entity", "value" => "🕒"],
            "🌀" => ["type" => "entity", "value" => "🌀"],
            "⏰" => ["type" => "entity", "value" => "⏰"],
            "🔔" => ["type" => "entity", "value" => "🔔"],
            "❌" => ["type" => "entity", "value" => "❌"],
            "🚧" => ["type" => "entity", "value" => "🚧"],
            "👨‍💻" => ["type" => "entity", "value" => "👨‍💻"],
            "👷‍♂️" => ["type" => "entity", "value" => "👷‍♂️"],
            "🏭" => ["type" => "entity", "value" => "🏭"],
            "🏃‍♂️" => ["type" => "entity", "value" => "🏃‍♂️"],
            "🇦🇺" => ["type" => "entity", "value" => "🇦🇺"],
            "🐼" => ["type" => "entity", "value" => "🐼"],
            "🦘" => ["type" => "entity", "value" => "🦘"],
            "🐨" => ["type" => "entity", "value" => "🐨"],
            "🪲" => ["type" => "entity", "value" => "🪲"],
            "🐛" => ["type" => "entity", "value" => "🐛"],
            "🐜" => ["type" => "entity", "value" => "🐜"],
            "🌐" => ["type" => "entity", "value" => "🌐"],
            "🐚" => ["type" => "entity", "value" => "🐚"],

        );
    }


    public function buildAllProjectLinks()
    {
        return UrlMapping::allProjectLinks();
    }

    public function getMenu(): array
    {
        return [
            'app_support_link' => [
                'url' => UrlMapping::localDocker(UrlMapping::APP_SUPPORT),
                'text' => "App Support",
                "popoverLinks" => self::getPopoverContent(UrlMapping::APP_SUPPORT)
            ],
            'adhoc_reports_link' => [
                'url' => UrlMapping::localDocker(UrlMapping::ADHOC_REPORTS),
                'text' => "Adhoc Reports",
                "popoverLinks" => self::getPopoverContent(UrlMapping::ADHOC_REPORTS)
            ],
            'competitive_analysis_link' => [
                'url' => UrlMapping::localDocker(UrlMapping::COMPETITIVE_ANALYSIS),
                'text' => "Competitive-Analysis",
                "popoverLinks" => self::getPopoverContent(UrlMapping::COMPETITIVE_ANALYSIS)
            ],
            'staff_link' => [
                'url' => UrlMapping::localDocker(UrlMapping::STAFF),
                'text' => "Staff",
                "popoverLinks" => self::getPopoverContent(UrlMapping::STAFF)
            ],
            'stingray_link' => [
                'url' => UrlMapping::localDocker(UrlMapping::STINGRAY),
                'text' => "Stingray",
                "popoverLinks" => self::getPopoverContent(UrlMapping::STINGRAY)
            ]
        ];
    }

    public function getClipboard()
    {
        return [
            ["text" => "Container adhoc-reports folder", "copy" => "cd /var/www/html/adhoc-reports", "order" => 1],
            ["text" => "Container app-support folder", "copy" => "cd /var/www/html/app-support", "order" => 2],
            ["text" => "Container competitive-analysis folder", "copy" => "cd /var/www/html/competitive-analysis", "order" => 3],
            ["text" => "Container staff folder", "copy" => "cd /var/www/html/staff", "order" => 4],
            ["text" => "Container stingray folder", "copy" => "cd /var/www/html/stingray", "order" => 5],
            ["text" => "GDPR Assume Role", "copy" => "aws sts assume-role --role-arn arn =>aws =>iam => =>347924498361 =>role/vi-dev-gdpr-data-removal-backup-readwrite --role-session-name s3-list-test", "order" => 6],
            ["text" => "Show folder and files", "copy" => "ls -lah --group-directories-first", "order" => 7],
            ["text" => "Submodule update command", "copy" => "git submodule update --remote --recursive", "order" => 8],
        ];
    }


    public static function getProjectMenuAndLinks($project): string
    {
        $popoverContent = self::getPopoverContent($project);
        $displayProject = ucfirst($project);        //ucwords($project, "-");
        $link = UrlMapping::localDocker($project);
        return <<<HTML
            <a class="popover-link" href="{$link}" id="menu-{$project}" target="_blank" rel="noopener noreferrer"
                data-toggle="popover"
                data-html="true"
                data-placement="bottom"
                data-trigger="hover focus"
                data-content="{$popoverContent}">
            <span class="ml-2 small mr-1">{$displayProject}</span>
            </a>
        HTML;
    }

    public static function getPopoverContent($project)
    {
        return [
            ['url' => $url = UrlMapping::apache($project), 'text' => "Apache"],
            ['url' => $url = UrlMapping::kibana($project), 'text' => "Kibana"],
            ['url' => $url = UrlMapping::pipeline($project), 'text' => "Pipeline"],
        ];
    }

    public static function getNoteStatuses()
    {
        return array(
            "All" => array("url" => "?statusOnly=", "icon" => "fa-list fa-fw", "color" => "blue", "text" => "All"),
            "Epic" => array("url" => "?statusOnly=epic", "icon" => "fa-bolt fa-fw", "color" => "purple", "text" => "Epic"),
            "Open" => array("url" => "?statusOnly=open", "icon" => "fa-hourglass-half fa-fw", "color" => "blue", "text" => "Open"),
            "Processing" => array("url" => "?statusOnly=processing", "icon" => "fa-spinner fa-spin fa-fw", "color" => "darkcyan", "text" => "Processing"),
            "Follow" => array("url" => "?statusOnly=follow", "icon" => "fa-eye fa-fw", "color" => "indigo", "text" => "Follow"),
            "Resolved" => array("url" => "?statusOnly=resolved", "icon" => "fa-check-circle fa-fw", "color" => "green", "text" => "Resolved"),
            "Unresolved" => array("url" => "?statusOnly=unresolved", "icon" => "fa-minus-circle fa-fw", "color" => "red", "text" => "Unresolved"),
            "Meeting" => array("url" => "?statusOnly=meeting", "icon" => "fa-users fa-fw", "color" => "Salmon", "text" => "Meeting"),
            "NoteOnly" => array("url" => "?statusOnly=noteonly", "icon" => "fa-sticky-note fa-fw", "color" => "grey", "text" => "NoteOnly")
        );
    }

    public static function getCopyCommandList()
    {
        return $array = array(
            "adhoc_reports" => array(
                "copy_value" => "cd /var/www/html/adhoc-reports",
                "text" => "Container adhoc-reports folder"
            ),
            "app_support" => array(
                "copy_value" => "cd /var/www/html/app-support",
                "text" => "Container app-support folder"
            ),
            "competitive_analysis" => array(
                "copy_value" => "cd /var/www/html/competitive-analysis",
                "text" => "Container competitive-analysis folder"
            ),
            "staff" => array(
                "copy_value" => "cd /var/www/html/staff",
                "text" => "Container staff folder"
            ),
            "stingray" => array(
                "copy_value" => "cd /var/www/html/stingray",
                "text" => "Container stingray folder"
            ),
            "gdpr_assume_role" => array(
                "copy_value" => "aws sts assume-role --role-arn arn:aws:iam::347924498361:role/vi-dev-gdpr-data-removal-backup-readwrite --role-session-name s3-list-test",
                "text" => "GDPR Assume Role"
            ),
            "show_folder_files" => array(
                "copy_value" => "ls -lah --group-directories-first",
                "text" => "Show folder and files"
            ),
            "submodule_update" => array(
                "copy_value" => "git submodule update --remote --recursive",
                "text" => "Submodule update command"
            )
        );
    }

    public function stockInfo(string $symbol): array
    {
        // Get stock info directly via StockService to avoid calling the app over HTTP
        try {
            // Prefer Alpaca Markets; StockService returns structured array
            $stockData = $this->stockService->getAlpacaMarketsStockPrice(StockService::STOCK_ALPAC_MARKETS, $symbol, 'json');

            if (empty($stockData)) {
                // fall back to Finnhub
                $stockData = $this->stockService->getFinnhubStockPrice(StockService::STOCK_FINNHUB, $symbol, 'json');
            }

            return $stockData ?? [];
        } catch (\Exception $e) {
            $this->error('Failed to fetch stock info: ' . $e->getMessage());
            return [];
        }
    }
}
