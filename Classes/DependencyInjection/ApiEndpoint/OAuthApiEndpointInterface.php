<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\DependencyInjection\ApiEndpoint;

use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;

interface OAuthApiEndpointInterface
{
    /**
     * Devines if the endpoint is public/without a user accessible
     */
    public function isPublic(): bool;

    /**
     * Devines the required scope to access the endpoint, '*' for any/none
     */
    public function getScope(): string;

    /**
     * Exact path this endpoint can handle e.g. '/api-endpoint'
     *
     * TODO: Use regex notation instead of exact string
     */
    public function canHandlePath(string $path): bool;

    /**
     * Process the request
     */
    public function handle(ServerRequest $request): Response;
}