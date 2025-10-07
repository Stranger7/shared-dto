<?php

namespace dto\Common\Input;

use Yiisoft\Validator\Rule\Required;

class Id extends \dto\Input
{
    // Entity ID
    #[Required]
    public int $id;
}
