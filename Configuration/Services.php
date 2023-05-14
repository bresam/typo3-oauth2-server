<?php

declare(strict_types = 1);

use FGTCLB\OAuth2Server\DependencyInjection\ApiEndpoint\OAuthApiEndpointInterface;
use FGTCLB\OAuth2Server\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerForAutoconfiguration(OAuthApiEndpointInterface::class)
        ->addTag(CompilerPass::TAG_NAME);

    $containerBuilder->addCompilerPass(new CompilerPass());
};