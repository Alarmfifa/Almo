<?php

namespace Almo\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SecurityController extends Controller
{
    /**
     * @Route("/login")
     * @Template("AlmoUserBundle:Security:login.html.twig")
     */
    public function loginAction(Request $request)
    {
        // TODO
        $session = $request->getSession();

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $session->get(Security::LAST_USERNAME),
            'error' => $error,
        );
    }

    /**
     * @Route("/logout")
     */
    public function logoutAction()
    {
    }

    /**
     * @Route("/login_check")
     */
    public function loginCheckAction()
    {
    }
}
