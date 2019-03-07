<?php

namespace FlexAuthBundle;

/**
 * Class FlexUserProviderFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */

use FlexAuthBundle\DependencyInjection\FlexAuthExtension;
use FlexAuth\AuthFlexTypeProviderInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class FlexUserUserProviderFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexUserProviderFactory implements UserProviderFactoryInterface
{
    public const DEFAULT_FLEX_AUTH_ENV_VAR = 'FLEX_AUTH';
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function create(ContainerBuilder $container, $id, $config)
    {
        $container->setDefinition($id, new ChildDefinition(FlexAuthExtension::USER_PROVIDER_SERVICE_ID));
        if (!array_key_exists('env_var', $config)) {
            throw new \InvalidArgumentException("'env_var' does not exist in config");
        }
        $definition = new Definition(AuthFlexTypeProviderInterface::class);
        $definition->setFactory([AuthFlexTypeProviderFactory::class, 'fromEnv']);
        $definition->addArgument($config['env_var']);
        $container->setDefinition(AuthFlexTypeProviderInterface::class, $definition);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
            ->scalarNode('env_var')->defaultValue(self::DEFAULT_FLEX_AUTH_ENV_VAR)->end()
            ->end();
    }
}