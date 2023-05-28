<?php

namespace App\Security;

use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UsersAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';


    public function __construct(private UrlGeneratorInterface $urlGenerator, private UsersRepository $usersRepository, private Security $security)

    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        $user = $this->usersRepository->findOneBy(['email' => $request->getSession()->get(Security::LAST_USERNAME)]);
        if ($user != null) {
            $request->getSession()->set('id', $user->getId());
            $user_token = md5(uniqid());
            $user->setUserToken($user_token);
            $this->usersRepository->save($user, true);
            $request->getSession()->set('user_token', $user_token);

            // For example:


            //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
        }
        $user = $this->security->getUser();
        if (in_array('ROLE_PATIENT', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_calendar'));
        } else {
            return new RedirectResponse($this->urlGenerator->generate('app_mespat'));
        }
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
