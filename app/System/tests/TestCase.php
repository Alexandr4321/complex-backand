<?php

namespace App\System\Tests;

use App\Auth\Models\User;
use App\System\Swagger\Testing\TestCase as BaseTestCase;
//use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\Assert as PHPUnit;
use Laravel\Passport\Passport;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function authReach()
    {
        $user = User::where('username', 'reach')->first();
        Passport::actingAs($user);

        $token = $user->createToken('App')->accessToken;
        $this->withHeader('Authorization', "Bearer $token");
    }

    public function authPoor()
    {
        $user = User::where('username', 'poor')->first();
        Passport::actingAs($user);

        $token = $user->createToken('App')->accessToken;
        $this->withHeader('Authorization', "Bearer $token");
    }


    // TODO rebase



    /**
     * Assert that the response has a 200 status code.
     * @param TestResponse $response
     * @param string $message
     * @return $this
     */
    public function assertOk($response, $message = '')
    {
        $message = $message ? $message.'. ' : '';
        PHPUnit::assertTrue(
            $response->getStatusCode() === 200,
            $message.'Response status code ['.$response->getStatusCode().'] does not match expected 200 status code.'
        );

        return $this;
    }
}
