<?php

namespace dto\Common\Output;

use Yiisoft\Validator\Rule\Required;

class Count extends \dto\Output
{
    // Count of items
    #[Required]
    public int $count;
}
