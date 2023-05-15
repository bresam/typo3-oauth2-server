<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\Service;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use FGTCLB\OAuth2Server\DependencyInjection\ApiEndpoint\OAuthApiEndpointInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

class OAuthDiscoveryEndpoint implements OAuthApiEndpointInterface
{
    public function isPublic(): bool
    {
        return true;
    }

    public function getScope(): string
    {
        return '*';
    }

    public function canHandlePath(string $path): bool
    {
        return $path === '/.well-known/openid-configuration';
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        $requestScheme = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['REQUEST_SCHEME'];

        return new JsonResponse([
            "issuer" => sprintf('%s://%s', $requestScheme, $_SERVER['HTTP_HOST']),
            "authorization_endpoint" => sprintf('%s://%s/oauth/authorize', $requestScheme, $_SERVER['HTTP_HOST']),
            "token_endpoint" => sprintf('%s://%s/oauth/token', $requestScheme, $_SERVER['HTTP_HOST']),
            "jwks_uri" => sprintf('%s://%s/oauth/jwks.json', $requestScheme, $_SERVER['HTTP_HOST']),
            "scopes_supported" => ["openid", "profile", "email", "address", "phone"],
            "claims_supported" => [
                "aud", "iss", "iat", "exp", "sub", "auth_time",
                "prefered_username", "name", "given_name", "family_name",
                "picture", "website", "telephone", "email"
            ],
//            "token_endpoint_auth_methods_supported" => ["client_secret_basic", "private_key_jwt"],
//            "token_endpoint_auth_signing_alg_values_supported" => ["RS256", "ES256"],
//            "userinfo_endpoint" => "https://server.example.com/connect/userinfo",
//            "check_session_iframe" => "https://server.example.com/connect/check_session",
//            "end_session_endpoint" => "https://server.example.com/connect/end_session",
//            "registration_endpoint" => "https://server.example.com/connect/register",
//            "response_types_supported" => ["code", "code id_token", "id_token", "token id_token"],
//            "acr_values_supported" => ["urn:mace:incommon:iap:silver", "urn:mace:incommon:iap:bronze"],
//            "subject_types_supported" => ["public", "pairwise"],
//            "userinfo_signing_alg_values_supported" => ["RS256", "ES256", "HS256"],
//            "userinfo_encryption_alg_values_supported" => ["RSA1_5", "A128KW"],
//            "userinfo_encryption_enc_values_supported" => ["A128CBC-HS256", "A128GCM"],
//            "id_token_signing_alg_values_supported" => ["RS256", "ES256", "HS256"],
//            "id_token_encryption_alg_values_supported" => ["RSA1_5", "A128KW"],
//            "id_token_encryption_enc_values_supported" => ["A128CBC-HS256", "A128GCM"],
//            "request_object_signing_alg_values_supported" => ["none", "RS256", "ES256"],
//            "display_values_supported" => ["page", "popup"],
//            "claim_types_supported" => ["normal", "distributed"],
//            "claims_parameter_supported" => true,
//            "service_documentation" => "http://server.example.com/connect/service_documentation.html",
//            "ui_locales_supported" => ["en-US", "en-GB", "en-CA", "fr-FR", "fr-CA"]
        ]);
    }
}