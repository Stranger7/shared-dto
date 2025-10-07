<?php

namespace dto\Common\Output;

use Yiisoft\Validator\Rule\Required;

class Meta extends \dto\Output
{
    // Page number
    #[Required]
    public int $page;

    // Total number of pages
    #[Required]
    public int $pageCount;

    // Number of items per page
    #[Required]
    public int $pageSize;

    // Total number of items
    #[Required]
    public int $totalCount;
}
