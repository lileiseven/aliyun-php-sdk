<?php

namespace Aliyun\SDK;

use Aliyun\SDK\Exception\SDKException;
use Aliyun\SDK\Exception\ConnectException;
use Aliyun\SDK\Exception\ResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use Psr\Log\LoggerInterface;

class SDK
{
    private $accessKeyId;

    private $accessSecret;

    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    private $debug;

    /**
     * @var ServiceConfig
     */
    private $config;

    private $clients;

    /**
     * SDK 构造函数
     *
     * @param string          $accessKeyId  阿里云 AccessKeyId
     * @param string          $accessSecret 阿里云 AccessSecret
     * @param LoggerInterface $logger       日志对象
     * @param bool            $debug        调试模式，默认关闭；开启的情况下，日志中会记录详细的请求及响应的数据
     */
    public function __construct($accessKeyId, $accessSecret, LoggerInterface $logger = null, $debug = false)
    {
        $this->accessKeyId = $accessKeyId;
        $this->accessSecret = $accessSecret;
        $this->logger = $logger;
        $this->debug = $debug;

        if ($debug && !$logger) {
            throw new SDKException('In debug mode, $logger can not null.');
        }

        $this->config = new ServiceConfig();
    }

    /**
     * 请求阿里云 API 接口
     *
     * @param string $service 服务名
     * @param array  $params  参数
     *
     * @return array API 请求结果
     */
    public function call($service, $action, $params = [])
    {
        $client = $this->createClient($service);

        try {
            $response = $client->get('/', [
                'query' => array_merge(['Action' => $action], $params),
            ]);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            throw new ConnectException($e);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            throw new ResponseException($e);
        } catch (\Exception $e) {
            throw new SDKException($e->getMessage(), $e->getCode(), $e);
        }

        return \GuzzleHttp\json_decode((string) $response->getBody(), true);
    }

    protected function createClient($service)
    {
        if (isset($this->clients[$service])) {
            return $this->clients[$service];
        }

        $config = $this->config->get($service);

        $stack = new HandlerStack(\GuzzleHttp\choose_handler());

        $stack->push(new PrepareCommonParamsMiddleware(array_merge($config, [
            'accessKeyId' => $this->accessKeyId,
            'accessSecret' => $this->accessSecret,
        ])));

        if ($this->logger) {
            if ($this->debug) {
                $formatter = new MessageFormatter(MessageFormatter::DEBUG);
            } else {
                $formatter = new MessageFormatter();
            }
            $stack->push(Middleware::log($this->logger, $formatter));
        }

        $stack->push(Middleware::httpErrors(), 'http_errors');
        $stack->push(Middleware::prepareBody(), 'prepare_body');

        $client = new Client([
            'base_uri' => $config['entrypoint'],
            'handler' => $stack,
        ]);

        $this->clients[$service] = $client;

        return $client;
    }
}
