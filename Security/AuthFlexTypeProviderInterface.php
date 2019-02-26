<?php

namespace FlexAuthBundle\Security;


interface AuthFlexTypeProviderInterface
{
    public function provide(): string;
}