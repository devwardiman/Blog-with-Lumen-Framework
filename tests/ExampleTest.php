<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_base_endpoint_returns_a_successful_response()
    {
        // $this->get('/');

        // $this->assertResponseOk();

        $user = User::find('13');
        // Auth::setUser($user, true);
        $this->actingAs($user);

        $this->get('/api/article');
        $this->assertResponseOk();
    }
}
