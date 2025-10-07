<?php

namespace DTOCompiler\compilers;

interface CompilerInterface
{
    public function render(): self;

    public function save();
}
