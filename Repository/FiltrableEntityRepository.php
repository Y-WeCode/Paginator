<?php

namespace YWC\PaginatorBundle\Repository;

use YWC\PaginatorBundle\Model\Filtrable;

class FiltrableEntityRepository extends EntityRepository implements Filtrable
{

    protected $filter;

    public function __construct($em, $class)
    {
        parent::__construct($em, $class);
        $this->filter = array();
    }

    public function buildQuery()
    {
        $qb = parent::buildQuery();
        $i = 0;
        foreach($this->filter as $k => $v) {
            if(is_array($v) && count($v) == 0) continue;
            if(is_array($v)) $qb->andWhere('o.'.$k.' IN(:v'.$i.')');
            else $qb->andWhere('o.'.$k.' = :v'.$i);
            $qb->setParameter('v'.$i, $v);
            $i++;
        }

        return $qb;
    }
        
    public function setFilter(array $filter)
    {
        $this->filter = $filter;
    }
    
}