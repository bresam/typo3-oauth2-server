<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Middleware;

use FGTCLB\OAuth2Server\Server\ServerFactory;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handler for OAuth2 identity requests
 *
 * @see https://oauth2.thephpleague.com/resource-server/securing-your-api/
 */
final class OAuth2Identity implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() === '/oauth/identity') {
            $factory = GeneralUtility::makeInstance(ServerFactory::class);
            $server = $factory->buildResourceServer();

            try {
                $request = $server->validateAuthenticatedRequest($request);
            } catch (OAuthServerException $e) {
                return $e->generateHttpResponse(new Response());
            }

            return $this->generateIdentityResponse($request);
        }

        return $handler->handle($request);
    }

    private function generateIdentityResponse(RequestInterface $request): JsonResponse
    {
        $userId = $request->getAttribute('oauth_user_id');

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('fe_users')
            ->createQueryBuilder();

        $userData = $qb
            ->select('uid', 'username', 'name', 'first_name', 'middle_name', 'last_name', 'email')
            ->from('fe_users')
            ->where($qb->expr()->eq('uid', $qb->createNamedParameter($userId)))
            ->executeQuery()
            ->fetchAssociative();

        return new JsonResponse($userData);
    }
}
