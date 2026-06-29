<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function managerView()
    {
        return view('admin.notification-manager');
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image_url' => 'nullable|url',
            'screen' => 'nullable|string',
            'drama_slug' => 'nullable|string',
            'episode_number' => 'nullable|string',
        ]);

        try {
            $this->sendFCM($request->all());
            return response()->json(['success' => true, 'message' => 'Notification sent successfully via Firebase FCM.']);
        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send notification: ' . $e->getMessage()], 500);
        }
    }

    private function getAccessToken()
    {
        $path = base_path('../firebase-service-account.json');
        if (!file_exists($path)) {
            throw new \Exception("Firebase service account file not found at $path.");
        }
        
        $credentials = json_decode(file_get_contents($path), true);
        
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $payload = json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;

        $signature = '';
        openssl_sign($signatureInput, $signature, $credentials['private_key'], 'sha256WithRSAEncryption');
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        $jwt = $signatureInput . "." . $base64UrlSignature;

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception("Failed to obtain access token: " . $response->body());
    }

    private function sendFCM($data)
    {
        $path = base_path('../firebase-service-account.json');
        $credentials = json_decode(file_get_contents($path), true);
        $projectId = $credentials['project_id'];

        $accessToken = $this->getAccessToken();

        $payload = [
            'message' => [
                'topic' => 'all',
                'data' => [
                    'title' => (string) $data['title'],
                    'body' => (string) $data['body'],
                    'image_url' => (string) ($data['image_url'] ?? ''),
                    'screen' => (string) ($data['screen'] ?? ''),
                    'drama_slug' => (string) ($data['drama_slug'] ?? ''),
                    'episode_number' => (string) ($data['episode_number'] ?? ''),
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json'
        ])->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

        if (!$response->successful()) {
            throw new \Exception("FCM API error: " . $response->body());
        }
    }
}
