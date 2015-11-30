<?php

namespace YWC\PaginatorBundle\Utils;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use YWC\CommonBundle\Utils\Model\Paginable;

class Paginator
{

    private $pageMaxItems;

    private $maxRange;

    private $repository;

    private $items;

    private $page;

    private $maxPage;

    private $title;

    private $route;

    public function __construct($pageMaxItems, $maxRange)
    {
        $this->pageMaxItems = $pageMaxItems;
        $this->maxRange = $maxRange;
    }

    public function setRepository(Paginable $repository)
    {
        $this->repository = $repository;
    }

    public function getMaxPage()
    {
        if(is_null($this->maxPage)) $this->maxPage = max(1, ceil($this->repository->count()/$this->pageMaxItems));

        return $this->maxPage;
    }

    public function setPage($page)
    {
        if($page < 1 || $page > $this->getMaxPage()) throw new NotFoundHttpException('Not a valid page');
        $this->page = $page;        
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getItems()
    {
        if(is_null($this->items)) $this->items = $this->repository->getList(($this->page-1)*$this->pageMaxItems, $this->pageMaxItems);
        
        return $this->items;
    }

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getPrevious()
    {
        return $this->page - 1;
    }

    public function getNext()
    {
        return $this->page + 1;
    }

    public function getRange()
    {
        return range(max(1, $this->page-$this->maxRange), min($this->getMaxPage(), $this->page+$this->maxRange));
    }

}