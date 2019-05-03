<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Account;
use App\Entity\Operation;
use App\Entity\Payment;
use App\Form\OperationType;
use App\Entity\Tag;
use Knp\Component\Pager\PaginatorInterface;

class WalletController extends AbstractController
{

    /**
     *
     * @Route("/", defaults={"act" = "pay"})
     * @Route("/wallet/{act}", defaults={"act" = "pay"}, requirements={"act" = "pay|add|transfer" }, methods={"GET", "POST"} )
     */
    public function walletAction($act, Request $req)
    {
        $tagRep = $this->getDoctrine()->getRepository(Tag::class);
        $accRep = $this->getDoctrine()->getRepository(Account::class);
        $userId = $this->get('security.token_storage')
            ->getToken()
            ->getUser()
            ->getId();
        $user = $this->get('security.token_storage')
            ->getToken()
            ->getUser();

        $operations = new Operation();
        $operations->addPayment(new Payment());
        $operations->setUserId($user);
        $operations->setDate(new \DateTime());

        if ($act == 'transfer') {
            $operations->addPayment(new Payment());
        }

        $form = $this->createForm(OperationType::class, $operations, array(
            'tagRep' => $tagRep,
            'userId' => $userId,
            'accRep' => $accRep,
            'act' => $act,
            'user' => $user
        ));

        if ($req->getMethod() == 'POST') {
            $form->handleRequest($req);

            if ($form->isValid()) {
                if ($operations->getType() != 'add') {
                    $paym = $operations->getPayments();

                    $amount = $paym[0]->getAmount();
                    $paym[0]->setAmount(- 1 * $amount);
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
                    'act' => $operations->getType()
                )));
            }
        }

        return $this->render('wallet/wallet_form.html.twig', [
            'form' => $form->createView(),
            'act' => $act
        ]);
    }

    /**
     *
     * @Route("/status/{accountId}/{page}", defaults={"accountId" = "false", "page" = 1}, requirements={"accountId" = "\d+", "page" = "\d+|all" }, methods  = "GET" )
     */
    public function statusAction($accountId, $page, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository(Payment::class)->getUserAccountsStatus($this->get('security.token_storage')
            ->getToken()
            ->getUser());

        $limit = 50; // TODO put it into config

        // show list of payments
        if ($accountId) {

            $query = $em->getRepository(Payment::class)->getUserAccountPaymentsQuery($accountId, $this->get('security.token_storage')
                ->getToken()
                ->getUser());

            if ($page == 'all') {
                $payments = $query->getResult();
            } else {
                $payments = $paginator->paginate($query, $page, $limit);
            }
        }

        return $this->render('wallet/status.html.twig', [
            'accounts' => $accounts,
            'payments' => $payments
        ]);
    }
}
