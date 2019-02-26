<?php

namespace FlexAuthBundle\Security;

/**
 * Class AuthFlexTypeCallbackProvider
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class AuthFlexTypeCallbackProvider implements AuthFlexTypeProviderInterface
{
    /** @var callable */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function provide(): string
    {
        return call_user_func($this->callback);
    }
}