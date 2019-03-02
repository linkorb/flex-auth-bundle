<?php

namespace FlexAuthBundle\Security\Type;

use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Interface UserProviderFactoryInterface
 */
interface UserProviderFactoryInterface
{
    /**
     * @param mixed $params
     * @return UserProviderInterface
     * @throws InvalidParamsException
     */
    public function create($params);
}