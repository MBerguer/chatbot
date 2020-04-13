<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\BotManTester;
use BotMan\BotMan\BotMan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

        /**
         * @var BotMan
         */
        protected $botman;

        /**
         * @var BotManTester
         */
        protected $bot;
}
