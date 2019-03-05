<?php

namespace FlexAuthBundle\Security\Type\JWT;

use Firebase\JWT\JWT;
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

    // TODO move to params
    private $key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
-----END RSA PRIVATE KEY-----
EOD;

    private $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;

    private $algo  = 'RS256';

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        JWTUserFactoryInterface $JWTUserFactory
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->JWTUserFactory = $JWTUserFactory;
    }

    public function supports(Request $request)
    {
        $hasHeader = $request->headers->has(self::TOKEN_HEADER) &&
            strpos($request->headers->get(self::TOKEN_HEADER), self::TOKEN_PREFIX) === 0;

        $hasQuery = $request->query->has('jwt');
        return $hasHeader || $hasQuery;
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

        $encodedPayload = JWT::encode($user, $this->key, $this->algo);

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
        $decodedPayload = JWT::decode($encodedPayload, $this->publicKey, [$this->algo]);

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

}