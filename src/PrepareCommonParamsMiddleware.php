<?php

namespace Aliyun\SDK;

use Psr\Http\Message\RequestInterface;

class PrepareCommonParamsMiddleware
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function __invoke(callable $handler)
    {
        return function ($request, array $options) use ($handler) {
            $request = $this->onBefore($request);
            return $handler($request, $options);
        };
    }

    private function onBefore(RequestInterface $request)
    {
        $params = \GuzzleHttp\Psr7\parse_query($request->getUri()->getQuery());
        $params['Version'] = $this->config['version'];
        $params['Format'] = 'JSON';
        $params['AccessKeyId'] = $this->config['accessKeyId'];
        $params['SignatureMethod'] = 'HMAC-SHA1';
        $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
        $params['SignatureVersion'] = '1.0';
        $params['SignatureNonce'] = uniqid();
        if (isset($this->config['regionId']) && !empty($this->config['regionId'])) {
            $params['RegionId'] = $this->config['regionId'];
        }

        $params['Signature'] = $this->getSignature($request, $params);
        $query = \GuzzleHttp\Psr7\build_query($params);
        $request = $request->withUri($request->getUri()->withQuery($query));
        return $request;
    }

    public function getSignature(RequestInterface $request, array $params)
    {
        //参数排序
        ksort($params);
        $query = http_build_query($params, null, '&', PHP_QUERY_RFC3986);
        $source = $request->getMethod() . '&%2F&' . $this->percentEncode($query);
        return base64_encode(hash_hmac('sha1', $source, $this->config['accessSecret'] . '&', true));
    }

    protected function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
}