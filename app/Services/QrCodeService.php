<?php

namespace App\Services;

use App\Models\VCard;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generate(VCard $vCard): string
    {
        $qrCode = QrCode::format('svg')
            ->size(400)
            ->margin(2)
            ->errorCorrection('H')
            ->style('round', 0.5)
            ->eye('circle')
            ->gradient(204, 255, 0, 255, 0, 170, 'diagonal')
            ->backgroundColor(245, 240, 232);

        $svg = $qrCode->generate($vCard->public_url);

        if ($vCard->avatar_path) {
            $svg = $this->embedAvatarInSvg($svg, $vCard->avatar_path);
        }

        return $svg;
    }

    public function generateStream(VCard $vCard)
    {
        $svgContent = $this->generate($vCard);

        return response()->stream(
            function () use ($svgContent) {
                echo $svgContent;
            },
            200,
            [
                'Content-Type' => 'image/svg+xml',
                'Content-Disposition' => 'attachment; filename="qrcode-' . $vCard->slug . '.svg"',
                'Content-Length' => strlen($svgContent),
            ]
        );
    }

    private function embedAvatarInSvg(string $svg, string $avatarPath): string
    {
        $avatarContent = Storage::disk('public')->get($avatarPath);
        $base64Avatar = base64_encode($avatarContent);

        $mimeType = $this->getMimeTypeFromPath($avatarPath);

        $size = 80;
        $center = 200;
        $offset = $size / 2;
        $radius = $size / 2;

        $avatarImage = '<image href="data:' . $mimeType . ';base64,' . $base64Avatar . '" x="' . ($center - $offset) . '" y="' . ($center - $offset) . '" width="' . $size . '" height="' . $size . '" clip-path="url(#avatarClip)" />';

        $clipDef = '<defs><clipPath id="avatarClip"><circle cx="' . $center . '" cy="' . $center . '" r="' . $radius . '" /></clipPath></defs>';

        $svg = preg_replace('/<svg([^>]*)>/', '<svg$1>' . $clipDef, $svg, 1);

        $svg = str_replace('</svg>', $avatarImage . '</svg>', $svg);

        return $svg;
    }

    private function getMimeTypeFromPath(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };
    }
}
