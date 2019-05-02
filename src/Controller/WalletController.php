<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Accounts;
use App\Entity\Operations;
use App\Entity\Payments;
use App\Entity\Wallet;
use App\Form\OperationsType;
use App\Entity\Tags;
use Knp\Component\Pager\PaginatorInterface;

class WalletController extends AbstractController
{
    /**
     * @Route("/", defaults={"act" = "pay"})
     * @Route("/wallet/{act}", defaults={"act" = "pay"}, requirements={"act" = "pay|add|transfer" }, methods={"GET", "POST"} )
     */
    public function walletAction($act, Request $req)
    {
        $tagRep = $this->getDoctrine()->getRepository(Tags::class);
        $accRep = $this->getDoctrine()->getRepository(Accounts::class);
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

                return $this->redirect($this->generateUrl('app_wallet_wallet_1', array(
                    'act' => $operations->getType(),
                )));
            }
        }

        return $this->render('wallet/wallet_form.html.twig', [
            'form' => $form->createView(),
            'act' => $act, 
            ]);
    }

    /**
     * @Route("/status/{accountId}/{page}", defaults={"accountId" = "false", "page" = 1}, requirements={"accountId" = "\d+", "page" = "\d+|all" }, methods  = "GET" )
     */
    public function statusAction($accountId, $page, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository('AlmoWalletBundle:Payments')->getUserAccountsStatus($this->get('security.token_storage')->getToken()->getUser());

        $limit = 50;    // TODO put it into config

        // show list of payments
        if ($accountId) {
            $paginator = $this->get('knp_paginator');

            $query = $em->getRepository(Payments::class)->getUserAccountPaymentsQuery($accountId, $this->get('security.token_storage')->getToken()->getUser());

            if ($page == 'all') {
                $payments = $query->getResult();
            } else {
                $payments = $paginator->paginate($query, $page, $limit);
            }
        }

        return $this->render('wallet/status.html.twig', ['accounts' => $accounts, 'payments' => $payments]);
    }
}
