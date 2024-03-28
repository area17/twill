<?php

namespace A17\Twill\Tests\Unit\Services;

use A17\Twill\Tests\Unit\TestCase;

class UtilsTest extends TestCase
{
    public function testTwillTransCanBeCached()
    {
        $trans = twillTrans('test', ['foo' => 'bar']);
        $exported = 'return ' . var_export($trans, true) . ';';

        $serializedTrans = eval($exported);

        $this->assertEquals($trans, $serializedTrans);
    }
}
