<?php

namespace App\Services;

use App\InteractionType;
use App\Models\QrCode;
use App\Models\VCard;
use App\Models\VCardLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InteractionRecorder
{
    public function record(
        Request $request,
        VCard $vCard,
        InteractionType $type,
        ?QrCode $qrCode = null,
        ?VCardLink $link = null,
    ): void {
        $userAgent = Str::lower((string) $request->userAgent());
        $ip = $request->ip();

        $vCard->interactions()->create([
            'qr_code_id' => $qrCode?->id,
            'v_card_link_id' => $link?->id,
            'event_type' => $type,
            'country' => $this->header($request, ['CF-IPCountry', 'X-Vercel-IP-Country']),
            'city' => $this->header($request, ['CF-IPCity', 'X-Vercel-IP-City']),
            'device' => $this->device($userAgent),
            'browser' => $this->browser($userAgent),
            'ip_hash' => $ip ? hash_hmac('sha256', $ip, (string) config('app.key')) : null,
            'occurred_at' => now(),
        ]);
    }

    /**
     * @param  array<int, string>  $names
     */
    private function header(Request $request, array $names): ?string
    {
        foreach ($names as $name) {
            $value = trim((string) $request->header($name));

            if ($value !== '') {
                return Str::limit($value, 120, '');
            }
        }

        return null;
    }

    private function device(string $userAgent): string
    {
        return match (true) {
            Str::contains($userAgent, ['ipad', 'tablet']) => 'tablet',
            Str::contains($userAgent, ['mobile', 'android', 'iphone']) => 'mobile',
            default => 'desktop',
        };
    }

    private function browser(string $userAgent): string
    {
        return match (true) {
            Str::contains($userAgent, 'edg/') => 'edge',
            Str::contains($userAgent, 'chrome/') => 'chrome',
            Str::contains($userAgent, 'firefox/') => 'firefox',
            Str::contains($userAgent, 'safari/') => 'safari',
            default => 'other',
        };
    }
}
