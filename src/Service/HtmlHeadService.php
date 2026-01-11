<?php



namespace  NoteReact\Service;

use NoteReact\Util\LoggerTrait;

class HtmlHeadService
{
    use LoggerTrait;

    public function getHeadData(): array
    {

        return [
            "css" => "",
            "js" => ""
        ];
    }

    public static function addCss()
    {

        $cssFile = realpath(__DIR__ . "/../..") . "/public/assets/note/noteBuilder.css";
        $cssContent = file_get_contents($cssFile);


        $css = <<<EOT
            <style>
                $cssContent
            </style>
            EOT;

        return $css . "\n";
    }

    public static function addJs()
    {

        $assets = realpath(__DIR__ . "/../..") . "/public/assets/";

        $jsFileBuilder = $assets . "/note/noteBuilder.js";
        $jsBuilderContent = file_get_contents($jsFileBuilder);

        $jsFileEditable = $assets . "/note/noteEditable.js";
        $jsEditableContent = file_get_contents($jsFileEditable);

        $jsFileStockUpdate = $assets . "/note/stockUpdate.js";
        $jsStockContent = file_get_contents($jsFileStockUpdate);

        $allJs = $jsBuilderContent . "\n" . $jsEditableContent . "\n" . $jsStockContent;


        $allJsContent = str_replace(["\r\n", "\r"], "\n", $allJs);
        $indent = '    ';
        $indentedJsContent = preg_replace("/\n/", "\n" . $indent, $allJsContent);


        $js = <<<EOT
            <script>
                $indentedJsContent
            </script>
            EOT;
        return $js;
    }
}
