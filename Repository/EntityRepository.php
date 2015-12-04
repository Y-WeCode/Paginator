<?php

namespace YWC\PaginatorBundle\Repository;

use Doctrine\ORM\EntityRepository as BaseRepository;
use YWC\PaginatorBundle\Model\Paginable;

class EntityRepository extends BaseRepository implements Paginable
{

    protected $orderby;

    public function __construct($em, $class)
    {
        parent::__construct($em, $class);
        $this->orderby = array();
    }

    public function buildQuery()
    {
        $qb = $this->createQueryBuilder('o');
        foreach($this->orderby as $k => $v) $qb->addOrderBy('o'.$k, $v);

        return $qb;
    }    
        
    public function getList($offset, $limit)
    {
        return $this->buildQuery()
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();
    }

    public function count()
    {
        return $this->buildQuery()
                    ->select('COUNT(o)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function setOrderBy(array $orderby)
    {
        $this->orderby = $orderby;
    }

    public function getDefaultTitle()
    {
        return $this->getEntityName();
    }
}