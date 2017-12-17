<?php

namespace SlimX\SelfTests\Controllers;

use PHPUnit\Framework\TestCase;
use SlimX\Controllers\Action;

class ActionTest extends TestCase
{
    public function testNotAllowedMethod()
    {
        $this->expectException(\Exception::class);
        $action = new Action(
            'NOTALLOWEDMETHOD',
            '/',
            function ($req, $res, $args) {
                return $res;
            }
        );
    }

    public function testOneFunctionCallable()
    {
        $func = function ($req, $res, $args) {
            return $res;
        };
        $action = new Action(
            'GET',
            '/',
            $func
        );

        $this->assertEquals('GET', $action->getMethod());
        $this->assertEquals('/', $action->getPattern());
        $this->assertEquals($func, $action->getCallable());
        // Make sure extra calls to getCallable doesn't chage its value.
        $this->assertEquals($func, $action->getCallable());
        $this->assertEquals($func, $action->getCallable());
    }
}
