<?php

namespace DTOCompiler\tests;

use DTOCompiler\compilers\php\properties\IntProperty;
use DTOCompiler\models\PropertyDescriptor;
use PHPUnit\Framework\TestCase;

class IntegerPropertyTest extends TestCase
{
    public function testCodeCompile(): void
    {
        $definition = (new IntProperty(
            new PropertyDescriptor([
                'name' => 'orgId',
                'type' => 'integer',
                'default' => 1,
                'required' => true,
                'comment' => 'Organization ID',
            ])
        ))->renderDefinition();

        $this->assertSame(
            $definition,
            '    // Organization ID' . PHP_EOL . '    #[Required]' . PHP_EOL . '    public int $orgId = 1;'
        );
    }
}
