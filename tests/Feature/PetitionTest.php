<?php

namespace Tests\Feature;

use App\Mail\VerifySignature;
use App\Models\Signature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PetitionTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Awa',
            'last_name' => 'Dossou',
            'email' => 'Awa.Dossou@example.com',
            'city' => 'Porto-Novo',
            'profile' => 'developpeur',
            'comment' => 'Un hackathon à Porto-Novo, vite !',
            'show_name' => '1',
            'consent' => '1',
        ], $overrides);
    }

    public function test_home_page_shows_petition(): void
    {
        $this->get('/')->assertOk()->assertSee('Porto-Novo')->assertSee('Je signe la pétition');
    }

    public function test_signing_sends_verification_and_is_not_counted_until_confirmed(): void
    {
        Mail::fake();

        $this->post('/signer', $this->payload())->assertRedirect(route('petition.thanks'));

        $s = Signature::firstOrFail();
        $this->assertSame('awa.dossou@example.com', $s->email);
        $this->assertNull($s->verified_at);
        $this->assertSame(0, Signature::counted()->count());
        Mail::assertSent(VerifySignature::class);

        $url = URL::temporarySignedRoute('petition.verify', now()->addDay(), ['signataire' => $s->id]);
        $this->get($url)->assertRedirect(route('petition.thanks'));

        $this->assertNotNull($s->fresh()->verified_at);
        $this->assertSame(1, Signature::counted()->count());
        $this->get('/')->assertSee('Awa D.');
    }

    public function test_verification_link_requires_valid_signature(): void
    {
        $s = Signature::create($this->payload(['email' => 'x@example.com']));
        $this->get('/confirmer/'.$s->id)->assertForbidden();
        $this->assertNull($s->fresh()->verified_at);
    }

    public function test_same_email_cannot_sign_twice(): void
    {
        Mail::fake();
        Signature::create($this->payload(['email' => 'awa.dossou@example.com', 'verified_at' => now()]));

        $this->post('/signer', $this->payload())->assertRedirect();
        $this->assertSame(1, Signature::count());
    }

    public function test_validation_and_consent_are_required(): void
    {
        $this->post('/signer', $this->payload(['consent' => null, 'email' => 'pas-un-email']))
            ->assertSessionHasErrors(['consent', 'email']);
        $this->assertSame(0, Signature::count());
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post('/signer', $this->payload(['website' => 'spam']))->assertRedirect();
        $this->assertSame(0, Signature::count());
    }

    public function test_hidden_name_is_anonymous_and_email_never_public(): void
    {
        Signature::create($this->payload(['email' => 'secret@example.com', 'show_name' => false, 'verified_at' => now()]));

        $this->get('/signataires')->assertOk()
            ->assertSee('Signataire anonyme')
            ->assertDontSee('secret@example.com')
            ->assertDontSee('Dossou');
    }

    public function test_admin_requires_credentials(): void
    {
        config(['petition.admin_password' => 'motdepasse']);

        $this->get('/admin')->assertUnauthorized();
        $this->get('/admin', ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'faux'])->assertUnauthorized();
        $this->get('/admin', ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'motdepasse'])->assertOk();
    }

    public function test_admin_disabled_without_password(): void
    {
        config(['petition.admin_password' => null]);
        $this->get('/admin')->assertNotFound();
    }

    public function test_admin_can_export_and_delete(): void
    {
        config(['petition.admin_password' => 'motdepasse']);
        $auth = ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'motdepasse'];
        $s = Signature::create($this->payload(['email' => 'awa@example.com', 'verified_at' => now()]));

        $csv = $this->get('/admin/export.csv', $auth)->assertOk()->streamedContent();
        $this->assertStringContainsString('awa@example.com', $csv);
        $this->assertStringContainsString('Porto-Novo', $csv);

        $this->delete('/admin/signatures/'.$s->id, [], $auth)->assertRedirect();
        $this->assertSame(0, Signature::count());
    }
}
