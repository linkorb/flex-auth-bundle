<?php

namespace FlexAuthBundle;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class FlexUserProvider
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexUserProvider implements UserProviderInterface
{
    /**
     * @var UserProviderInterface
     */
    protected $provider;

    public function loadUserByUsername($username)
    {
        // TODO: Implement loadUserByUsername() method.
    }

    public function refreshUser(UserInterface $user)
    {
        // TODO: Implement refreshUser() method.
    }

    public function supportsClass($class)
    {
        // TODO: Implement supportsClass() method.
    }
}