<?php

namespace FlexAuthBundle\Security\Type\Entity;


use FlexAuthBundle\Security\Type\UserProviderFactoryInterface;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;

class EntityUserProviderFactory implements UserProviderFactoryInterface
{
    const TYPE = 'entity';

    public function create($paramsString)
    {
        $param = $this->convertParamsStringToUsers($paramsString);
        return new EntityUserProvider($param['manager_registry'], $param['classOrAlias'], $param['property'], $param['managerName']);
    }

    private function convertParamsStringToUsers($paramsString)
    {
        $param = [];

        // TODO
        // throw new InvalidParamsException("Unsupported format");

        return $param;
    }
}