<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationAndThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_locale_to_french(): void
    {
        $response = $this->get(route('locale.switch', 'fr'));
        $response->assertSessionHas('locale', 'fr');
        $response->assertRedirect();

        $page = $this->withSession(['locale' => 'fr'])->get(route('books.index'));
        $page->assertOk();
        $page->assertSee('Explorer les Livres');
        $page->assertSee('dir="ltr"', false);
    }

    public function test_user_can_switch_locale_to_arabic_with_rtl(): void
    {
        $response = $this->get(route('locale.switch', 'ar'));
        $response->assertSessionHas('locale', 'ar');
        $response->assertRedirect();

        $page = $this->withSession(['locale' => 'ar'])->get(route('books.index'));
        $page->assertOk();
        $page->assertSee('استكشف الكتب');
        $page->assertSee('dir="rtl"', false);
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $response = $this->get(route('locale.switch', 'invalid_locale'));
        $response->assertSessionMissing('locale');
        $response->assertRedirect();
    }

    public function test_dark_mode_anti_fouc_script_is_present(): void
    {
        $response = $this->get(route('books.index'));
        $response->assertOk();
        $response->assertSee('localStorage.getItem(\'theme\')', false);
    }
}
