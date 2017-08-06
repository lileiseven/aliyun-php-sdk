<?php

namespace Aliyun\SDK;

class ServiceConfig
{
    protected $config = [
        'cdn' => [
            'entrypoint' => 'http://cdn.aliyuncs.com/',
            'version' => '2014-11-11',
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