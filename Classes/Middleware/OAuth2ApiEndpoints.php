<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Middleware;

use FGTCLB\OAuth2Server\Server\ServerFactory;
use FGTCLB\OAuth2Server\Service\OAuthApiEndpointRegistry;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;

/**
 * Handler for OAuth2 identity requests
 *
 * @see https://oauth2.thephpleague.com/resource-server/securing-your-api/
 */
final class OAuth2ApiEndpoints implements MiddlewareInterface
{
    private OAuthApiEndpointRegistry $registry;

    public function __construct(OAuthApiEndpointRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $apiEndpoint = $this->registry->getEndpointByPath($request->getUri()->getPath());

        if ($apiEndpoint) {
            if (!$apiEndpoint->isPublic() && !$request->getAttribute('frontend.user')->user) {
                $factory = new ServerFactory();
                $server = $factory->buildResourceServer();

                try {
                    $request = $server->validateAuthenticatedRequest($request);
                } catch (OAuthServerException $e) {
                    return $e->generateHttpResponse(new Response());
                }
            }

            return $apiEndpoint->handle($request);
        }

        return $handler->handle($request);
    }
}
