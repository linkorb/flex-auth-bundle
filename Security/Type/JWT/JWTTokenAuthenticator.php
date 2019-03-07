<?php

namespace FlexAuthBundle\Security\Type\JWT;

use Firebase\JWT\JWT;
use FlexAuthBundle\Security\AuthFlexTypeProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class JWTTokenAuthenticator
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class JWTTokenAuthenticator extends AbstractGuardAuthenticator
{
    const TOKEN_HEADER = 'Authorization';
    const TOKEN_PREFIX = 'Bearer ';

    private $passwordEncoder;
    private $JWTUserFactory;

    /** @var AuthFlexTypeProviderInterface */
    private $authFlexTypeProvider;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        JWTUserFactoryInterface $JWTUserFactory,
        AuthFlexTypeProviderInterface $authFlexTypeProvider
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->JWTUserFactory = $JWTUserFactory;
        $this->authFlexTypeProvider = $authFlexTypeProvider;
    }

    public function supports(Request $request)
    {
        $params = $this->authFlexTypeProvider->provide();

        $jwtEnable = $params['type'] === JWTUserProviderFactory::TYPE;

        $hasHeader = $request->headers->has(self::TOKEN_HEADER) &&
            strpos($request->headers->get(self::TOKEN_HEADER), self::TOKEN_PREFIX) === 0;

        $hasQuery = $request->query->has('jwt');
        return $jwtEnable && ($hasHeader || $hasQuery);
    }

    public function getCredentials(Request $request)
    {
        $authorization = $request->headers->get(self::TOKEN_HEADER);
        if ($authorization) {
            $token = substr($authorization, strlen(self::TOKEN_PREFIX));
        } else {
            $token = $request->query->get('jwt');
        }
        return $token;
    }

    public function createTokenFromUser(UserInterface $user): string
    {
        $FLEX_AUTH_USER_FIELD = 'username';
        $FLEX_AUTH_ROLE_FIELD = 'permissions';

        $user = [
            $FLEX_AUTH_USER_FIELD => $user->getUsername(),
            $FLEX_AUTH_ROLE_FIELD => implode(",", $user->getRoles())
        ];

        $encodedPayload = JWT::encode($user, $this->getPrivateKey(), $this->getAlgorithm());

        return $encodedPayload;
    }

    public function getUser($credentialsToken, UserProviderInterface $userProvider)
    {
        if (!is_string($credentialsToken)) {
            throw new \InvalidArgumentException(
                sprintf('The first argument of the "%s()" method must be string.', __METHOD__, __CLASS__)
            );
        }

        $FLEX_AUTH_USER_FIELD = 'username';
        $FLEX_AUTH_ROLE_FIELD = 'permissions';

        $encodedPayload = $credentialsToken;
        $decodedPayload = JWT::decode($encodedPayload, $this->getPublicKey(), [$this->getAlgorithm()]);

        $user = $this->JWTUserFactory->createFromPayload([
           'username' => $decodedPayload->{$FLEX_AUTH_USER_FIELD},
           'roles' => explode(",", $decodedPayload->{$FLEX_AUTH_ROLE_FIELD})
        ]);

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response(sprintf('"%s" header required', self::TOKEN_HEADER), 401);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

    }

    public function supportsRememberMe()
    {
        return false;
    }


    private function getAlgorithm()
    {
        $params = $this->authFlexTypeProvider->provide();

        return array_key_exists('algo', $params) ? $params['algo'] : 'RS256';
    }

    private function getPrivateKey()
    {
        $params = $this->authFlexTypeProvider->provide();

        return $params['private_key'];
    }

    private function getPublicKey()
    {
        $params = $this->authFlexTypeProvider->provide();

        return $params['public_key'];
    }

}