<?php

namespace Aliyun\SDK;

use Aliyun\SDK\Exception\SDKException;

class ServiceConfig
{
    protected $config = [
        'cdn' => [
            'entrypoint' => 'http://cdn.aliyuncs.com/',
            'version' => '2014-11-11',
        ],
        'domain' => [
            'entrypoint' => 'http://domain.aliyuncs.com/',
            'version' => '2016-05-11',
        ]
    ];

    public function get($service)
    {
        if (!isset($this->config[$service])) {
            throw new SDKException("Service {$service} config is not exist.");
        }

        return $this->config[$service];
    }

    public function all()
    {
        return $this->config;
    }
}