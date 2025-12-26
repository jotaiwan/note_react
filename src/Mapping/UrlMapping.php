<?php

namespace Note\Mapping;

class UrlMapping
{
    const APP_SUPPORT = "app-support";
    const ADHOC_REPORTS = "adhoc-reports";
    const STINGRAY = "stingray";
    const STAFF = "staff";
    const COMPETITIVE_ANALYSIS = "competitive-analysis";

    public static function apache(string $project): string
    {
        $apacheMapping = [
            self::APP_SUPPORT => "http://app-support.local.viatorsystems.com/",
            self::ADHOC_REPORTS => "'http://adhoc-reports.local.viatorsystems.com/",
            self::STINGRAY => "http://stingray.local.viator.com",
            self::STAFF => "http://staff.local.viator.com",
            self::COMPETITIVE_ANALYSIS => "http://competitive-analysis.local.viator.com"
        ];

        return $apacheMapping[$project] ?? "";
    }

    public static function kibana(string $project, string $env = "int"): string
    {
        $env = $env == 'local' ? 'int' : $env;
        $kibanaMapping = [
            "int" => [
                self::APP_SUPPORT => "https://kibana.prod.viatorsystems.com/s/application-support/goto/f6e39730-0a86-11ee-95e2-5fea4b6b16dd",
//                self::ADHOC_REPORTS => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/5oZV9",   // old utility
                self::ADHOC_REPORTS => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/D1X8S",
                self::STINGRAY => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/xhNJ1",
                self::STAFF => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/5fuOM",
                self::COMPETITIVE_ANALYSIS => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/CDufB"
            ],
            "rc" => [
                self::APP_SUPPORT => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/0fKSy",
//                self::ADHOC_REPORTS => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/vBzBH",   // old utility
                self::ADHOC_REPORTS => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/iOZcq",
                self::STINGRAY => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/1xccN",
                self::STAFF => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/KiDwN",
                self::COMPETITIVE_ANALYSIS => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/1JPWw"
            ],
            "prod" => [
                self::APP_SUPPORT => "https://kibana.prod.viatorsystems.com/s/application-support/goto/10382db0-b184-11ed-9db1-9d3bca3e8809",
//                self::ADHOC_REPORTS => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/FEDPB",   // old utility
                self::ADHOC_REPORTS => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/gby0q",
                self::STINGRAY => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/VYxQZ",
                self::STAFF => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/FL3vG",
                self::COMPETITIVE_ANALYSIS => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/3DxNN"
            ]
        ];

        return $kibanaMapping[$env][$project] ?? "";
    }


    public static function pipeline($project) {
        $pipelineMapping = [
            self::APP_SUPPORT => "https://gitlab.com/viator/engineering/app-support/app-support.viator.com/-/pipelines",
            self::ADHOC_REPORTS => "https://gitlab.com/viator/engineering/app-support/adhoc-reports.viator.com/-/pipelines",
            self::STINGRAY => "https://gitlab.com/viator/engineering/app-support/stingray.viator.com/-/pipelines",
            self::STAFF => "https://gitlab.com/viator/engineering/app-support/staff.viator.com/-/pipelines",
            self::COMPETITIVE_ANALYSIS => "https://gitlab.com/viator/engineering/app-support/competitive-analysis/-/pipelines",
        ];

        return $pipelineMapping[$project] ?? "";
    }


    public static function localDocker($project) {
        return "http://localhost:" . static::appId($project);
    }


    public static function appId($project) {
        $appIdMapping = [
            self::APP_SUPPORT => 8178,
            self::ADHOC_REPORTS => 8177,
            self::STINGRAY => 8176,
            self::STAFF => 8179,
            self::COMPETITIVE_ANALYSIS => 8215
        ];

        return $appIdMapping[$project] ?? "";
    }

}