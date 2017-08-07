<?php

use PHPUnit\Framework\TestCase;

use Aliyun\SDK\SDK;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Aliyun\SDK\Exception\ResponseException;

class SDKTest extends TestCase
{
    public function testCall()
    {
        $sdk = $this->createSDK();

        $result = $sdk->call('cdn', 'DescribeCdnService');
        $this->assertArrayHasKey('InstanceId', $result);

        $result = $sdk->call('domain', 'GetWhoisInfo', [
            'DomainName' => 'aliyun.com',
        ]);
        $this->assertEquals('aliyun.com', $result['DomainName']);
    }

    /**
     * @expectedException Aliyun\SDK\Exception\SDKException
     */
    public function testCall_ErrorServiceName()
    {
        $sdk = $this->createSDK();
        $result = $sdk->call('not_exist_service', 'a_action');
    }

    public function testCall_ErrorAction()
    {
        $sdk = $this->createSDK();
        try {
            $result = $sdk->call('cdn', 'ErrorAction');
        } catch (ResponseException $e) {
            $error = $e->getResponseData();
            $this->assertEquals('InvalidParameter', $error['Code']);
        }
    }

    protected function createSDK()
    {
        $logDir = __DIR__.'/log';
        if (!is_dir($logDir)) {
            mkdir($logDir);
        }

        $logger = new Logger('aliyun');
        $logger->pushHandler(new StreamHandler($logDir.'/aliyun-php-sdk-unittest.log', Logger::INFO));

        $accessKeyId = getenv('ACCESS_KEY_ID');
        $accessSecret = getenv('ACCESS_SECRET');

        $sdk = new SDK($accessKeyId, $accessSecret, $logger, true);

        return $sdk;
    }
}
