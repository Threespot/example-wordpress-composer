<?php

namespace Threespot\Tests\Unit;

use Brain\Monkey\Functions;
use Threespot\Tests\BrainMonkeyTestCase;

/**
 * Smoke test proving the harness works: PHPUnit discovers the suite, the
 * autoload-dev namespace resolves, and Brain Monkey stubs WP functions.
 * Replace with real tests as project code grows.
 */
class ExampleTest extends BrainMonkeyTestCase
{
    public function test_brain_monkey_stubs_wp_functions(): void
    {
        Functions\when('esc_html')->returnArg();

        $this->assertSame('example', esc_html('example'));
    }
}
