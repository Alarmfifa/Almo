<?php

namespace Almo\WalletBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Almo\WalletBundle\Entity\Operations;
use Almo\WalletBundle\Entity\Wallet;
use Symfony\Component\HttpFoundation\Request;
use Almo\WalletBundle\Entity\Accounts;
use Almo\WalletBundle\Entity\Payments;
use Almo\WalletBundle\Form\OperationsType;

class WalletController extends Controller
{
    /**
     * @Route("/hello/{name}" )
     * @Template("AlmoWalletBundle:Default:index.html.twig")
     */
    public function indexAction($name)
    {
        return array(
            'name' => $name,
        );
    }

    /**
     * @Route("/", defaults={"act" = "pay"})
     * @Route("/wallet/{act}", defaults={"act" = "pay"}, requirements={"act" = "pay|add|transfer" } )
     *
     * @method ({"GET", "POST"})
     *         @Template("AlmoWalletBundle:Wallet:wallet_form.html.twig")
     */
    public function walletAction($act, Request $req)
    {
        $tagRep = $this->getDoctrine()->getRepository('AlmoWalletBundle:Tags');
        $accRep = $this->getDoctrine()->getRepository('AlmoWalletBundle:Accounts');
        $userId = $this->get('security.token_storage')
            ->getToken()
            ->getUser()
            ->getId();
        $user = $this->get('security.token_storage')
            ->getToken()
            ->getUser();

        $operations = new Operations();
        $operations->addPayment(new Payments());
        $operations->setUserId($user);
        $operations->setDate(new \DateTime());

        if ($act == 'transfer') {
            $operations->addPayment(new Payments());
        }

        $form = $this->createForm(OperationsType::class, $operations, array(
            'tagRep' => $tagRep,
            'userId' => $userId,
            'accRep' => $accRep,
            'act' => $act,
            'user' => $user,
        ));

        if ($req->getMethod() == 'POST') {
            $form->handleRequest($req);

            if ($form->isValid()) {
                if ($operations->getType() != 'add') {
                    $paym = $operations->getPayments();

                    $amount = $paym[0]->getAmount();
                    $paym[0]->setAmount(-1 * $amount);
                }

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($operations);
                // try {
                // TODO need try/catch
                $em->flush();
                // }
                // catch (Exception $e) {

                // }

                $this->addFlash('notice', 'Success!');

                return $this->redirect($this->generateUrl('almo_wallet_wallet_wallet_1', array(
                    'act' => $operations->getType(),
                )));
            }
        }

        return array(
            'form' => $form->createView(),
            'act' => $act,
        );
    }

    /**
     * @Route("/status/{accountId}", defaults={"accountId" = "false"}, requirements={"accountId" = "\d+" } )
     *
     * @method ({"GET"})
     *         @Template("AlmoWalletBundle:Wallet:status.html.twig")
     */
    public function statusAction($accountId)
    {
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository('AlmoWalletBundle:Payments')->getUserAccountsStatus($this->get('security.token_storage')
            ->getToken()
            ->getUser());
        $payments = array();

        if ($accountId) {
            $payments = $em->getRepository('AlmoWalletBundle:Payments')->getUserAccountPayments($accountId, $this->get('security.token_storage')
                ->getToken()
                ->getUser());
        }

        return array(
            'accounts' => $accounts,
            'payments' => $payments,
        );
    }
}
