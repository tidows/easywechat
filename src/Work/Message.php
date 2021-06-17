<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Kernel\Traits\HasAttributes;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ServerRequestInterface;

class Message
{
    use HasAttributes;

    public function __construct(
        array $attributes = [],
        protected ?string $originContent = ''
    ) {
        $this->attributes = $attributes;
    }

    public function getOriginalContents(): ?string
    {
        return $this->originContent;
    }

    #[Pure]
    public function __toString()
    {
        return $this->getOriginalContents();
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException|\EasyWeChat\Kernel\Exceptions\RuntimeException|\EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public static function createFromRequest(ServerRequestInterface $request, ?Encryptor $encryptor = null): static
    {
        $originContent = strval($request->getBody());

        if (0 === stripos($originContent, '<')) {
            $attributes = XML::parse($originContent);
        }

        if (empty($attributes)) {
            throw new BadRequestException('Failed to decode request contents.');
        }

        $query = $request->getQueryParams();

        if (isset($query['msg_signature']) && 'aes' === ($query['encrypt_type'] ?? '') && $ciphertext = $attributes['Encrypt'] ?? null) {
            if (!$encryptor) {
                throw new InvalidArgumentException('$encryptor could not be empty in safety mode.');
            }
            $attributes = XML::parse($encryptor->decrypt($ciphertext, $query['msg_signature'], $query['nonce'], $query['timestamp']));
        }

        return new static($attributes, $originContent);
    }
}
