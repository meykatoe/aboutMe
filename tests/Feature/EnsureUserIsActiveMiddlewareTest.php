<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureUserIsActive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureUserIsActiveMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_suspended_user_is_logged_out_and_forbidden(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        Auth::login($user);

        $request = Request::create('/dashboard', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->setUserResolver(fn () => $user);

        try {
            (new EnsureUserIsActive)->handle($request, fn ($req) => response('ok'));
            $this->fail('Expected a 403 HttpException to be thrown.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }

        $this->assertGuest();
    }

    public function test_guest_is_unaffected_by_the_active_check(): void
    {
        $this->get('/login')->assertOk();
    }
}
