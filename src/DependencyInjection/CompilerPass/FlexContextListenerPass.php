<?php

namespace FlexAuthBundle\DependencyInjection\CompilerPass;

use FlexAuth\FlexAuthTypeProviderInterface;
use FlexAuth\Security\FlexAuthContextListenerDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Http\Firewall\ContextListener;

/**
 * Class RegisterFlexContextListenerPass
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexContextListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->patchContextListeners($container);
    }

    /**
     * Decorate context listeners.
     *
     * @param ContainerBuilder $container
     * @see SecurityExtension::createContextListener
     * @see ContextListener
     */
    private function patchContextListeners(ContainerBuilder $container)
    {
        $exceptedContextKeys = []; // TODO from config

        $contextListenerCounter = 0;
        while($container->hasDefinition('security.context_listener.'. $contextListenerCounter)) {
            $id = 'security.context_listener.'. $contextListenerCounter;
            $contextListenerCounter++;

            $contextListenerDefinition = $container->getDefinition($id);
            $contextKey = $contextListenerDefinition->getArgument(2);

            if (!in_array($contextKey, $exceptedContextKeys, true)) {
                $decoratedListenerID = 'flex_auth.security.decorated_context_listener.'.$contextListenerCounter;
                $container->setDefinition($decoratedListenerID, $contextListenerDefinition);

                $decoratorDefinition = new Definition(FlexAuthContextListenerDecorator::class);

                // $decoratorDefinition->setDecoratedService($decoratedListenerID); ?!

                $decoratorDefinition->setArgument(0, new Reference($decoratedListenerID));
                $decoratorDefinition->setArgument(1, new Reference(FlexAuthTypeProviderInterface::class));

                $container->setDefinition($id, $decoratorDefinition);
            }
        }
    }
}