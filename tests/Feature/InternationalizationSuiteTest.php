<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternationalizationSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'is_supreme_admin' => 1,
            'locale' => 'en',
        ]);
    }

    /**
     * Test 1: Language switcher works for all 9 supported languages and dispatches cookie
     */
    public function test_all_supported_languages_swap_successfully(): void
    {
        $locales = ['en', 'ml', 'hi', 'ar', 'fr', 'de', 'ta', 'kn', 'it'];

        foreach ($locales as $locale) {
            $response = $this->get("/lang/{$locale}");
            $response->assertRedirect();
            $response->assertSessionHas('locale', $locale);
            $response->assertCookie('akmart_locale', $locale);
        }
    }

    /**
     * Test 2: Authenticated user language change persists to database
     */
    public function test_authenticated_user_locale_persists_to_database(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/lang/ml');
        $response->assertRedirect();

        $this->user->refresh();
        $this->assertEquals('ml', $this->user->locale);
    }

    /**
     * Test 3: Arabic locale dynamically configures RTL direction
     */
    public function test_arabic_locale_configures_rtl_layout(): void
    {
        $this->actingAs($this->user);

        $response = $this->withSession(['locale' => 'ar'])->get('/admin/dashboard');
        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('lang="ar"', false);
    }

    /**
     * Test 4: AI Copilot endpoint responds in active language
     */
    public function test_ai_copilot_multilingual_chat(): void
    {
        $this->actingAs($this->user);

        // Malayalam AI response test
        $responseMl = $this->postJson(route('app-ai-copilot-chat'), [
            'prompt' => 'stock inventory',
            'locale' => 'ml',
        ]);
        $responseMl->assertOk();
        $responseMl->assertJson(['success' => true]);
        $this->assertStringContainsString('സ്റ്റോക്ക്', $responseMl->json('response'));

        // Hindi AI response test
        $responseHi = $this->postJson(route('app-ai-copilot-chat'), [
            'prompt' => 'stock report',
            'locale' => 'hi',
        ]);
        $responseHi->assertOk();
        $responseHi->assertJson(['success' => true]);
        $this->assertStringContainsString('स्टॉक', $responseHi->json('response'));
    }

    /**
     * Test 5: Translation Audit CLI command runs with 100% parity
     */
    public function test_translation_audit_artisan_command(): void
    {
        $this->artisan('akmart:translation-audit')
            ->expectsOutputToContain('AK-MART GLOBAL TRANSLATION PARITY AUDIT')
            ->expectsOutputToContain('100%')
            ->assertExitCode(0);
    }
}
