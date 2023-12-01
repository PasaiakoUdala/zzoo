<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Atala;
use AppBundle\Entity\Azpiatala;
use Doctrine\DBAL\Query\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\AppBundle;
use AppBundle\Entity\Ordenantza;
use AppBundle\Entity\Udala;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/ordenantza/{id}", name="ordenantzabat")
     * @param Request $request
     * @param         $id
     *
     * @return Response
     */
    public function ordenantzabatAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Ordenantza $ordenantza */
        //$ordenantza = $em->getRepository('AppBundle:Ordenantza')->find($id);
        $ordenantza = $em->getRepository('AppBundle:Ordenantza')->getOrdenantzabat($id);

        /** @var Udala $udala */
        $udala = $ordenantza->getUdala();

        return $this->render('default\ordenantza.html.twig', array(
            'ordenantza' => $ordenantza,
            'udala' => $udala
        ));
    }

  /**
   * @Route("/html/{udala}", name="homepage")
   * @param         $udala
   *
   * @return Response
   */
    public function htmlAction($udala)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $udala Udala */
        $udala = $em->getRepository('AppBundle:Udala')->findOneBy(array('kodea' => $udala));

        /** @var $ordenantzak Ordenantza */
        $ordenantzak = $em->getRepository('AppBundle:Ordenantza')->findBy(
            array(
                'udala' => $udala->getId(),
            )
            , array('kodea' => 'ASC')
        );

        return $this->render('default\index.html.twig', array(
            'ordenantzas' => $ordenantzak,
            'udala' => $udala,
        ));
    }

    /**
   * @Route("/html2/{udala}", name="homepage2")
   * @param         $udala
   *
   * @return Response
   */
    public function htm2lAction($udala)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $udala Udala */
        $udala = $em->getRepository('AppBundle:Udala')->findOneBy(array('kodea' => $udala));

        //$filter = $this->get('app.doctrine.filter.configurator');


        /** @var  $query QueryBuilder */
        $query = $em->createQuery(
            /** @lang text */
              '
                    SELECT o
                    FROM AppBundle:Ordenantza o
                       INNER JOIN o.udala u
                    WHERE u.kodea = :udalkodea
                    ORDER BY o.kodea
              '
            );

//        /** @lang text */
//        '
//            SELECT o, u , op, a , ap , aa , aap, k, aapo
//            FROM AppBundle:Ordenantza o
//               INNER JOIN o.udala u
//               LEFT JOIN o.parrafoak op
//               LEFT JOIN o.atalak a
//               LEFT JOIN a.parrafoak ap
//               LEFT JOIN a.azpiatalak aa
//               LEFT JOIN aa.parrafoak aap
//               LEFT JOIN aa.kontzeptuak k
//               LEFT JOIN aa.parrafoakondoren aapo
//            WHERE u.id = :udalkodea
//              AND ((o.ezabatu IS NULL) or (o.ezabatu <> 1))
//              AND ((op.ezabatu IS NULL) or (op.ezabatu <> 1))
//              AND ((a.ezabatu IS NULL) or (a.ezabatu <> 1))
//              AND ((ap.ezabatu IS NULL) or (ap.ezabatu <> 1))
//              AND ((aa.ezabatu IS NULL) or (aa.ezabatu <> 1))
//              AND ((aap.ezabatu IS NULL) or (aap.ezabatu <> 1))
//              AND ((k.ezabatu IS NULL) or (k.ezabatu <> 1))
//              AND ((aapo.ezabatu IS NULL) or (aapo.ezabatu <> 1))
//            ORDER BY o.kodea ASC, a.kodea ASC, aa.kodea ASC, k.kodea ASC
//            '
//        );




        $query->setParameter('udalkodea', $udala->getId());
        $ordenantzak = $query->getResult();

        return $this->render('default\index.html.twig', array(
            'ordenantzas' => $ordenantzak,
            'udala' => $udala,
        ));
    }


    /**
     * @Route("/html3", name="homepage3")
     *
     * @return Response
     */
    public function htm3lAction()
    {
        $em = $this->getDoctrine()->getManager();
        $filter = $em->getFilters()->disable("ezabatu_marka");


        /** @var  $query QueryBuilder */
        $query = $em->createQuery(
        /** @lang text */
            '
            SELECT o
            FROM AppBundle:Ordenantza o
               INNER JOIN o.udala u
            WHERE u.kodea = :udalkodea and o.deletedAt is NULL
            ORDER BY o.kodea
            '
        );

        $query->setParameter('udalkodea', '064');
        $ordenantzak = $query->getResult();

        $resp = [];

        /** @var Ordenantza $ordenantza */
        foreach ($ordenantzak as $keyOrdenatza => $ordenantza) {
            $ord = [];
            $ord['id'] = $ordenantza->getId();
            $ord['origenid'] = $ordenantza->getOrigenid();
            $ord['izenburuaes_prod'] = $ordenantza->getIzenburuaesProd();
            $ord['izenburuaeu_prod'] = $ordenantza->getIzenburuaeuProd();
            $ord['kodea_prod'] = $ordenantza->getKodeaProd();
            /** @var Atala $atala */
            foreach ($ordenantza->getAtalak() as $keyAtala => $atala) {
                $atal = [];
                $atal['id'] = $atala->getId();
                $atal['origenid'] = $atala->getOrigenid();
                $atal['kodea_prod'] = $atala->getKodeaProd();
                $atal['izenburuaeu_prod'] = $atala->getIzenburuaeuProd();
                $atal['izenburuaes_prod'] = $atala->getIzenburuaesProd();
                $atal['utsa_prod'] = $atala->getUtsaProd();
                $atal['parrafoak'] = $atala->getParrafoak();


                /** @var Azpiatala $azpiatala */
                foreach ($atala->getAzpiatalak() as $keyAzpiAtala => $azpiatala) {

                    //if (($azpiatala->getKodeaProd()!==null) && ($azpiatala->getIzenburuaeuProd()!==null) && ($azpiatala->getIzenburuaesProd()!==null) ){
                    if (($azpiatala->getIzenburuaeuProd() !== null) && ($azpiatala->getIzenburuaesProd() !== null)) {
                        $azpi = [];
                        $azpi['id'] = $azpiatala->getId();
                        $azpi['izenburuaeu_prod'] = $azpiatala->getIzenburuaeuProd();
                        $azpi['izenburuaes_prod'] = $azpiatala->getIzenburuaesProd();
                        $azpi['kodea_prod'] = $azpiatala->getKodeaProd();
                        $azpi['kontzeptuak'] = $azpiatala->getKontzeptuak();
                        $azpi['parrafoak'] = $azpiatala->getParrafoak();
                        $azpi['parrafoakondoren'] = $azpiatala->getParrafoakondoren();
                        $atal['azpiatalak'][] = $azpi;
                    }
                }
                $ord['atalak'][] = $atal;
            }
            $resp[] = $ord;
        }

        dump($resp);

        return $this->render('default\index.html.twig', array(
            'ordenantzas' => $ordenantzak,
            'udala' => '064',
        ));
    }
}
