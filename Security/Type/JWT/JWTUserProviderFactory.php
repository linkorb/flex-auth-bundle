<?php

namespace FlexAuthBundle\Security\Type\JWT;

use FlexAuthBundle\Security\Type\UserProviderFactoryInterface;

/**
 * Class JWTUserProviderFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class JWTUserProviderFactory implements UserProviderFactoryInterface
{
    const TYPE = 'jwt';

    public function create($params)
    {
        return null;
    }
}