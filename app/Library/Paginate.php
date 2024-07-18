<?php

/**
 *
 */

namespace App\Library;

use stdClass;

class Paginate extends stdClass
{
    public string $draw;
    public int $recordsTotal;
    public int $recordsFiltered;
    public array $data;

    public function __construct($draw, $total, $filter)
    {
        $this->draw            = $draw;
        $this->recordsTotal    = $total;
        $this->recordsFiltered = $filter;
        $this->data            = [];
    }

    public function setRows(array $rows): void
    {
        $this->data = $rows;
    }
}
