<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class BaseController extends Controller
{
    public function storeFile(Request $request) : JsonResponse
    {
        if (!$request->user('sanctum')) {
            throw new \Exception('Not allowed');
        }

        $request->validate([
            'file' => 'max:131072', // 65536

        ]);

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json([
                'type' => 'image',
                'width' => 768,
                'height' => 492,
                'path' => \env('APP_URL') . '/storage/' . '/uploads/loremipsum.png',
                'size' => 0.1,
                'extension' => 'png',
            ]);
        }

        $mimeType = $file->getMimeType();

        if (\str_contains($mimeType, 'video/')) {
            $fileType = 'video';
        } else if (\str_contains($mimeType, 'image/')) {
            $fileType = 'image';
        } else {
            $fileType = 'file';
        }

        $path = $file->store('uploads', 'public');
        $extension = $file->guessExtension();
        $size = \round($file->getSize() / (1024 * 1024), 1);

        try {
            ImageOptimizer::optimize(__DIR__ . '/../../../storage/app/public/' . $path);
        } catch (\Exception $ex) {
            Log::debug($ex);
        }

        if ($fileType === 'image') {
            [$width, $height] = \getimagesize(__DIR__ . '/../../../storage/app/public/' . $path);
        } else {
            $width = $height = null;
        }

        return response()->json([
            'type' => $fileType,
            'width' => $width,
            'height' => $height,
            'path' => \env('APP_URL') . '/storage/' . $path,
            'size' => $size,
            'extension' => $extension,
        ]);
    }
}
