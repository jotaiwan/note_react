<?php
// src/Service/MenuService.php
namespace Note\Service;

use Note\Mapping\UrlMapping;
use Symfony\Component\Validator\Constraints\Json;

use Note\Util\LoggerTrait;

class MenuService
{
    use LoggerTrait;

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

    public static function stockInfo($source)
    {
        $scheme = $_SERVER['REQUEST_SCHEME'] ?? 'http';
        $host   = $_SERVER['HTTP_HOST'];

        $apiUrl = "$scheme://$host/api/stocks/$source/json";

        // Note: make sure the period much match your mock, otherwise it will break everything
        $json = file_get_contents($apiUrl);
        $data = json_decode($json, true);

        return $data;
    }
}
