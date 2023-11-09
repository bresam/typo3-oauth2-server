<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\Middleware;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use FGTCLB\OAuth2Server\Configuration;
use FGTCLB\OAuth2Server\Domain\Model\Client;
use FGTCLB\OAuth2Server\Domain\Model\FrontendUserGroup;
use FGTCLB\OAuth2Server\Domain\Model\Scope;
use FGTCLB\OAuth2Server\Domain\Model\User;
use FGTCLB\OAuth2Server\Server\ServerFactory;
use FGTCLB\OAuth2Server\Session\UserSession;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Routing\RouterInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Handler for OAuth2 authorization requests
 *
 * If a user is logged in, an authorization request is completed automatically,
 * otherwise the authorization request is stored in the frontend session and
 * restored once a user has logged in. Login is enforced by a redirect to the
 * login page with a request to redirect back to this middleware after login.
 *
 * @see https://oauth2.thephpleague.com/authorization-server/auth-code-grant/#part-one
 */
final class OAuth2Authorization implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const REDIRECT_COOKIE_NAME = 'x-oauth-login-redirect';

    protected SiteFinder $siteFinder;
    protected Context $context;
    protected Configuration $configuration;

    public function __construct(Configuration $configuration, Context $context, SiteFinder $siteFinder)
    {
        $this->configuration = $configuration;
        $this->context = $context;
        $this->siteFinder = $siteFinder;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // redirect to auth after native login loop
        if (
            array_key_exists(self::REDIRECT_COOKIE_NAME, ($cookies = $request->getCookieParams())) &&
            $request->getMethod() === 'POST' &&
            $request->getAttribute('frontend.user')->user &&
            $request->getUri()->getPath() === $this->siteFinder
                ->getSiteByPageId($this->configuration->getLoginPage())
                ->getRouter($this->context)
                ->generateUri($this->configuration->getLoginPage())->getPath()
        ) {
            return new RedirectResponse($cookies[self::REDIRECT_COOKIE_NAME], 302, [
                'Set-Cookie' => sprintf('%s=; path=/; Max-Age=0', self::REDIRECT_COOKIE_NAME)
            ]);
        }

        // ignore if not related to oauth path
        if ($request->getUri()->getPath() !== '/oauth/authorize') {
            return $handler->handle($request);
        }

        // proceed with oauth
        $factory = new ServerFactory();
        $server = $factory->buildAuthorizationServer();

        $frontendUser = $request->getAttribute('frontend.user');
        if (!$frontendUser instanceof FrontendUserAuthentication) {
            $this->logger->warning('No frontend user logged in. Cannot continue with OAuth2 Authorization request.');
            return $handler->handle($request);
        }

        $userSession = new UserSession($frontendUser);
        $authorizationRequest = $userSession->getData('oauth2.authorizationRequest');

        $router = $this->siteFinder->getSiteByPageId($this->configuration->getLoginPage())->getRouter($this->context);

        if (!$authorizationRequest) {
            try {
                $authorizationRequest = $server->validateAuthorizationRequest($request);
            } catch (OAuthServerException $e) {
                $this->logger->warning(sprintf('Validating authorization request failed: %s', $e->getMessage()));

                return $this->generateLoginRedirectResponse($request, $router);
            }
        }

        // redirect to login of not logged in
        if (!$this->context->getPropertyFromAspect('frontend.user', 'isLoggedIn', false)) {
            $userSession->setData('oauth2.authorizationRequest', serialize($authorizationRequest));

            return $this->generateLoginRedirectResponse($request, $router);
        }

        // With TYPO3 11.5.17 it takes 3 loops to unserialize the AuthorizationRequest
        $count = 0;
        while (!$authorizationRequest instanceof AuthorizationRequest && $count < 10) {
            $authorizationRequest = unserialize($authorizationRequest, ['allowed_classes' => [AuthorizationRequest::class, Client::class, Scope::class, ObjectStorage::class, FrontendUserGroup::class]]);
            $count++;
        }

        // Handle error when $authorizationRequest is still a string
        if (!$authorizationRequest instanceof AuthorizationRequest) {
            $this->logger->error('Unserializing of AuthorizationRequest failed!');

            $redirectUri = $router->generateUri($this->configuration->getLoginPage());

            return new RedirectResponse($redirectUri);
        }

        $authorizationRequestApproved = true;

        // deny access when not granted by frontend user group
        if (!$authorizationRequest->getClient()->hasUserAccess($frontendUser)) {
            $this->logger->warning(
                sprintf('User is not granted on this client! Client: %s, UserID: %s', $authorizationRequest->getClient()->getIdentifier(), $frontendUser->user['uid'])
            );

            $authorizationRequestApproved = false;
        }

        // deny access when invalid email address
        if (!(new EmailValidator())->isValid($frontendUser->user['email'], new RFCValidation())) {
            $this->logger->warning(
                sprintf('User has invalid email address! Address: %s', $frontendUser->user['email'])
            );

            $authorizationRequestApproved = false;
        }

        if ($authorizationRequestApproved) {
            $this->logger->info(sprintf('Granted auth on client: %s with userID: %s', $authorizationRequest->getClient()->getIdentifier(), $frontendUser->user['uid']));
        }

        $authorizationRequest->setUser(new User((string)$this->context->getPropertyFromAspect('frontend.user', 'id')));
        $authorizationRequest->setAuthorizationApproved($authorizationRequestApproved);

        $userSession->removeData('oauth2.authorizationRequest');

        try {
            return $server->completeAuthorizationRequest($authorizationRequest, new Response());
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse(new Response());
        }
    }

    private function generateLoginRedirectResponse(ServerRequestInterface $request, RouterInterface $router): RedirectResponse
    {
        $redirectUri = $router->generateUri($this->configuration->getLoginPage());
        $referer = $request->getUri()->getPath() . '?' . $request->getUri()->getQuery();

        return new RedirectResponse($redirectUri, 302, [
            'Set-Cookie' => sprintf('%s=%s; path=/; Max-Age=300', self::REDIRECT_COOKIE_NAME, $referer)
        ]);
    }
}
