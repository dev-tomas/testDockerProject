<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestForTest extends TestCase
{
    public function test()
    {
        $tst = new Tst();
        $list = $tst->getList();
        $this->assertEquals(1, $list[1]);
    }
}
