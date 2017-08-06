<?php
namespace Aliyun\SDK\Exception;

use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;

class ConnectException extends SDKException
{
    public function __construct(GuzzleConnectException $e)
    {
        parent::__construct($e->getMessage(), $e->getCode());
    }
}