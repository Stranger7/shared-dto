<?php

namespace dto\Common\Input;

class Page extends \dto\Input
{
    // from 1 to page count
    public ?int $pageNumber = 1;

    public ?int $perPage = 20;

    /** @var string[] */
    public ?array $sortFields = null;

    /** @var string[] */
    public ?array $sortDirections = null;
}
