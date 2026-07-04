<?php
namespace App\Http\Controllers;

use App\Models\ScheduledNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

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
            'item_type' => 'nullable|string|in:movie,tv',
        ]);

        try {
            $this->sendFCMNotification(
                $request->input('title'),
                $request->input('body'),
                $request->input('image_url'),
                $request->input('screen'),
                $request->input('drama_slug'),
                $request->input('episode_number'),
                $request->input('item_type')
            );
            return response()->json(['success' => true, 'message' => 'Notification sent successfully via Firebase FCM.']);
        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send notification: ' . $e->getMessage()], 500);
        }
    }

    // ── CRUD for Scheduled Notifications (Separated templates) ────────────────

    /** GET /admin/api/scheduled-notifications - list templates */
    public function index(Request $request)
    {
        $query = ScheduledNotification::query();
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }
        $templates = $query->orderByDesc('updated_at')->get();
        return response()->json($templates);
    }

    /** POST /admin/api/scheduled-notifications - create template */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:movie,tv',
            'image_type' => 'required|in:manual,tmdb',
            'image' => 'required_if:image_type,manual|image|max:4096',
            'image_path' => 'required_if:image_type,tmdb|nullable|string|max:255',
            'tmdb_id' => 'nullable|string|max:50',
            'screen' => 'nullable|string|max:50',
            'drama_slug' => 'nullable|string|max:255',
            'episode_number' => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'title', 'body', 'type', 'image_type', 'tmdb_id', 'screen', 'drama_slug', 'episode_number'
        ]);

        if ($request->input('image_type') === 'manual') {
            if ($request->hasFile('image')) {
                try {
                    $data['image_path'] = $this->convertAndStoreWebp($request->file('image'));
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => 'Image conversion failed: ' . $e->getMessage()], 422);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Manual image file is required.'], 422);
            }
        } else {
            $data['image_path'] = $request->input('image_path');
        }

        $template = ScheduledNotification::create($data);
        return response()->json($template, 201);
    }

    /** POST /admin/api/scheduled-notifications/{id} - update */
    public function update(Request $request, $id)
    {
        $template = ScheduledNotification::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:movie,tv',
            'image_type' => 'required|in:manual,tmdb',
            'image' => 'nullable|image|max:4096',
            'image_path' => 'required_if:image_type,tmdb|nullable|string|max:255',
            'tmdb_id' => 'nullable|string|max:50',
            'screen' => 'nullable|string|max:50',
            'drama_slug' => 'nullable|string|max:255',
            'episode_number' => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'title', 'body', 'type', 'image_type', 'tmdb_id', 'screen', 'drama_slug', 'episode_number'
        ]);

        if ($request->input('image_type') === 'manual') {
            if ($request->hasFile('image')) {
                try {
                    $oldManualPath = ($template->image_type === 'manual') ? $template->image_path : null;
                    $data['image_path'] = $this->convertAndStoreWebp($request->file('image'), $oldManualPath);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => 'Image conversion failed: ' . $e->getMessage()], 422);
                }
            } else {
                $data['image_path'] = $template->image_path;
            }
        } else {
            if ($template->image_type === 'manual' && $template->image_path) {
                $oldFile = public_path($template->image_path);
                if (file_exists($oldFile) && is_file($oldFile)) {
                    @unlink($oldFile);
                }
            }
            $data['image_path'] = $request->input('image_path');
        }

        $template->update($data);
        return response()->json($template);
    }

    /** DELETE /admin/api/scheduled-notifications/{id} - destroy */
    public function destroy($id)
    {
        $template = ScheduledNotification::findOrFail($id);

        if ($template->image_type === 'manual' && $template->image_path) {
            $oldFile = public_path($template->image_path);
            if (file_exists($oldFile) && is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        $template->delete();
        return response()->json(['success' => true]);
    }

    /** POST /admin/api/scheduled-notifications/{id}/send - send specific */
    public function sendSpecific($id)
    {
        $template = ScheduledNotification::findOrFail($id);

        try {
            $this->sendFCMNotification(
                $template->title,
                $template->body,
                $template->image_path,
                $template->screen,
                $template->drama_slug,
                $template->episode_number,
                $template->type
            );
            return response()->json(['success' => true, 'message' => "Notification template '{$template->title}' sent successfully."]);
        } catch (\Exception $e) {
            Log::error('FCM Send Specific Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send: ' . $e->getMessage()], 500);
        }
    }

    /** POST /admin/api/scheduled-notifications/send-random - send random */
    public function sendRandom(Request $request)
    {
        $request->validate([
            'type' => 'required|in:movie,tv',
        ]);

        $type = $request->input('type');
        $template = ScheduledNotification::where('type', $type)->inRandomOrder()->first();

        if (!$template) {
            return response()->json(['success' => false, 'message' => "No notification templates found for type: {$type}. Please create templates first."], 404);
        }

        try {
            $this->sendFCMNotification(
                $template->title,
                $template->body,
                $template->image_path,
                $template->screen,
                $template->drama_slug,
                $template->episode_number,
                $template->type
            );
            return response()->json(['success' => true, 'message' => "Random {$type} notification '{$template->title}' sent successfully."]);
        } catch (\Exception $e) {
            Log::error('FCM Send Random Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send: ' . $e->getMessage()], 500);
        }
    }

    // ── FCM & Access Token Helpers ────────────────────────────────────────────

    public function sendFCMNotification($title, $body, $imageUrl = null, $screen = null, $dramaSlug = null, $episodeNumber = null, $itemType = null)
    {
        $path = $this->getFirebaseCredentialsPath();
        if (!file_exists($path)) {
            throw new \Exception("Firebase service account file not found at $path.");
        }
        
        $credentials = json_decode(file_get_contents($path), true);
        $projectId = $credentials['project_id'];

        $accessToken = $this->getAccessToken();

        $finalImageUrl = $imageUrl;
        if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
            $trimmed = ltrim($imageUrl, '/');
            $finalImageUrl = url($trimmed);
        }

        $payload = [
            'message' => [
                'topic' => 'all',
                'data' => [
                    'title' => (string) $title,
                    'body' => (string) $body,
                    'image_url' => (string) ($finalImageUrl ?? ''),
                    'screen' => (string) ($screen ?? ''),
                    'drama_slug' => (string) ($dramaSlug ?? ''),
                    'episode_number' => (string) ($episodeNumber ?? ''),
                    'item_type' => (string) ($itemType ?? ''),
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

    private function getAccessToken()
    {
        $path = $this->getFirebaseCredentialsPath();
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

    private function getFirebaseCredentialsPath()
    {
        $configuredPath = env('FIREBASE_CREDENTIALS_PATH');
        if ($configuredPath) {
            if (str_starts_with($configuredPath, '/') || str_contains($configuredPath, ':')) {
                $path = $configuredPath;
            } else {
                $path = base_path($configuredPath);
            }
            if (file_exists($path)) {
                return $path;
            }
        }

        $fallback1 = base_path('firebase-service-account.json');
        if (file_exists($fallback1)) {
            return $fallback1;
        }

        $fallback2 = base_path('../firebase-service-account.json');
        if (file_exists($fallback2)) {
            return $fallback2;
        }

        return $fallback1;
    }

    private function convertAndStoreWebp($file, $oldPath = null)
    {
        $dir = public_path('uploads/notifications');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'notif_' . time() . '_' . uniqid() . '.webp';
        $destination = $dir . '/' . $filename;

        $tempPath = $file->getRealPath();
        $info = getimagesize($tempPath);
        if (!$info) {
            throw new \Exception("Invalid image file.");
        }

        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($tempPath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($tempPath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($tempPath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($tempPath);
                break;
            default:
                throw new \Exception("Unsupported image format: $mime. Only JPEG, PNG, GIF, and WebP are supported.");
        }

        if (!$image) {
            throw new \Exception("Failed to load image. Make sure GD library is enabled.");
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        if (!imagewebp($image, $destination, 80)) {
            imagedestroy($image);
            throw new \Exception("Failed to save image as WebP. Ensure write permissions on public/uploads/notifications.");
        }

        imagedestroy($image);

        if ($oldPath) {
            $oldFile = public_path($oldPath);
            if (file_exists($oldFile) && is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        return '/uploads/notifications/' . $filename;
    }
}
