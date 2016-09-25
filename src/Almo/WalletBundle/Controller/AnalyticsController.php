<?php

namespace Almo\WalletBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AnalyticsController extends Controller
{
    /**
     * @Route("/analytics/")
     * @Method({"GET"})
     * @Template("AlmoWalletBundle:Analytics:analytics.html.twig")
     */
    public function indexAction(Request $req)
    {
        // get filter data from url
        // TODO maybe put into route
        $tagId = $req->query->get('tagId', false);
        $dateStart = $req->query->get('dateStart', false);
        $dateFinish = $req->query->get('dateFinish', false);

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $rep = $this->getDoctrine()->getManager()->getRepository('AlmoWalletBundle:Payments');

        // get available user tags
        // TODO maybe get tags directly from the entity (not from operation list)
        $tags = $rep->getUserPaymentsTagsQuery($user)->getResult();

        $qb = $rep->getAllUserPaymentsQueryBuilder($user);

        // add filter data to the query
        if ($tagId) {
            $qb->andWhere('o.tagId = :tagId')->setParameter('tagId', $tagId);
        }
        if ($dateStart) {
            $qb->andWhere('o.date > :dateStart')->setParameter('dateStart', $dateStart);
        }
        if ($dateFinish) {
            $qb->andWhere('o.date < :dateFinish')->setParameter('dateFinish', $dateFinish);
        }
        $payments = $qb->getQuery()->getResult();

        // total sum (with filters)
        $total = $qb->addSelect('SUM(p.amount) AS total')->groupBy('p.currencyId')->getQuery()->getResult();

        return ['payments' => $payments, 'tags' => $tags, 'totalArr' => $total];
    }
}
