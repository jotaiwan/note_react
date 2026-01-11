<?php

namespace  NoteReact\Service;

use Config\NoteConstants;
use NoteReact\CredentialReader\CredentialReader;
use NoteReact\Util\ProjectPaths;
use Symfony\Component\Validator\Constraints\Json;

use Psr\Log\LoggerInterface;
use NoteReact\Util\LoggerTrait;


class StockService
{

    use LoggerTrait;

    public const RISE_OR_DROP_KEY = 'rise_or_drop';
    public const DAILY_HIGHEST_KEY = 'daily_highest';
    public const EARLIST_OPEN_DATE = "earliest_open_days";
    public const DAILY_HIGHEST_TIMESTAMP = 'daily_highest_timestamp';
    public const DAILY_HIGHEST_SYDNEY_TIME = 'daily_highest_sydney_time';

    public const STOCK_FINNHUB = "Finnhub";
    public const STOCK_ALPAC_MARKETS = "Alpacamarkets";

    private const FINNHUB_API_URL = 'https://finnhub.io/api/v1/quote';
    private const FINNHUB_STOCK_CANDLE_API_URL = 'https://finnhub.io/api/v1/stock/candle';

    private const STOCKDATA_ORG_API_URL = 'https://api.stockdata.org/v1/data/quote';

    private const FINNHUB_JSON_FILE = "stock_funnhub.json";

    private const LOAD_FROM_PAST_SAVED = "loded_from_past_saved";

