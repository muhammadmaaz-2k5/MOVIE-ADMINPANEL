<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmbedProxyController extends Controller
{
    /**
     * Domains known to have bottom-clipping / cut-off controls on mobile WebViews.
     */
    public static array $cutOffDomains = [
        'fiuosba.com',
        'vidara.so',
    ];

    /**
     * Resolve a stream URL for mobile app or web client consumption.
     * If the URL belongs to a server with cut-off bottom controls (such as fiuosba.com),
     * it automatically transforms it into our server-side responsive wrapper player URL.
     */
    public static function resolveStreamUrl(?string $url): ?string
    {
        if (empty($url)) {
            return $url;
        }

        $trimmed = trim($url);
        $lower = strtolower($trimmed);

        // Already wrapped
        if (str_contains($lower, '/embed/player?') || str_contains($lower, '/embed/fiuosba/')) {
            return $trimmed;
        }

        // Check if this URL needs bottom-control lifting
        foreach (self::$cutOffDomains as $domain) {
            if (str_contains($lower, $domain)) {
                // Ensure embed player format (/e/ instead of /f/, /v/, or /d/)
                if (preg_match('#fiuosba\.com/(?:f|v|d)/([a-zA-Z0-9]+)#i', $trimmed, $m)) {
                    $trimmed = "https://fiuosba.com/e/{$m[1]}";
                }
                
                $baseUrl = config('app.url') ?: url('/');
                return rtrim($baseUrl, '/') . '/embed/player?url=' . urlencode($trimmed);
            }
        }

        return $trimmed;
    }

    /**
     * Fetch stream data directly from fiuosba API.
     */
    public static function fetchFiuosbaStream(string $filecode): ?array
    {
        $clean = preg_replace('/[^a-zA-Z0-9_-]/', '', trim($filecode));
        if (empty($clean)) {
            return null;
        }

        $ch = curl_init('https://fiuosba.com/api/stream');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['filecode' => $clean, 'device' => 'android']));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Referer: https://fiuosba.com/e/' . $clean,
            'User-Agent: Mozilla/5.0 (Linux; Android 13; Mobile) AppleWebKit/537.36'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        $res = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($res && empty($err)) {
            $data = json_decode($res, true);
            if (!empty($data['streaming_url'])) {
                return $data;
            }
        }

        return null;
    }

    /**
     * Render the embed player wrapper view.
     * GET /embed/player?url={targetUrl}&bottom={offsetInPx}
     */
    public function render(Request $request)
    {
        $rawUrl = $request->query('url', '');
        if (empty($rawUrl)) {
            return response('No video stream URL provided', 400);
        }

        $url = trim($rawUrl);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response('Invalid stream URL format', 400);
        }

        $bottom = $request->query('bottom') ?: $request->query('offset');
        $bottom = is_numeric($bottom) ? max(0, min(300, (int)$bottom)) : null;

        // For fiuosba: serve direct native JWPlayer (no iframes, no sandbox traps, perfect mobile sizes & controls)
        if (str_contains(strtolower($url), 'fiuosba.com')) {
            if (preg_match('#fiuosba\.com/(?:e|f|v|d)/([a-zA-Z0-9_-]+)#i', $url, $m)) {
                $streamData = self::fetchFiuosbaStream($m[1]);
                if ($streamData) {
                    return response()
                        ->view('embed.player', [
                            'streamData' => $streamData,
                            'filecode'   => $m[1],
                            'url'        => $url,
                            'bottom'     => $bottom,
                        ])
                        ->header('X-Frame-Options', 'ALLOWALL');
                }
            }
        }

        return response()
            ->view('embed.player', [
                'url'    => $url,
                'bottom' => $bottom,
            ])
            ->header('X-Frame-Options', 'ALLOWALL');
    }

    /**
     * Direct shortcut for fiuosba embed by filecode.
     * GET /embed/fiuosba/{code}?bottom={offsetInPx}
     */
    public function fiuosba(Request $request, string $code)
    {
        $cleanCode = preg_replace('/[^a-zA-Z0-9_-]/', '', trim($code));
        if (empty($cleanCode)) {
            return response('Invalid filecode', 400);
        }

        $targetUrl = "https://fiuosba.com/e/" . $cleanCode;
        $bottom = $request->query('bottom') ?: $request->query('offset');
        $bottom = is_numeric($bottom) ? max(0, min(300, (int)$bottom)) : null;

        $streamData = self::fetchFiuosbaStream($cleanCode);
        return response()
            ->view('embed.player', [
                'streamData' => $streamData,
                'filecode'   => $cleanCode,
                'url'        => $targetUrl,
                'bottom'     => $bottom,
            ])
            ->header('X-Frame-Options', 'ALLOWALL');
    }
}
