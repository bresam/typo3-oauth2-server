<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\DependencyInjection;

use FGTCLB\OAuth2Server\Service\OAuthApiEndpointRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CompilerPass implements CompilerPassInterface
{
    public const TAG_NAME = 'oauth.api.endpoint';

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $id => $tags) {
            $definition = $container->findDefinition($id);
            if (!$definition->isAutoconfigured() || $definition->isAbstract()) {
                continue;
            }

            // Services that implement MyCustomInterface need to be public,
            // to be lazy loadable by the registry via $container->get()
            $definition->setPublic(true);
            // Add a method call to the registry class to the (auto-generated) factory for
            // the registry service.
            // This supersedes explicit registrations in ext_localconf.php (which're
            // still possible and can be combined with this autoconfiguration).
            $container->findDefinition(OAuthApiEndpointRegistry::class)
                ->addMethodCall('addApiEndpoint', [$id]);
        }
    }
}