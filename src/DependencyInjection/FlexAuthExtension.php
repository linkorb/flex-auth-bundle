<?php

namespace FlexAuthBundle\DependencyInjection;

use FlexAuthBundle\DependencyInjection\CompilerPass\RegisterAuthFlexTypePass;
use FlexAuth\Security\FlexUserProvider;
use FlexAuth\Type\Entity\EntityUserProviderFactory;
use FlexAuth\Type\JWT\FlexTypeJWTEncoder;
use FlexAuth\Type\JWT\JWTEncoderInterface;
use FlexAuth\Type\JWT\JWTUserProviderFactory;
use FlexAuth\Type\JWT\DefaultJWTUserFactory;
use FlexAuth\Type\JWT\JWTTokenAuthenticator;
use FlexAuth\Type\JWT\JWTUserFactoryInterface;
use FlexAuth\Type\Memory\MemoryUserProviderFactory;
use FlexAuth\Type\UserbaseClient\UserbaseClientUserProviderFactory;
use FlexAuth\UserProviderFactory;
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

    public function load(array $configs, ContainerBuilder $container)
    {
        /* Flex auth type registration */

        /* InMemory */
        $definition = new Definition(MemoryUserProviderFactory::class);
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
            $definition->addTag(RegisterAuthFlexTypePass::AUTH_FLEX_TYPE_TEG, ['type' => UserbaseClientUserProviderFactory::TYPE]);
            $container->setDefinition('flex_auth.type.'.UserbaseClientUserProviderFactory::TYPE, $definition);
        }

        /* JWT */
        $definition = new Definition(JWTUserProviderFactory::class);
        $definition->addTag(RegisterAuthFlexTypePass::AUTH_FLEX_TYPE_TEG, ['type' => JWTUserProviderFactory::TYPE]);
        $container->setDefinition('flex_auth.type.'.JWTUserProviderFactory::TYPE, $definition);


        /* Common services */
        $definition = new Definition(UserProviderFactory::class);
        $definition->setAutowired(true);
        $container->setDefinition(self::USER_PROVIDER_FACTORY_SERVICE_ID, $definition);

        $definition = new Definition(FlexUserProvider::class);
        $definition->addArgument(new Reference(self::USER_PROVIDER_FACTORY_SERVICE_ID));
        $container->setDefinition(self::USER_PROVIDER_SERVICE_ID, $definition);


        /* JWT services */
        $definition = new Definition(FlexTypeJWTEncoder::class);
        $definition->setAutowired(true);
        $container->setDefinition(FlexTypeJWTEncoder::class, $definition);

        $definition = new Definition(FlexTypeJWTEncoder::class);
        $definition->setAutowired(true);
        $container->setDefinition(JWTEncoderInterface::class, $definition);

        $definition = new Definition(JWTTokenAuthenticator::class);
        $definition->setAutowired(true);
        $container->setDefinition(JWTTokenAuthenticator::class, $definition);

        $definition = new Definition(DefaultJWTUserFactory::class);
        $definition->setAutowired(true);
        $container->setDefinition(JWTUserFactoryInterface::class, $definition);
    }
}