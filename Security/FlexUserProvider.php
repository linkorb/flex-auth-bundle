<?php

namespace FlexAuthBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class FlexUserProvider
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexUserProvider implements UserProviderInterface
{
    /**
     * @var UserProviderFactory
     */
    protected $userProviderFactory;

    public function __construct(UserProviderFactory $userProviderFactory)
    {
        $this->userProviderFactory = $userProviderFactory;
    }

    public function loadUserByUsername($username)
    {
        $this->userProviderFactory->create()->loadUserByUsername($username);
    }

    public function refreshUser(UserInterface $user)
    {
        $this->userProviderFactory->create()->refreshUser($user);
    }

    public function supportsClass($class)
    {
        $this->userProviderFactory->create()->supportsClass($class);
    }
}