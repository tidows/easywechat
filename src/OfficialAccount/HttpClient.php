<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Support\UserAgent;
use EasyWeChat\Kernel\Traits\AccessTokenHttpClientDecorator;
use EasyWeChat\OfficialAccount\Contracts\AccessToken as AccessTokenInterface;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class HttpClient implements \EasyWeChat\OfficialAccount\Contracts\HttpClient
{
    use AccessTokenHttpClientDecorator;

    protected array $defaultOptions = [
        'base_uri' => 'https://api.weixin.qq.com/',
    ];
}
