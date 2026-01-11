<?php

namespace  NoteReact\Mapping;

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
            self::ADHOC_REPORTS => "http://adhoc-reports.local.viatorsystems.com/",
            self::STINGRAY => "http://stingray.local.viator.com",
            self::STAFF => "http://staff.local.viator.com",
            self::COMPETITIVE_ANALYSIS => "http://competitive-analysis.local.viator.com"
        ];

        return $apacheMapping[$project] ?? "";
    }

    public static function allProjectLinks()
    {
        return [
            self::APP_SUPPORT => [
                "site" => self::projectLinks(self::APP_SUPPORT),
                "kibana" => self::kibanaLinkIds(self::APP_SUPPORT),
                "pipeline" => self::pipeline(self::APP_SUPPORT)
            ],
            self::ADHOC_REPORTS => [
                "site" => self::projectLinks(self::ADHOC_REPORTS),
                "kibana" => self::kibanaLinkIds(self::ADHOC_REPORTS),
                "pipeline" => self::pipeline(self::ADHOC_REPORTS)
            ],

            self::COMPETITIVE_ANALYSIS => [
                "site" => self::projectLinks(self::COMPETITIVE_ANALYSIS),
                "kibana" => self::kibanaLinkIds(self::COMPETITIVE_ANALYSIS),
                "pipeline" => self::pipeline(self::COMPETITIVE_ANALYSIS)
            ],

            self::STAFF => [
                "site" => self::projectLinks(self::STAFF),
                "kibana" => self::kibanaLinkIds(self::STAFF),
                "pipeline" => self::pipeline(self::STAFF)
            ],

            self::STINGRAY => [
                "site" => self::projectLinks(self::STINGRAY),
                "kibana" => self::kibanaLinkIds(self::STINGRAY),
                "pipeline" => self::pipeline(self::STINGRAY)
            ],
        ];
    }

    private static function projectLinks($project, $isViatorSystems = true)
    {
        $envs = ["prod", "rc", "zelda", "int", "docker", "apache"];
        $links = array();

        foreach ($envs as $env) {
            $envKey = ($env == "prod") ? "production" : $env;
            if ($env == "docker") {
                $link = self::localDocker($project);
            } else {
                $link = self::getFixedViatorDomain($project, $env, $isViatorSystems);
            }
            $links[$envKey] = $link;
        }
        return $links;
    }


    private static function getFixedViatorDomain($project, $env, $isViatorSystems)
    {
        $domain = $isViatorSystems ? "viatorsystems.com" : "viator.com";
        if ($env == "prod" && !$isViatorSystems) {
            return "http://$project.$domain";
        }
        if ($env == "apache") {
            return "http://$project.local.$domain";
        } else {
            return "https://$project.$env.$domain";
        }
    }

    private static function kibanaLinkIds($project)
    {
        $kibanaLinkIds = [
            self::APP_SUPPORT => [
                "int" => "/goto/f6e39730-0a86-11ee-95e2-5fea4b6b16dd",
                "rc" => "/app/r/s/0fKSy",
                "production" => "/goto/10382db0-b184-11ed-9db1-9d3bca3e8809"
            ],
            self::ADHOC_REPORTS => [
                "int" => "/app/r/s/D1X8S",
                "rc" => "/app/r/s/iOZcq",
                "production" => "/app/r/s/gby0q"
            ],
            self::STINGRAY => [
                "int" => "/app/r/s/xhNJ1",
                "rc" => "/app/r/s/1xccN",
                "production" => "/app/r/s/VYxQZ"
            ],
            self::STAFF => [
                "int" => "/app/r/s/5fuOM",
                "rc" => "/app/r/s/KiDwN",
                "production" => "/app/r/s/FL3vG"
            ],
            self::COMPETITIVE_ANALYSIS => [
                "int" => "/app/r/s/CDufB",
                "rc" => "/app/r/s/1JPWw",
                "production" => "/app/r/s/3DxNN"
            ]
        ];
        return $kibanaLinkIds[$project];
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
                self::ADHOC_REPORTS => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/gby0q",
                self::STINGRAY => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/VYxQZ",
                self::STAFF => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/FL3vG",
                self::COMPETITIVE_ANALYSIS => "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/3DxNN"
            ]
        ];

        return $kibanaMapping[$env][$project] ?? "";
    }


    public static function pipeline($project)
    {
        $pipelineMapping = [
            self::APP_SUPPORT => "https://gitlab.com/viator/engineering/app-support/app-support.viator.com/-/pipelines",
            self::ADHOC_REPORTS => "https://gitlab.com/viator/engineering/app-support/adhoc-reports.viator.com/-/pipelines",
            self::STINGRAY => "https://gitlab.com/viator/engineering/app-support/stingray.viator.com/-/pipelines",
            self::STAFF => "https://gitlab.com/viator/engineering/app-support/staff.viator.com/-/pipelines",
            self::COMPETITIVE_ANALYSIS => "https://gitlab.com/viator/engineering/app-support/competitive-analysis/-/pipelines",
        ];

        return $pipelineMapping[$project] ?? "";
    }


    public static function localDocker($project)
    {
        return "http://localhost:" . static::appId($project);
    }


    public static function appId($project)
    {
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
