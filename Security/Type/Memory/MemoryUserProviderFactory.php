<?php

namespace FlexAuthBundle\Security\Type\Memory;


use FlexAuthBundle\Security\Type\InvalidParamsException;
use FlexAuthBundle\Security\Type\UserProviderFactoryInterface;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

class MemoryUserProviderFactory implements UserProviderFactoryInterface
{
    const TYPE = 'memory';

    public function create($paramsString)
    {
        return new InMemoryUserProvider($this->convertParamsStringToUsers($paramsString));
    }

    private function convertParamsStringToUsers($paramsString)
    {
        $users = [];

        // TODO
        // throw new InvalidParamsException("Unsupported format");

        return $users;
    }
}