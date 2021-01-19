<?php
namespace App\Extensions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class GroupBuilder extends Builder
{
    public function __construct(QueryBuilder $query)
    {
        parent::__construct($query);
        $this->query->whereIn('allow_group_id', groupIdsAllowUpdate());
    }
}