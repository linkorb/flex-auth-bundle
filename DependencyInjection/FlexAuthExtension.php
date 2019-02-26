<?php

namespace FlexAuthBundle\DependencyInjection;

use FlexAuthBundle\DependencyInjection\CompilerPass\RegisterAuthFlexTypePass;
use FlexAuthBundle\Security\AuthFlexTypeCallbackProvider;
use FlexAuthBundle\Security\AuthFlexTypeProviderInterface;
use FlexAuthBundle\Security\FlexUserProvider;
use FlexAuthBundle\Security\Type\Entity\EntityUserProviderFactory;
use FlexAuthBundle\Security\Type\Memory\MemoryUserProviderFactory;
use FlexAuthBundle\Security\UserProviderFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Class FlexAuthExtension
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexAuthExtension extends Extension
{
    const USER_PROVIDER_FACTORY_SERVICE_ID = 'flex_auth.security.user_provider_factory';
    const USER_PROVIDER_SERVICE_ID = 'flex_auth.security.user.provider';
    const TYPE_PROVIDER_SERVICE_ID = 'flex_auth.type_provider';

    public function load(array $configs, ContainerBuilder $container)
    {
        // TODO move service's definitions to services.yaml file

        $definition = new Definition(FlexUserProvider::class);
        $definition->setAutowired(true);
        $container->setDefinition(self::USER_PROVIDER_SERVICE_ID, $definition);


        /* Flex auth type registration */
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


        $definition = new Definition(AuthFlexTypeProviderInterface::class);
        $definition->setFactory([self::class, 'createDefaultTypeProvider']);
        $container->setDefinition(self::TYPE_PROVIDER_SERVICE_ID, $definition);
    }

    public static function createDefaultTypeProvider() {
        return new AuthFlexTypeCallbackProvider(function () {
           return $_ENV['FLEX_AUTH'];
        });
    }
}