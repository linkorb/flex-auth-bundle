<?php


namespace FlexAuthBundle\Security\Type\JWT;


use Symfony\Component\Security\Core\User\UserInterface;

interface JWTUserFactoryInterface
{
    public function createFromPayload($payload): UserInterface;
}