<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\Service;

use FGTCLB\OAuth2Server\DependencyInjection\ApiEndpoint\OAuthApiEndpointInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;

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

    public function handle(ServerRequest $request): Response
    {
        $requestScheme = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'https';

        return new JsonResponse([
            "issuer" => sprintf('%s://%s', $requestScheme, $_SERVER['HTTP_HOST']),
            "authorization_endpoint" => sprintf('%s://%s/oauth/authorize', $requestScheme, $_SERVER['HTTP_HOST']),
            "token_endpoint" => sprintf('%s://%s/oauth/token', $requestScheme, $_SERVER['HTTP_HOST']),
            "userinfo_endpoint" => sprintf('%s://%s/connect/userinfo', $requestScheme, $_SERVER['HTTP_HOST']),
            "introspection_endpoint" => sprintf('%s://%s/token/introspect', $requestScheme, $_SERVER['HTTP_HOST']),
            "jwks_uri" => sprintf('%s://%s/connect/jwks.json', $requestScheme, $_SERVER['HTTP_HOST']),
//            "registration_endpoint" => sprintf('%s://%s/connect/register', $requestScheme, $_SERVER['HTTP_HOST']),
            "scopes_supported" => ["openid", "profile", "email", "address", "phone"],
            "response_types_supported" => ["code", "code id_token", "id_token", "token id_token"],
            "response_modes_supported" => ["query", "fragment"],
            "grant_types_supported" => ["authorization_code", "implicit"],
//            "acr_values_supported" => ?,
            "subject_types_supported" => ["public", "pairwise"],
            "id_token_signing_alg_values_supported" => ["RS256", "ES256", "HS256"],
//            "id_token_encryption_alg_values_supported" => ["RSA1_5", "A128KW"],
//            "id_token_encryption_enc_values_supported" => ["A128CBC-HS256", "A128GCM"],
            "claims_supported" => [
                "aud", "iss", "iat", "exp", "sub", "auth_time",
                "prefered_username", "name", "given_name", "family_name",
                "picture", "website", "telephone", "email"
            ],
            "token_endpoint_auth_methods_supported" => ["client_secret_basic", "private_key_jwt"],
//            "token_endpoint_auth_signing_alg_values_supported" => ["RS256", "ES256"],
//            "check_session_iframe" => "https://server.example.com/connect/check_session",
//            "end_session_endpoint" => "https://server.example.com/connect/end_session",
//            "acr_values_supported" => ["urn:mace:incommon:iap:silver", "urn:mace:incommon:iap:bronze"],
//            "userinfo_signing_alg_values_supported" => ["RS256", "ES256", "HS256"],
//            "userinfo_encryption_alg_values_supported" => ["RSA1_5", "A128KW"],
//            "userinfo_encryption_enc_values_supported" => ["A128CBC-HS256", "A128GCM"],
//            "request_object_signing_alg_values_supported" => ["none", "RS256", "ES256"],
//            "display_values_supported" => ["page", "popup"],
//            "claim_types_supported" => ["normal", "distributed"],
//            "claims_parameter_supported" => true,
//            "service_documentation" => "http://server.example.com/connect/service_documentation.html",
//            "ui_locales_supported" => ["en-US", "en-GB", "en-CA", "fr-FR", "fr-CA"]
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}