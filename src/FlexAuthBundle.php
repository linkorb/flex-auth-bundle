<?php

namespace FlexAuthBundle;

use FlexAuthBundle\DependencyInjection\CompilerPass\FlexContextListenerPass;
use FlexAuthBundle\DependencyInjection\CompilerPass\RegisterFlexAuthTypePass;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class FlexAuthBundle
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexAuthBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SecurityExtension $securityExtension */
        $securityExtension = $container->getExtension('security');
        $securityExtension->addUserProviderFactory(new FlexUserProviderFactory('flex_auth'));

        $container->addCompilerPass(new RegisterFlexAuthTypePass());
        $container->addCompilerPass(new FlexContextListenerPass());
    }
}