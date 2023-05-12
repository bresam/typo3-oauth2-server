<?php declare(strict_types = 1);
namespace FGTCLB\OAuth2Server\Middleware;

use FGTCLB\OAuth2Server\Server\ServerFactory;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\Response;

/**
 * Handler for OAuth2 access token requests
 *
 * @see https://oauth2.thephpleague.com/authorization-server/auth-code-grant/#part-two
 */
final class OAuth2AccessToken implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() !== '/oauth/token') {
            return $handler->handle($request);
        }

        $factory = new ServerFactory();
        $server = $factory->buildAuthorizationServer();

        try {
            $this->logger->warning('AccessTokenRequest', [
                'attributes' => $request->getAttributes(),
                'uri' => $request->getUri(),
                'cookies' => $request->getCookieParams(),
                'body' => $request->getBody(),
                'query' => $request->getQueryParams(),
                'headers' => $request->getHeaders(),
            ]);

            return $server->respondToAccessTokenRequest($request, new Response());
        } catch (OAuthServerException $exception) {
            $this->logger->error('AccessTokenException', [
                'msg' => $exception->getMessage(),
                'attributes' => $request->getAttributes(),
                'uri' => $request->getUri(),
                'cookies' => $request->getCookieParams(),
                'body' => $request->getBody(),
                'query' => $request->getQueryParams(),
                'headers' => $request->getHeaders(),
            ]);

            return $exception->generateHttpResponse(new Response());
        }
    }
}
