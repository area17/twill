<?php

namespace A17\Twill\Tests\Integration\Exceptions;

use A17\Twill\Tests\Integration\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class ExceptionHandlerTest extends TestCase
{
    public function test404InAdmin()
    {
        $res = $this->get('/twill/foobar');
        $res->assertStatus(404);
        $this->assertEquals($res->original->name(), "twill::errors.404");
    }

    public function test404InFrontend()
    {
        $res = $this->get('/foobar');
        $res->assertStatus(404);
        $this->assertTrue(is_string($res->original));
    }

    public function testValidationException()
    {
        Route::post('/twill/validation-exception', function (Request $request) {
            $request->validate([
                'dummy' => 'required',
            ]);
        });

        $res = $this->postJson('/twill/validation-exception');
        $res->assertStatus(422);
        // Response is directly an array of exceptions and doesn't have a response key
        $res->assertJsonValidationErrors('dummy', null);
    }
}
