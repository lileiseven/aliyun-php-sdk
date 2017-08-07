# Aliyun PHP SDK

由于阿里云官方的SDK不支持 Composer 安装，对在项目中的集成造成了极大的困难，所以才有了此项目。

## 安装

```bash
$ composer require l1l31/aliyun-php-sdk
```

## 使用

```php
<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Aliyun\SDK\SDK;

$logger = new Logger('aliyun');
$logger->pushHandler(new StreamHandler('/tmp/aliyun-php-sdk-unittest.log', Logger::INFO));

$sdk = new SDK('<ACCESS KEY ID>', '<ACCESS SECRET', $logger);

// 查询域名 aliyun.com 的 Whois 信息
$result = $sdk->call('domain', 'GetWhoisInfo', [
    'DomainName' => 'aliyun.com',
]);
```

## 单元测试

```bash
ACCESS_KEY_ID=<YOUR_KEY_ID> ACCESS_SECRET=<YOUR_SECRET> phpunit 
```