    private const SOURCE = "source";

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }


    public function getFinnhubStockPrice($source, $symbol, $format)
    {
        // Note: no $period because it returns single record
        $quote = self::getFinnhubQuoteStockPrice($symbol);
        $isLoadFromPastSaved = self::isLoadFromPastSaved($quote);

        // Convert to DateTime object in UTC
        $date = new \DateTime("@" . $quote['t']);
        $date->setTimezone(new \DateTimeZone('UTC'));

        $timeSeries = self::convertToTimeSeries(
            $quote['o'],
            $quote['h'],
            $quote['l'],
            $quote['c'],
            $date->format('Y-m-d H:i:s')
        );
        return $this->returnApiResult($source, $symbol, $timeSeries, $format, $isLoadFromPastSaved);
    }

    public static function getFinnhubQuoteStockPrice($symbol)
    {
        // Note: make mock true to use mock data, set false to use real api
        $apiUrl = "https://finnhub.io/api/v1/quote?symbol={$symbol}&token=" . CredentialReader::getFinnhubApiKey();

        $content = file_get_contents($apiUrl);

        $loadFromPastSaved = false;
        if (!empty($content) && !is_null($content)) {
            error_log("[OK] Finnhub new response: $content");
            self::saveLatestDataToFile(self::FINNHUB_JSON_FILE, $content);
        } else {
            error_log("[FAIL] Couldn't fetch response from Finnhub, use mock data instead.");
            // load the most recently saved data string from a file in the past.
            $content = file_get_contents(self::FINNHUB_JSON_FILE);
            $content = json_decode($content, true);
            $loadFromPastSaved = true;
        }

        $content = json_decode($content, true);

        $content[self::LOAD_FROM_PAST_SAVED] = $loadFromPastSaved;

        return $content;
    }

    public function getAlpacaMarketsStockPrice($source, $symbol, $format)
    {
        // prepare curl header
        $headers = [
            "APCA-API-KEY-ID: " . CredentialReader::getAlpacaMarketsApiKey(),
            "APCA-API-SECRET-KEY: " . CredentialReader::getAlpacaMarketsSecret()
        ];

        // Note:
        // Here is a bit confusing about timezone, the php default timezone is 'Etc/GMT+7' which is USA western timezone
        // However, Alpaca Markets server is in Eastern Time (ET)
        // In the case, retrieve ET datetime and get the date is priority
        $etDatetime = new \DateTime('now', new \DateTimeZone('America/New_York'));

        // setup the start and end time for the result
        // example of actual link: "https://data.alpaca.markets/v2/stocks/TRIP/bars?timeframe=1Day&start=2025-12-19T00:00:00Z&end=2025-12-25T01:12:18Z"
        $endDateTime = $etDatetime->format('Y-m-d\TH:i:s\Z');

        $xDaysAgoDateTime = clone $etDatetime; // clone so we don't modify the original
        $xDaysAgoDateTime->modify('-5 days');
        $startDateTime = $xDaysAgoDateTime->format('Y-m-d\TH:i:s\Z');

        $urlPeriod = "start=$startDateTime&end=$endDateTime";
        $barsUrl = CredentialReader::getAlpacaMarketsDataUrl() . "/stocks/$symbol/bars?timeframe=1Day&$urlPeriod";
        $barsData = $this->getStockApiResult($barsUrl, $headers);

        $latestBar = null;
        foreach ($barsData['bars'] as $bar) {
            if ($latestBar === null || strtotime($bar['t']) > strtotime($latestBar['t'])) {
                $latestBar = $bar;
            }
        }

        $latestDate = str_replace(['T', 'Z'], [' ', ''], $latestBar['t']);   // from 2025-12-24T05:00:00Z to 2025-12-24 05:00:00
        $latestStockPrice = [
            "symbol" => $symbol,                                // 股票代號 / Stock symbol
            "today_open" => $latestBar['o'] ?? null,             // 今天開盤價 / Today's open price
            "today_high" => $latestBar['h'] ?? null,             // 今天最高價 / Today's high price
            "today_low"  => $latestBar['l'] ?? null,             // 今天最低價 / Today's low price
            "today_close" => $latestBar['c'] ?? null,            // 今天最後成交價 / 收盤價 / Today's last trade / close price
            "today_volume" => $latestBar['v'] ?? null,           // 今天累計成交量 / Today's cumulative volume
        ];

        $timeSeries = $this->convertToTimeSeries(
            $latestStockPrice["today_open"],
            $latestStockPrice["today_high"],
            $latestStockPrice["today_low"],
            $latestStockPrice["today_close"],
            $latestDate
        );

        $this->info("Calling Alpaca Marketing `$barsUrl` for result: " . json_encode($timeSeries));

        return $this->returnApiResult($source, $symbol, $timeSeries, $format, false);
    }

    public function getStockApiResult($url, $headers, $methos = "GET")
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // for debugging
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->error('Curl error: ' . curl_error($ch));
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->info(
            sprintf(
                "Curl Request: %s | HTTP Code: %d | Response: %s",
                $url,
                $httpcode,
                $response ?: 'EMPTY'
            )
        );

        return json_decode($response, true);
    }

    private static function convertToTimeSeries($open, $high, $low, $close, $timestamp)
    {
        $timeSeries = [];
        $timeSeries[$timestamp] = [
            '1. open'  => $open,
            '2. high'  => $high,
            '3. low'   => $low,
            '4. close' => $close,
        ];
        return $timeSeries;
    }

    private function returnApiResult($source, $symbol, $timeSeries, $format, $loadFromPastSaved = false)
    {
        // echo json_encode($timeSeries);
        // exit();
        $this->info("✓✓✓ Preparing the reponse of `$source` to controller: " . json_encode($timeSeries));

        $riseOrDrop = self::getStockRiseOrDrop($timeSeries);
        $dailyHighestTimestamp = self::getDailyHighestTimestamp($timeSeries);
        $dailyHighestSydney = self::getDifferentTimestamp($dailyHighestTimestamp, 'UTC', 'Australia/Sydney');

        $stockJson = array(
            self::RISE_OR_DROP_KEY => $riseOrDrop,
            self::DAILY_HIGHEST_TIMESTAMP => $dailyHighestTimestamp,
            self::DAILY_HIGHEST_SYDNEY_TIME => $dailyHighestSydney,
            self::EARLIST_OPEN_DATE => self::getDaysAgoFromUtc($timeSeries),
            self::LOAD_FROM_PAST_SAVED => $loadFromPastSaved,
            self::SOURCE => $source
        );

        error_log(json_encode($stockJson));

        return $stockJson;
    }

    private static function getStockRiseOrDrop($timeSeries)
    {
        $timestamps = array_keys($timeSeries);
        sort($timestamps); // ascending

        $firstTimestamp = reset($timestamps);      // first of the day
        $latestTimestamp = end($timestamps);       // latest

        $openingPrice = (float)$timeSeries[$firstTimestamp]['1. open'];
        $latestClose = (float)$timeSeries[$latestTimestamp]['4. close'];
        $dailyHigh = max(array_column($timeSeries, '2. high'));

        // Optional emoji for latest vs opening
        $trendEmoji = ($latestClose > $openingPrice) ? '▲' : (($latestClose < $openingPrice) ? '▼' : '●');

        return [
            'daily_highest' => self::getNumberTwoDecimal($dailyHigh),
            'opening_price' => self::getNumberTwoDecimal($openingPrice),
            'latest_close' => self::getNumberTwoDecimal($latestClose),
            'trend' => $trendEmoji,
        ];
    }

    private static function getTimeSeriesPeriod($json)
    {
        $timeSeriesKey = "Time Series (Daily)";
        if (empty($json) || !isset($json[$timeSeriesKey])) {
            return null;
        }

        return $json[$timeSeriesKey];
    }

    private static function getRiseOrDropResult($riseOrDrop, $latestClose, $previousClose, $openingPrice)
    {
        return array(
            'status' => $riseOrDrop,
            'latest_close' => $latestClose,
            'previous_close' => $previousClose,
            'opening_price' => $openingPrice
        );
    }

    private static function isLoadFromPastSaved($data)
    {
        return isset($data[self::LOAD_FROM_PAST_SAVED]) && $data[self::LOAD_FROM_PAST_SAVED] === true;
    }

    private static function getDailyHighestTimestamp($timeSeries)
    {
        $dailyHigh = null;
        $dailyHighTimestamp = null;
        foreach ($timeSeries as $timestamp => $data) {
            $high = (float)$data['2. high'];

            if ($dailyHigh === null || $high > $dailyHigh) {
                $dailyHigh = $high;
                $dailyHighTimestamp = $timestamp;
            }
        }
        return $dailyHighTimestamp;
    }

    private static function getNumberTwoDecimal($number)
    {
        return number_format((float)$number, 2, '.', '');
    }

    private static function getDifferentTimestamp($timestamp, $sourceTimezone, $targetTimezone)
    {
        // Create DateTime as UTC
        $dt = self::getTimeStamp($timestamp, $sourceTimezone);
        // Convert to Sydney time
        $dt->setTimezone(new \DateTimeZone($targetTimezone));
        return $dt->format("Y-m-d H:i:s");
    }

    private static function getDaysAgoFromUtc(array $timeSeries, string $timezone = 'Australia/Sydney'): int
    {
        $timestamps = array_keys($timeSeries);
        sort($timestamps);

        $earliestTimestamp = $timestamps[0]; // string "2025-11-14 17:37:00"


        // Alpha Vantage intraday uses US/Eastern per metadata; use Olson name for compatibility
        $usEastern = self::getTimeStamp($earliestTimestamp, 'America/New_York');

        $sydney = clone $usEastern;
        $sydney->setTimezone(new \DateTimeZone('Australia/Sydney'));

        $nowSydney = self::getTimeStamp("now");
        $interval = $sydney->diff($nowSydney);

        $daysAgo = $interval->days;

        return -$daysAgo;
    }

    private static function getTimeStamp($timeStamp, $timezone = 'Australia/Sydney')
    {
        return new \DateTime($timeStamp, new \DateTimeZone($timezone));
    }

    private static function getFullPathDataFile($file)
    {
        return ProjectPaths::dataDir() . "/" . $file;
    }
    // {
    //     "c": 14.00,  // Current price: $14.00 (当前价格)
    //     "d": -0.18,  // Change: Price has decreased by $0.18 (变化：比前一个收盘价下降了 0.18<)
    //     "dp": -1.2694,  // Percentage change: -1.27% (百分比变化：下降了 1.27%)
    //     "h": 14.34,  // High: The highest price today was $14.34 (最高价：14.34)
    //     "l": 13.92,  // Low: The lowest price today was $13.92 (最低价：13.92)
    //     "o": 14.16,  // Open: The price when the market opened was $14.16 (开盘价：14.16)
    //     "pc": 14.18,  // Previous close: Last closing price was $14.18 (前一个收盘价：14.18)
    //     "t": 1766523600  // Timestamp: The time when the data was recorded (时间)
    // }
    private static function getFinnhubKeyDescription()
    {
        return [
            'c'  => 'Current price',            // Latest price (当前价格)
            'd'  => 'Change',                   // Difference between current price and previous close (变化：比前一个收盘价上升或下降)
            'dp' => 'Percent change',           // Percentage change from previous close (百分比变化)
            'h'  => 'High price of the day',    // Today\'s high (最高价)
            'l'  => 'Low price of the day',     // Today\'s low(最低价)
            'o'  => 'Open price of the day',    // Today\'s open (开盘价)
            'pc' => 'Previous close price',     // Closing price from previous trading day (前一个收盘价)
            't'  => 'Timestamp',                // UNIX timestamp of the current price (时间)
        ];
    }

    private static function getFinnhubContent(string $content)
    {
        $renameMap = self::getFinnhubKeyDescription();
        $json = array();
        foreach (json_decode($content, true) as $key => $value) {
            $json[$renameMap[$key]] = $value;
        }
        return $json;
    }

    private static function saveLatestDataToFile($file, $dataString)
    {
        $fullPathFile = self::getFullPathDataFile($file);
        file_put_contents($fullPathFile, $dataString);
    }

    private static function loadJsonStringFromFile($file)
    {
        $fullPathFile = self::getFullPathDataFile($file);
        return file_get_contents($fullPathFile);
    }

    private static function getSymbolFirstCharUppercase($symbol)
    {
        return strtoupper(substr($symbol, 0, 1));
    }
}
