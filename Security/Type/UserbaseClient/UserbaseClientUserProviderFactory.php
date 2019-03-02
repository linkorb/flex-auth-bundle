<?php

namespace FlexAuthBundle\Security\Type\UserbaseClient;


use FlexAuthBundle\Security\Type\UserProviderFactoryInterface;
use UserBase\Client\UserProvider;
use UserBase\Client\Client;

class UserbaseClientUserProviderFactory implements UserProviderFactoryInterface
{
    public const TYPE = 'userbase';

    public function create($params)
    {
        return new UserProvider(new Client($params["url"], $params["username"], $params["password"]));
    }
}