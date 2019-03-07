<?php

namespace FlexAuthBundle\DependencyInjection;

use FlexAuthBundle\DependencyInjection\CompilerPass\RegisterAuthFlexTypePass;
use FlexAuthBundle\Security\FlexUserProvider;
use FlexAuthBundle\Security\Type\Entity\EntityUserProviderFactory;
use FlexAuthBundle\Security\Type\JWT\FlexTypeJWTEncoder;
use FlexAuthBundle\Security\Type\JWT\JWTEncoderInterface;
use FlexAuthBundle\Security\Type\JWT\JWTUserProviderFactory;
use FlexAuthBundle\Security\Type\JWT\DefaultJWTUserFactory;
use FlexAuthBundle\Security\Type\JWT\JWTTokenAuthenticator;
use FlexAuthBundle\Security\Type\JWT\JWTUserFactoryInterface;
use FlexAuthBundle\Security\Type\Memory\MemoryUserProviderFactory;
use FlexAuthBundle\Security\Type\UserbaseClient\UserbaseClientUserProviderFactory;
use FlexAuthBundle\Security\UserProviderFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class FlexAuthExtension
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexAuthExtension extends Extension
{
    /**
     * @var string
     * @see UserProviderFactory
     */
    const USER_PROVIDER_FACTORY_SERVICE_ID = 'flex_auth.security.user_provider_factory';

    /**
     * @var string
     * @see UserProviderInterface
     */
    const USER_PROVIDER_SERVICE_ID = 'flex_auth.security.user.provider';

    /**
     * @TODO move service's definitions to services.yaml file?!
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        /* Flex auth type registration */

        /* InMemory */
        $definition = new Definition(MemoryUserProviderFactory::class);
        $definition->setAutowired(true);
        $definition->addTag(RegisterAuthFlexTypePass::AUTH_FLEX_TYPE_TEG, ['type' => MemoryUserProviderFactory::TYPE]);
        $container->setDefinition('flex_auth.type.'.MemoryUserProviderFactory::TYPE, $definition);

        /* Entity */
        $definition = new Definition(EntityUserProviderFactory::class);
        $definition->setAutowired(true);
        $definition->addTag(RegisterAuthFlexTypePass::AUTH_FLEX_TYPE_TEG, ['type' => EntityUserProviderFactory::TYPE]);
        $container->setDefinition('flex_auth.type.'.EntityUserProviderFactory::TYPE, $definition);


        if (class_exists(\UserBase\Client\UserProvider::class)) {
            /* Userbase */
            $definition = new Definition(UserbaseClientUserProviderFactory::class);
            $definition->setAutowired(true);
            $definition->addTag(RegisterAuthFlexTypePass::AUTH_FLEX_TYPE_TEG, ['type' => UserbaseClientUserProviderFactory::TYPE]);
            $container->setDefinition('flex_auth.type.'.UserbaseClientUserProviderFactory::TYPE, $definition);
        }

        /* JWT */
        $definition = new Definition(JWTUserProviderFactory::class);
        $definition->setAutowired(true);
        $definition->addTag(RegisterAuthFlexTypePass::AUTH_FLEX_TYPE_TEG, ['type' => JWTUserProviderFactory::TYPE]);
        $container->setDefinition('flex_auth.type.'.JWTUserProviderFactory::TYPE, $definition);

        /* Common services */
        $definition = new Definition(UserProviderFactory::class);
        $definition->setAutowired(true);
        $container->setDefinition(self::USER_PROVIDER_FACTORY_SERVICE_ID, $definition);

        $definition = new Definition(FlexUserProvider::class);
        $definition->addArgument(new Reference(self::USER_PROVIDER_FACTORY_SERVICE_ID));
        $container->setDefinition(self::USER_PROVIDER_SERVICE_ID, $definition);

        $definition = new Definition(FlexTypeJWTEncoder::class);
        $definition->setAutowired(true);
        $container->setDefinition(FlexTypeJWTEncoder::class, $definition);

        $definition = new Definition(FlexTypeJWTEncoder::class);
        $definition->setAutowired(true);
        $container->setDefinition(JWTEncoderInterface::class, $definition);

        /* JWT services */
        $definition = new Definition(JWTTokenAuthenticator::class);
        $definition->setAutowired(true);
        $container->setDefinition(JWTTokenAuthenticator::class, $definition);

        $definition = new Definition(DefaultJWTUserFactory::class);
        $definition->setAutowired(true);
        $container->setDefinition(JWTUserFactoryInterface::class, $definition);
    }
}