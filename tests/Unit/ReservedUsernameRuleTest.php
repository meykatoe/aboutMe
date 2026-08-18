<?php

namespace Tests\Unit;

use App\Rules\ReservedUsername;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ReservedUsernameRuleTest extends TestCase
{
    public function test_it_fails_for_a_reserved_username(): void
    {
        Config::set('reserved-usernames', ['admin']);

        $failed = false;

        (new ReservedUsername)->validate('username', 'admin', function () use (&$failed): void {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_is_case_insensitive(): void
    {
        Config::set('reserved-usernames', ['admin']);

        $failed = false;

        (new ReservedUsername)->validate('username', 'ADMIN', function () use (&$failed): void {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_passes_for_a_non_reserved_username(): void
    {
        Config::set('reserved-usernames', ['admin']);

        $failed = false;

        (new ReservedUsername)->validate('username', 'someone', function () use (&$failed): void {
            $failed = true;
        });

        $this->assertFalse($failed);
    }
}
