<?php

namespace FlexAuthBundle\Security;

/**
 * Class AuthFlexTypeProviderFactory
 * @package FlexAuthBundle\Security
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class AuthFlexTypeProviderFactory
{
    public static function fromEnv(string $envVar)
    {
        return new AuthFlexTypeCallbackProvider(function () use($envVar) {
            return 'memory?users=alice:4l1c3:ROLE_ADMIN;ROLE_EXAMPLE,bob:b0b:ROLE_EXAMPLE)'; //$_ENV[$envVar];
        });
    }
}