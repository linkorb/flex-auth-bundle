<?php

namespace FlexAuthBundle;

use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FlexAuthBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SecurityExtension $securityExtension */
        $securityExtension = $container->getExtension('security');
        $securityExtension->addUserProviderFactory(
            new FlexUserUserProviderFactory('flex_auth', 'flex_auth.security.user.provider')
        );
    }
}