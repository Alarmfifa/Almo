<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SecurityController extends Controller
{

    /**
     *
     * @Route("/login")
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

        return $this->render('security/login.html.twig', [
            'last_username' => $session->get(Security::LAST_USERNAME),
            'error' => $error
        ]);
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
