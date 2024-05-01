<?php

namespace Tests\Feature;

use App\Models\VCard;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QrCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_qr_code_returns_svg_string(): void
    {
        $vCard = VCard::create([
            'title' => 'Test Card',
            'bio' => 'Test bio',
            'social_links' => ['twitter' => 'https://twitter.com/test'],
        ]);

        $service = app(QrCodeService::class);
        $result = $service->generate($vCard);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('<svg', $result);
        $this->assertStringContainsString('xmlns="http://www.w3.org/2000/svg"', $result);
    }

    public function test_generate_qr_code_with_avatar_embedded(): void
    {
        Storage::fake('public');

        $avatarContent = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        );

        Storage::disk('public')->put('avatars/test.png', $avatarContent);

        $vCard = VCard::create([
            'title' => 'Test Card',
            'avatar_path' => 'avatars/test.png',
        ]);

        $service = app(QrCodeService::class);
        $result = $service->generate($vCard);

        $this->assertStringContainsString('<svg', $result);
        $this->assertStringContainsString('<image', $result);
        $this->assertStringContainsString('clip-path="url(#avatarClip)"', $result);
        $this->assertStringContainsString('<clipPath', $result);
    }

    public function test_download_qr_code_returns_stream_response(): void
    {
        $vCard = VCard::create([
            'title' => 'Test Card',
        ]);

        $response = $this->get(route('vcard.qr.download', $vCard->slug));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $response->assertHeader('Content-Disposition');
    }
}
