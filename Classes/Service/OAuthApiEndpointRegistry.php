<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\Service;

use FGTCLB\OAuth2Server\DependencyInjection\ApiEndpoint\OAuthApiEndpointInterface;
use Psr\Container\ContainerInterface;

class OAuthApiEndpointRegistry
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /** @var OAuthApiEndpointInterface[] */
    private array $apiEndpoints = [];

    public function addApiEndpoint(string $apiEndpointServiceId): void
    {
        $this->apiEndpoints[] = $this->container->get($apiEndpointServiceId);
    }

    public function getEndpointByPath(string $path): ?OAuthApiEndpointInterface
    {
        foreach ($this->apiEndpoints as $apiEndpoint) {
            if ($apiEndpoint->canHandlePath($path)) {
                return $apiEndpoint;
            }
        }

        return null;
    }
}