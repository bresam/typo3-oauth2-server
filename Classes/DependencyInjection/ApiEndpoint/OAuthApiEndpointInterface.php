<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\DependencyInjection\ApiEndpoint;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface OAuthApiEndpointInterface
{
    public function isPublic(): bool;
    public function getScope(): string;
    public function canHandlePath(string $path): bool;
    public function handle(RequestInterface $request): ResponseInterface;
}