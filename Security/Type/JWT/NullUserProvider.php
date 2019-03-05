<?php

namespace FlexAuthBundle\Security\Type\JWT;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class NullUserProvider
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class NullUserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        throw new UsernameNotFoundException();
    }

    public function refreshUser(UserInterface $user)
    {

    }

    public function supportsClass($class)
    {
        return $class instanceof UserInterface;
    }
}