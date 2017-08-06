<?php

namespace Aliyun\SDK\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\BadResponseException;

class ResponseException extends SDKException
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    protected $responseData;


    public function __construct(BadResponseException $e)
    {
        $this->request = $e->getRequest();
        $this->response = $e->getResponse();
        $this->responseData = \GuzzleHttp\json_decode((string)$this->response->getBody(), true);

        parent::__construct($e->getMessage(), $e->getCode());
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getRawResponse()
    {
        return (string) $this->response->getBody();
    }

    public function getResponseData()
    {
        return $this->responseData;
    }
}