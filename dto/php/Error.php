<?php

namespace dto;

class Error extends Output
{
    public string $module = '';
    public ?int $code = null;
    public string $message = '';
    public string $timestamp = '';
    public ?Debug $debug = null;
}
