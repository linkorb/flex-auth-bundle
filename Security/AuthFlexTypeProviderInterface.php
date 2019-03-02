<?php

namespace FlexAuthBundle\Security;

/**
 * Interface AuthFlexTypeProviderInterface
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
interface AuthFlexTypeProviderInterface
{
    /**
     * Return type and params as string in format type?param1=value1&param2=value2
     * Example
     *  entity?class=\App\Entities\User&property=username
     *  userbase?dsn=https://username:password@userbase.example.com
     * @return string
     */
    public function provide(): array;
}