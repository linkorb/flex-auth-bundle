<?php

namespace FlexAuthBundle\DependencyInjection;

use FlexAuthBundle\DependencyInjection\CompilerPass\RegisterAuthFlexTypePass;
use FlexAuthBundle\FlexUserProvider;
use FlexAuthBundle\Security\Type\Entity\EntityUserProviderFactory;
use FlexAuthBundle\Security\Type\Memory\MemoryUserProviderFactory;
use FlexAuthBundle\Security\UserProviderFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

class FlexAuthExtension extends Extension
{
    const USER_PROVIDER_FACTORY_SERVICE_ID = 'flex_auth.security.user_provider_factory';

    public function load(array $configs, ContainerBuilder $container)
    {
        // TODO move service's definitions to services.yaml file

        $definition = new Definition(FlexUserProvider::class);
        $definition->setAutowired(true);
        $container->setDefinition('flex_auth.security.user.provider', $definition);


        $definition = new Definition(UserProviderFactory::class);
        $definition->setAutowired(true);
        $container->setDefinition(self::USER_PROVIDER_FACTORY_SERVICE_ID, $definition);

        $definition = new Definition(MemoryUserProviderFactory::class);
        $definition->setAutowired(true);
        $definition->addTag(RegisterAuthFlexTypePass::AUTH_FLEX_TYPE_TEG, ['type' => MemoryUserProviderFactory::TYPE]);
        $container->setDefinition('flex_auth.type.'.MemoryUserProviderFactory::TYPE, $definition);

        $definition = new Definition(EntityUserProviderFactory::class);
        $definition->setAutowired(true);
        $definition->addTag(RegisterAuthFlexTypePass::AUTH_FLEX_TYPE_TEG, ['type' => EntityUserProviderFactory::TYPE]);
        $container->setDefinition('flex_auth.type.'.EntityUserProviderFactory::TYPE, $definition);

        /* TODO userbase jwt */
    }
}