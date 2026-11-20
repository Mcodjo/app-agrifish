<?php

namespace Tests\Feature;

use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_public_pages_are_displayed(): void
    {
        foreach ([
            '/' => 'Vos projets agricoles et aquacoles',
            '/services' => 'Agriculture, aquaculture et études techniques',
            '/formation' => 'Des formations pratiques',
            '/projets' => 'Des missions menées sur le terrain',
            '/a-propos' => 'Une équipe de terrain',
            '/contact' => 'Parlons de votre projet',
            '/mentions-legales' => 'Crédits photographiques',
            '/politique-confidentialite' => 'Politique de confidentialité',
        ] as $url => $texte) {
            $this->get($url)->assertOk()->assertSee($texte);
        }
    }

    public function test_unknown_page_returns_404(): void
    {
        $this->get('/page-qui-n-existe-pas')->assertNotFound()->assertSee('Cette page n’existe pas');
    }

    public function test_whatsapp_button_is_hidden_without_a_number(): void
    {
        config(['site.contact.whatsapp' => null]);
        $this->get('/')->assertDontSee('wa.me/', false);

        config(['site.contact.whatsapp' => '2250700000000']);
        $this->get('/')->assertSee('https://wa.me/2250700000000', false);
    }

    public function test_contact_subject_can_be_preselected(): void
    {
        $this->get('/contact?sujet=Formation&formation=Pisciculture+en+bassin')
            ->assertOk()
            ->assertSee('<option value="Formation" selected>', false)
            ->assertSee('Je souhaite m’inscrire à la formation « Pisciculture en bassin ».');
    }

    public function test_contact_form_sends_an_email(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'first_name' => 'Awa',
            'last_name' => 'Traoré',
            'email' => 'awa@example.com',
            'organisation' => 'Coopérative de Kindia',
            'subject' => 'Conseil agricole',
            'message' => 'Nous cherchons un appui pour planifier la prochaine campagne maraîchère.',
            'consent' => '1',
        ])->assertRedirect(route('contact.merci'));

        Mail::assertSent(ContactFormMail::class, fn (ContactFormMail $mail) => $mail->formData['organisation'] === 'Coopérative de Kindia');
    }
}
