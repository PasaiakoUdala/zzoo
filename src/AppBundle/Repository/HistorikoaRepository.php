<?php

namespace AppBundle\Repository;


class HistorikoaRepository extends \Doctrine\ORM\EntityRepository
{
    public function getHistorikoa($udalaid)
    {
        $qm = $this->createQueryBuilder('h')
            ->innerJoin('h.udala', 'u')
            ->andWhere('h.deletedAt is not null')
            ->andWhere('u.id = :udalaid')->setParameter('udalaid',$udalaid)
            ->orderBy('h.bogargitaratzedata', 'DESC')
        ;

        return $qm->getQuery()->getResult();
    }

}
