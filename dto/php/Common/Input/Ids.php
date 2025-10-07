<?php

namespace dto\Common\Input;

use Yiisoft\Validator\Rule\Required;

class Ids extends \dto\Input
{
    // Entity IDs
    #[Required]
    public array $ids;
}
