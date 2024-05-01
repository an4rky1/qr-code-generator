<?php

namespace Tests\Feature;

use App\Models\VCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class VCardEndToEndTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_vcard_creation_flow(): void
    {
        Livewire::test('v-card-constructor')
            ->set('title', 'Cyber Dev')
            ->set('bio', 'Full-stack hacker')
            ->set('socialLinks.github', 'https://github.com/cyberdev')
            ->set('socialLinks.telegram', 'https://t.me/cyberdev')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('vcard-created');

        $vCard = VCard::latest()->first();

        $this->assertNotNull($vCard);
        $this->assertEquals('Cyber Dev', $vCard->title);
        $this->assertEquals('Full-stack hacker', $vCard->bio);
        $this->assertEquals('https://github.com/cyberdev', $vCard->social_links->get('github'));
        $this->assertEquals('https://t.me/cyberdev', $vCard->social_links->get('telegram'));
        $this->assertNull($vCard->social_links->get('twitter'));
    }

    public function test_full_flow_with_avatar(): void
    {
        Storage::fake('public');

        $uploaded = TemporaryUploadedFile::fake()->image('avatar.png');

        Livewire::test('v-card-constructor')
            ->set('title', 'Avatar User')
            ->set('avatar', $uploaded)
            ->call('save')
            ->assertHasNoErrors();

        $vCard = VCard::latest()->first();

        $this->assertNotNull($vCard->avatar_path);
        Storage::disk('public')->assertExists($vCard->avatar_path);
    }

    public function test_public_vcard_page_renders_correctly(): void
    {
        $vCard = VCard::create([
            'title' => 'Public User',
            'bio' => 'Visible to the world',
            'social_links' => [
                'github' => 'https://github.com/publicuser',
                'linkedin' => 'https://linkedin.com/in/publicuser',
            ],
        ]);

        $response = $this->get(route('vcard.show', $vCard->slug));

        $response->assertStatus(200);
        $response->assertSee('Public User');
        $response->assertSee('Visible to the world');
        $response->assertSee('https://github.com/publicuser');
        $response->assertSee('https://linkedin.com/in/publicuser');
    }

    public function test_qr_download_returns_valid_image(): void
    {
        $vCard = VCard::create([
            'title' => 'QR Test',
        ]);

        $response = $this->get(route('vcard.qr.download', $vCard->slug));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $response->assertHeader('Content-Disposition');
    }

    public function test_nonexistent_slug_returns_404(): void
    {
        $response = $this->get(route('vcard.show', 'nonexistent-slug'));

        $response->assertStatus(404);
    }

    public function test_qr_preview_updates_reactively(): void
    {
        Livewire::test('v-card-constructor')
            ->assertSet('title', '')
            ->assertSee('Введите имя')
            ->set('title', 'Reactive Test')
            ->assertSee('<svg', escape: false);
    }

    public function test_empty_social_links_are_filtered(): void
    {
        Livewire::test('v-card-constructor')
            ->set('title', 'Minimal User')
            ->set('socialLinks.github', 'https://github.com/minimal')
            ->set('socialLinks.twitter', '')
            ->set('socialLinks.linkedin', '   ')
            ->call('save')
            ->assertHasNoErrors();

        $vCard = VCard::latest()->first();

        $this->assertCount(1, $vCard->social_links);
        $this->assertTrue($vCard->social_links->has('github'));
        $this->assertFalse($vCard->social_links->has('twitter'));
        $this->assertFalse($vCard->social_links->has('linkedin'));
    }
}
