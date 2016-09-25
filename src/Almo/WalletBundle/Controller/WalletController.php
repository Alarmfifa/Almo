<?php

namespace Almo\WalletBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Almo\WalletBundle\Entity\Accounts;
use Almo\WalletBundle\Entity\Operations;
use Almo\WalletBundle\Entity\Payments;
use Almo\WalletBundle\Entity\Wallet;
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
     * @Method ({"GET", "POST"})
     * @Template("AlmoWalletBundle:Wallet:wallet_form.html.twig")
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

                $em = $this->getDoctrine()->getManager();
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
     * @Route("/status/{accountId}/{page}", defaults={"accountId" = "false", "page" = 1}, requirements={"accountId" = "\d+", "page" = "\d+|all" } )
     * @Method({"GET"})
     * @Template("AlmoWalletBundle:Wallet:status.html.twig")
     */
    public function statusAction($accountId, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository('AlmoWalletBundle:Payments')->getUserAccountsStatus($this->get('security.token_storage')->getToken()->getUser());

        $limit = 50;    // TODO put it into config

        // show list of payments
        if ($accountId) {
            $paginator = $this->get('knp_paginator');

            $query = $em->getRepository('AlmoWalletBundle:Payments')->getUserAccountPaymentsQuery($accountId, $this->get('security.token_storage')->getToken()->getUser());

            if ($page == 'all') {
                $payments = $query->getResult();
            } else {
                $payments = $paginator->paginate($query, $page, $limit);
            }
        }

        return ['accounts' => $accounts, 'payments' => $payments];
    }
}
