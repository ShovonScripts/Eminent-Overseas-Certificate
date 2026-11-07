<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_redirects_to_the_verify_form(): void
    {
        $this->get('/')->assertRedirect('/verify');
    }

    public function test_the_verify_form_is_public(): void
    {
        $this->get('/verify')->assertOk()->assertSee('Verify a certificate');
    }
}
