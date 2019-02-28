<?php

namespace FlexAuthBundle\Security;

use FlexAuthBundle\Security\Type\InvalidParamsException;
use FlexAuthBundle\Security\Type\UserProviderFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProviderFactory
 *
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class UserProviderFactory
{
    /**
     * @var UserProviderFactoryInterface[]
     */
    protected $factories = [];

    protected $authFlexTypeProvider;

    public function __construct(AuthFlexTypeProviderInterface $authFlexTypeProvider)
    {
        $this->authFlexTypeProvider = $authFlexTypeProvider;
    }

    public function addType($typeKey, UserProviderFactoryInterface $userFactory)
    {
        if (array_key_exists($typeKey, $this->factories)) {
            throw new \InvalidArgumentException(sprintf('Auth type "%s" was added already', $typeKey));
        }

        $this->factories[$typeKey] = $userFactory;
    }

    /**
     * @return UserProviderInterface
     * @throws \Exception
     */
    public function create(): UserProviderInterface
    {
        $result = $this->resolveTypeAndParams();
        $type = $result[0];
        $paramsString = $result[1];

        if (!array_key_exists($type, $this->factories)) {
            throw new \InvalidArgumentException(sprintf('Auth type "%s" is not supported', $type));
        }
        return $this->factories[$type]->create($paramsString);
    }


    /**
     * Resolve rype and params from env string
     * @return array
     * @throws \Exception
     */
    private function resolveTypeAndParams()
    {
        $flexAuth = $this->authFlexTypeProvider->provide();

        $parts = [];
        $allowTypes = array_keys($this->factories);

        preg_match('/(' . join('|', $allowTypes) . ')\?(.+)/i', $flexAuth, $parts);
        if (count($parts) !== 3) {
            throw new InvalidParamsException(
                sprintf('Unsupported flex auth environment format. Allow: %s', join(', ', $allowTypes))
            );
        }

        $type = $parts[1];
        // TODO
        $params = $parts[2];

        return [$type, $params];
    }
}