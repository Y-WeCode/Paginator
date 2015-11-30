<?php

namespace YWC\PaginatorBundle\Utils\Model;

interface Paginable
{
    public function getList($offset, $limit);

    public function count();

    public function getDefaultTitle();
}
    