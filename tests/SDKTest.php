<?php

use PHPUnit\Framework\TestCase;

use Aliyun\SDK\SDK;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SDKTest extends TestCase
{
    public function testCall()
    {
        $logger = new Logger('name');
        $logger->pushHandler(new StreamHandler('/tmp/guzzlehttp.log', Logger::INFO));

        $accessKeyId = getenv('ACCESS_KEY_ID');
        $accessSecret = getenv('ACCESS_SECRET');

        $sdk = new SDK($accessKeyId, $accessSecret, $logger, true);

        $result = $sdk->call('cdn', 'DescribeCdnService');

        // $result = $sdk->call('cdn', 'DescribeUserDomains', ['PageSize' => '3']);

        // $result = $sdk->call('cdn', 'OpenCdnService', ['InternetChargeType' => 'PayByTraffic']);

        var_dump($result);
    }
}