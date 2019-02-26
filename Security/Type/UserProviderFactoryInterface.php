<?php

namespace FlexAuthBundle\Security\Type;

use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Interface UserProviderFactoryInterface
 */
interface UserProviderFactoryInterface
{
    /**
     * @param string $paramsString
     * @return UserProviderInterface
     * @throws InvalidParamsException
     */
    public function create($paramsString);
}