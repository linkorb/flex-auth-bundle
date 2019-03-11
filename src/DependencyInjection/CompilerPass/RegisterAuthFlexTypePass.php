<?php

namespace FlexAuthBundle\DependencyInjection\CompilerPass;


use FlexAuthBundle\DependencyInjection\FlexAuthExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterAuthFlexTypePass
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class RegisterAuthFlexTypePass implements CompilerPassInterface
{
    const AUTH_FLEX_TYPE_TEG = 'auth_flex_type';

    public function process(ContainerBuilder $container)
    {
        foreach($container->findTaggedServiceIds(self::AUTH_FLEX_TYPE_TEG) as $serviceId => $tags) {
            if (!array_key_exists(0, $tags) || !array_key_exists('type', $tags[0]) || !$tags[0]['type']) {
                throw new InvalidArgumentException(sprintf('No specify type for %s tag', self::AUTH_FLEX_TYPE_TEG));
            }
            $container->getDefinition(FlexAuthExtension::USER_PROVIDER_FACTORY_SERVICE_ID)
                ->addMethodCall('addType', [$tags[0]['type'], new Reference($serviceId)]);
        }
    }
}