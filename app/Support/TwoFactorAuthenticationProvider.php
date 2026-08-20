<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticationProvider
{
    protected Google2FA $engine;

    public function __construct()
    {
        $this->engine = new Google2FA;
    }

    public function generateSecretKey(): string
    {
        return $this->engine->generateSecretKey();
    }

    public function qrCodeSvg(string $holder, string $secret): string
    {
        $url = $this->engine->getQRCodeUrl(
            config('app.name', 'aboutMe'),
            $holder,
            $secret,
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd,
        );

        $svg = (new Writer($renderer))->writeString($url);

        return trim(Str::after($svg, "\n"));
    }

    public function verify(string $secret, string $code): bool
    {
        return $this->engine->verifyKey($secret, $code, 1);
    }

    /**
     * @return array<int, string>
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        return collect(range(1, $count))
            ->map(fn () => Str::upper(Str::random(4).'-'.Str::random(4).'-'.Str::random(4)))
            ->all();
    }
}
