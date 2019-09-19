<?php

declare(strict_types=1);

use Tester\Environment;

require __DIR__ . '/../vendor/autoload.php';

Environment::setup();

class FooMockService
{}

class BarMockService
{
    /** @var FooMockService @inject */
    public $foo;
}

class SickMockService
{
    public function __construct()
    {
        throw new \Exception();
    }
}
