<?php

namespace Almo\WalletBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AnalyticsController extends Controller
{
    /**
     * @Route("/analytics/")
     * @Method({"GET"})
     * @Template("AlmoWalletBundle:Analytics:analytics.html.twig")
     */
    public function indexAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $rep = $this->getDoctrine()->getManager()->getRepository('AlmoWalletBundle:Payments');

        // get available user tags
        // TODO maybe get tags directly from the entity (not from operation list)
        $tags = $rep->getUserPaymentsTagsQuery($user)->getResult();

        return ['tags' => $tags];
    }

    /**
     * @Route("/analytics/search/")
     * @Method({"GET"})
     * @Template("AlmoWalletBundle:Analytics:analytics.html.twig")
     */
    public function historyAction(Request $req)
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

    /**
     * @Route("/analytics/graph/")
     * @Method({"GET"})
     * @Template("AlmoWalletBundle:Analytics:graph.html.twig")
     */
    public function graphAction(Request $req)
    {
    }

    /**
     * @Route("/analytics/graph-data/{type}/{group}", defaults={"type" : "pay", "group" : "month"}, requirements={"type" : "pay|add", "group" : "month|year"} )
     * @Method({"GET"})
     */
    public function graphDataAction(Request $req, $type, $group)
    {

        //TODO add filters!

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $rep = $this->getDoctrine()->getManager()->getRepository('AlmoWalletBundle:Payments');

        $qb = $rep->getAllUserPaymentsQueryBuilder($user);

        $total = $qb->addSelect('YEAR(o.date) as ydate, MONTH(o.date) as mdate, SUM(p.amount) AS total')->groupBy('ydate, mdate, o.tagId')
        ->andWhere('o.type = :type')->setParameter('type', $type)->orderBy('o.date',  'ASC')->getQuery()->getResult();

        $resArr = [];
        $resDateArr = [];
        $graphs = [];
        $data = [];

        foreach ($total as $k => $v) {
            $graph = $v[0]->getOperationId()->getTagId()->getTitle();
            if (!\in_array($graph, $graphs)) {
                $graphs[] = $graph;
            }

            $resDateArr[$v[0]->getOperationId()->getDate()->format('Y-m')][$graph] = abs($v['total']);
        }

        foreach ($resDateArr as $k => $v) {
            foreach ($graphs as $graph) {
                $val = (isset($v[$graph])) ? $v[$graph] : 0;
                $resArr[$graph][] = ['x' => $k, 'y' => $val];
            }
        }

        foreach ($resArr as $k => $v) {
            $data[] = ['label' => $k, 'data' => $v, 'lineTension' => 0, 'spanGaps' => false, 'fill' => false];
        }

        return new Response(\json_encode($data));
    }
}
