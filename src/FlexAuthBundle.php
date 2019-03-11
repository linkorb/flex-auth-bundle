<?php

namespace FlexAuthBundle;

use FlexAuthBundle\DependencyInjection\CompilerPass\RegisterAuthFlexTypePass;
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

        $container->addCompilerPass(new RegisterAuthFlexTypePass());
    }
}