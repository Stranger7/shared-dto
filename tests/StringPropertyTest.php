<?php

namespace DTOCompiler\tests;

use DTOCompiler\compilers\php\properties\StringProperty;
use DTOCompiler\models\PropertyDescriptor;
use PHPUnit\Framework\TestCase;

class StringPropertyTest extends TestCase
{
    public function testCodeCompile(): void
    {
        $definition = (new StringProperty(
            new PropertyDescriptor([
                'name' => 'role',
                'type' => 'string',
                'default' => 'user',
                'comment' => 'User`s role'
            ])
        ))->renderDefinition();

        $this->assertSame($definition, '    // User`s role' . PHP_EOL . '    public ?string $role = \'user\';');
    }
}
