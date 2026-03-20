<?php

namespace App\Http\Controllers;

use App\Models\ProfileModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GalleryController extends Controller
{
    public function uploadGalleryImages(Request $request, $id)
    {
        $module = ProfileModule::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->where('type', 'gallery_feed')
            ->firstOrFail();

        $payload = $request->validate([
            'images' => ['required', 'array', 'max:12'],
            'images.*' => ['image', 'max:10240'],
        ]);

        $settings = $module->settings ?? [];
        $existing = collect($settings['images'] ?? []);

        $uploaded = collect($request->file('images', []))
            ->filter()
            ->map(function ($file) {
                $path = $file->store('gallery-images', 'public');

                return [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            });

        $settings['images'] = $existing->concat($uploaded)->take(24)->values()->all();
        $module->update(['settings' => $settings]);

        return back()->with('status', 'Gallery updated.');
    }

    public function showGalleryImage($id, int $imageIndex): StreamedResponse
    {
        $module = ProfileModule::where('id', $id)
            ->where('type', 'gallery_feed')
            ->firstOrFail();

        $images = collect($module->settings['images'] ?? [])->values();
        $image = $images->get($imageIndex);

        abort_unless(is_array($image) && ! empty($image['path']), 404);
        abort_unless(Storage::disk('public')->exists($image['path']), 404);

        return Storage::disk('public')->response(
            $image['path'],
            $image['name'] ?? basename($image['path'])
        );
    }

    public function deleteGalleryImage(Request $request, $id, int $imageIndex)
    {
        $module = ProfileModule::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->where('type', 'gallery_feed')
            ->firstOrFail();

        $settings = $module->settings ?? [];
        $images = collect($settings['images'] ?? [])->values();
        $image = $images->get($imageIndex);

        abort_unless(is_array($image), 404);

        if (! empty($image['path'])) {
            Storage::disk('public')->delete($image['path']);
        }

        $settings['images'] = $images->reject(fn ($_image, $index) => $index === $imageIndex)->values()->all();
        $module->update(['settings' => $settings]);

        return back()->with('status', 'Gallery image removed.');
    }
}
