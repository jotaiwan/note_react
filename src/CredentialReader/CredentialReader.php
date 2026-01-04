<?php

namespace  NoteReact\CredentialReader;

use Config\NoteConstants;

class CredentialReader
{
    private static function initialize()
    {
        $credentialJson = NoteConstants::getCredentialJson();
        $json = file_get_contents($credentialJson);
        if (!$json) {
            return null;
        }
        return json_decode($json, true);
    }

    public static function getCredential($source, $key)
    {
        $credentials = self::initialize();
        if ($credentials && isset($credentials[$source]) && isset($credentials[$source][$key])) {
            return $credentials[$source][$key];
        }
        return null;
    }

    public static function getTaSso()
    {
        return self::getCredential("ta_sso", "current");
    }

    public static function getFinnhubApiKey()
    {
        return self::getCredential("finnhub", "api_key");
    }

    public  static function getKibanaCloudApiKey()
    {
        return self::getKibanaKey("cloud_api_key");
    }

    public static function getKibanaClusterId()
    {
        return self::getKibanaKey("cluster_id");
    }

    public static function getKibanaKey($key)
    {
        return self::getCredential("kibana", $key);
    }

    public static function getAlpacaMarketsApiKey()
    {
        return self::getCredential("alpacamarkets", "api_key") ?? "";
    }

    public static function getAlpacaMarketsDataUrl()
    {
        return self::getCredential("alpacamarkets", "data_url") ?? "";
    }

    public static function getAlpacaMarketsSecret()
    {
        return self::getCredential("alpacamarkets", "secret") ?? "";
    }
}
