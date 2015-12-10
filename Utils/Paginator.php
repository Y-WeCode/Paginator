<?php

namespace YWC\PaginatorBundle\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use YWC\PaginatorBundle\Form\FilterForm;
use YWC\PaginatorBundle\Form\GroupActionForm;
use YWC\PaginatorBundle\Model\Filtrable;
use YWC\PaginatorBundle\Model\Paginable;

class Paginator
{

    private $pageMaxItems;

    private $maxRange;

    private $repository;

    private $items;

    private $page;

    private $title;

    private $route;

    private $formFactory;

    private $groupActions;

    private $groupActionForm;

    private $groupActionFormView;
    
    private $filter;

    private $filterForm;

    private $filterFormView;

    public function __construct($pageMaxItems, $maxRange, $formFactory)
    {
        $this->pageMaxItems = $pageMaxItems;
        $this->maxRange = $maxRange;
        $this->formFactory = $formFactory;
        $this->groupActions = array();
    }

    public function setRepository(Paginable $repository)
    {
        $this->repository = $repository;
    }

    public function getTitle()
    {
        return $this->repository->getDefaultTitle();
    }

    public function getMaxPage()
    {
        return max(1, ceil($this->repository->count()/$this->pageMaxItems));
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

    public function getBaseRoute()
    {
        if(substr($this->route, -5) === 'index') return substr($this->route, 0, -5);

        return false;
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

    public function setFilter(array $filter)
    {
        $this->filter = $filter;
    }

    public function getFilterFormView()
    {
        if(is_null($this->filterFormView)) $this->filterFormView = $this->filterForm->createView();
        
        return $this->filterFormView;
    }

    public function getGroupActionFormView()
    {
        if(is_null($this->groupActionFormView)) $this->groupActionFormView = $this->groupActionForm->createView();
        
        return $this->groupActionFormView;
    }

    public function addGroupAction($callback, $title)
    {
        if(!is_callable($callback)) throw new \Exception('first argument to addGroupAction method must be callable');
        $this->groupActions[] = array(
            'callback' => $callback,
            'title' => $title,
        );
    }

    public function handleRequest(Request $request, $page = null)
    {
        // Set Route and page
        if(is_null($this->route)) $this->route = $request->get('_route');
        if(!is_null($page)) $this->setPage($page);
        
        // Manage Filter
        if($this->repository instanceof Filtrable) {
            $session = $request->getSession();
            $filter = $session->get($this->route.'_filter');
            if(!is_null($filter)) $this->repository->setFilter($filter);
            $this->filterForm = $this->formFactory->create(new FilterForm($this->filter), $filter, array());
            $this->filterForm->handleRequest($request);
            if($this->filterForm->isValid()) {
                $this->repository->setFilter($this->filterForm->getData());
                $session->set($this->route.'_filter', $this->filterForm->getData());
            }
        }

        // Manage GroupActions
        if(count($this->groupActions) > 0) {
            $this->groupActionForm = $this->formFactory->create(new GroupActionForm($this->getItems(), $this->groupActions), null, array());
            $this->groupActionForm->handleRequest($request);
            if($this->groupActionForm->isValid()) {
                $data = $this->groupActionForm->getData();
                foreach($data['entities'] as $id) {
                    $entity = $this->repository->find($id);
                    $action = array_filter($this->groupActions, function($action) use ($data) {
                        return $action['title'] == $data['actions'];
                    });
                    $action = array_pop($action);
                    call_user_func_array($action['callback'], array($entity));
                }
            }
        }
    }    
}
