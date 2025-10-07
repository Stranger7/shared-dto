<?php

namespace dto;

class Debug extends AbstractDto
{
    public string $exception = '';
    public string $file = '';
    public ?int $line = null;
    public ?array $trace = null;
    public ?array $context = null;
    public ?array $params = null;
}
