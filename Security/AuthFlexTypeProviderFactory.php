<?php

namespace FlexAuthBundle\Security;

/**
 * Class AuthFlexTypeProviderFactory
 * @package FlexAuthBundle\Security
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class AuthFlexTypeProviderFactory
{
    public static function fromEnv(string $envVar)
    {
        return new AuthFlexTypeCallbackProvider(function () use($envVar) {
            $type = $_ENV[$envVar];
            preg_match('/([A-Z0-9_]+)\?(.+)/i', $type , $parts);

            $params = [];
            if (array_key_exists(2, $parts)) {
                $stringParams = $parts[2];
                foreach (explode("&", $stringParams) as $keyValue) {
                    [$key, $value] = explode("=", $keyValue);
                    if ($key && $value) {
                        $params[$key] = $value;
                    }
                }
                $params['type'] = $parts[1];
            } else {
                foreach ($_ENV as $key => $value) {
                    if (strpos($key, $envVar.'_') === 0) {
                        $paramKey = substr($key, 0, strlen($key.'_'));
                        $params[strtolower($paramKey)] = $value;
                    }
                }
                $params['type'] = $type;
            }

            return $params;
        });
    }
}