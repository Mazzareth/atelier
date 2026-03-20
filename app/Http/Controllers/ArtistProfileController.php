<?php

namespace App\Http\Controllers;

use App\Models\ProfileConfig;
use App\Models\ProfileModule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArtistProfileController extends Controller
{
    /**
     * Browse artists.
     */
    public function browse(Request $request)
    {
        $query = User::where('role', 'artist')
            ->whereNotNull('username')
            ->with(['profileModules' => function ($query) {
                $query->whereIn('type', ['avatar_info', 'bio', 'gallery_feed', 'comm_slots']);
            }]);

        if ($request->user()) {
            $query->where('id', '!=', $request->user()->id);
        }

        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->whereRaw('LOWER(username) LIKE ?', ["%$q%"])
                    ->orWhereRaw('LOWER(name) LIKE ?', ["%$q%"]);
            });
        }

        $artists = $query->orderBy('follower_count', 'desc')
            ->get()
            ->filter(function ($artist) use ($request) {
                $bioModule = $artist->profileModules->firstWhere('type', 'bio');
                $bio = (string) ($bioModule->settings['text'] ?? '');
                $slotsModule = $artist->profileModules->firstWhere('type', 'comm_slots');
                $slotsOpen = (int) ($slotsModule->settings['slots_open'] ?? 0);
                $availability = $request->string('availability')->toString();
                $tag = trim(strtolower($request->string('tag')->toString()));

                if ($availability === 'open' && $slotsOpen < 1) {
                    return false;
                }

                if ($availability === 'closed' && $slotsOpen > 0) {
                    return false;
                }

                if ($tag !== '' && !str_contains(strtolower($bio), $tag) && !str_contains(strtolower($artist->name), $tag)) {
                    return false;
                }

                return true;
            })
            ->values();

        $perPage = 12;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedArtists = $artists->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $artists = new LengthAwarePaginator(
            $pagedArtists,
            $artists->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('browse', [
            'artists' => $artists,
            'totalArtists' => $artists->total(),
        ]);
    }

    /**
     * Show the public profile for an artist.
     */
    public function show($username)
    {
        $artist = User::whereRaw('LOWER(username) = ?', [strtolower($username)])->first();

        if (! $artist || ! $artist->isArtist()) {
            abort(404, 'Artist not found or does not have a public atelier.');
        }

        if ($artist->profileModules()->count() === 0) {
            $this->seedDefaultProfile($artist);
        }

        $modules = $artist->profileModules()->orderBy('order')->get()->groupBy('zone');
        $trackerGroups = $artist->receivedCommissionRequests()
            ->with('requester')
            ->where('status', \App\Models\CommissionRequest::STATUS_ACCEPTED)
            ->whereNotNull('tracker_stage')
            ->latest('tracker_stage_updated_at')
            ->latest()
            ->get()
            ->groupBy('tracker_stage');

        $savedConfigs = collect();
        if (auth()->check() && auth()->id() === $artist->id) {
            $savedConfigs = $artist->profileConfigs()->latest()->get();
        }

        return view('profile.show', [
            'artist' => $artist,
            'modules' => $modules,
            'trackerGroups' => $trackerGroups,
            'savedConfigs' => $savedConfigs,
            'defaultConfigs' => $this->defaultConfigs(),
        ]);
    }

    /**
     * Save the dragged layout.
     */
    public function saveLayout(Request $request)
    {
        try {
            $payload = $request->validate([
                'page_layout' => 'nullable|string|in:classic,fixed_left,editorial,stacked,magazine',
                'zones' => 'required|array',
            ]);

            $user = $request->user();

            if (isset($payload['page_layout'])) {
                $user->update(['page_layout' => $payload['page_layout']]);
            }

            $handledExistingIds = [];

            foreach ($payload['zones'] as $zone => $moduleIds) {
                if (! is_array($moduleIds)) {
                    continue;
                }

                foreach ($moduleIds as $index => $id) {
                    if (! $id) {
                        continue;
                    }

                    if (is_array($id) && isset($id['is_new'])) {
                        $created = $user->profileModules()->create([
                            'type' => $id['type'],
                            'zone' => (string) $zone,
                            'order' => (int) $index,
                            'settings' => $id['settings'] ?? $this->defaultSettingsForType($id['type']),
                        ]);
                        $handledExistingIds[] = $created->id;
                        continue;
                    }

                    if (is_string($id) && str_starts_with($id, 'new-')) {
                        $type = str_replace('new-', '', $id);
                        $created = $user->profileModules()->create([
                            'type' => $type,
                            'zone' => (string) $zone,
                            'order' => (int) $index,
                            'settings' => $this->defaultSettingsForType($type),
                        ]);
                        $handledExistingIds[] = $created->id;
                        continue;
                    }

                    $moduleId = (int) $id;
                    ProfileModule::where('id', $moduleId)
                        ->where('user_id', $user->id)
                        ->update(['zone' => (string) $zone, 'order' => (int) $index]);
                    $handledExistingIds[] = $moduleId;
                }
            }

            if (count($handledExistingIds)) {
                ProfileModule::where('user_id', $user->id)
                    ->whereNotIn('id', $handledExistingIds)
                    ->delete();
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::error('Layout save error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function saveConfig(Request $request)
    {
        $payload = $request->validate([
            'name' => 'required|string|max:80',
        ]);

        $user = $request->user();
        $snapshot = $this->buildCurrentProfileSnapshot($user);

        $config = $user->profileConfigs()->create([
            'name' => $payload['name'],
            'page_layout' => $snapshot['page_layout'],
            'modules' => $snapshot['modules'],
            'is_default' => false,
        ]);

        return response()->json([
            'status' => 'saved',
            'config' => [
                'id' => $config->id,
                'name' => $config->name,
            ],
        ]);
    }

    public function loadConfig(Request $request)
    {
        $payload = $request->validate([
            'config_id' => 'nullable|integer',
            'default_key' => 'nullable|string',
        ]);

        $user = $request->user();

        if (! empty($payload['config_id'])) {
            $config = $user->profileConfigs()->findOrFail($payload['config_id']);
            $this->applyProfileSnapshot($user, [
                'page_layout' => $config->page_layout,
                'modules' => $config->modules,
            ]);

            return response()->json(['status' => 'loaded']);
        }

        if (! empty($payload['default_key'])) {
            $default = collect($this->defaultConfigs())->firstWhere('key', $payload['default_key']);
            abort_unless($default, 404, 'Default config not found.');

            $this->applyProfileSnapshot($user, [
                'page_layout' => $default['page_layout'],
                'modules' => $default['modules'],
            ]);

            return response()->json(['status' => 'loaded']);
        }

        return response()->json(['status' => 'error', 'message' => 'No config target provided.'], 422);
    }

    public function deleteConfig(Request $request, $id)
    {
        $request->user()->profileConfigs()->where('id', $id)->delete();
        return response()->json(['status' => 'deleted']);
    }

    /**
     * Delete a module
     */
    public function deleteModule(Request $request, $id)
    {
        ProfileModule::where('id', $id)->where('user_id', $request->user()->id)->delete();
        return response()->json(['status' => 'deleted']);
    }

    /**
     * Update module settings
     */
    public function updateModuleSettings(Request $request, $id)
    {
        $payload = $request->validate(['settings' => 'required|array']);
        ProfileModule::where('id', $id)->where('user_id', $request->user()->id)->update(['settings' => $payload['settings']]);
        return response()->json(['status' => 'updated']);
    }

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

        abort_unless(is_array($image) && !empty($image['path']), 404);
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

        if (!empty($image['path'])) {
            Storage::disk('public')->delete($image['path']);
        }

        $settings['images'] = $images->reject(fn ($_image, $index) => $index === $imageIndex)->values()->all();
        $module->update(['settings' => $settings]);

        return back()->with('status', 'Gallery image removed.');
    }

    protected function seedDefaultProfile(User $user): void
    {
        $snapshot = collect($this->defaultConfigs())->firstWhere('key', 'starter_classic');

        $this->applyProfileSnapshot($user, [
            'page_layout' => $snapshot['page_layout'] ?? 'classic',
            'modules' => $snapshot['modules'] ?? [],
        ]);
    }

    protected function buildCurrentProfileSnapshot(User $user): array
    {
        return [
            'page_layout' => $user->page_layout ?? 'classic',
            'modules' => $user->profileModules()
                ->orderBy('zone')
                ->orderBy('order')
                ->get()
                ->map(fn (ProfileModule $module) => [
                    'type' => $module->type,
                    'zone' => $module->zone,
                    'order' => $module->order,
                    'settings' => $module->settings ?? [],
                ])
                ->values()
                ->all(),
        ];
    }

    protected function applyProfileSnapshot(User $user, array $snapshot): void
    {
        DB::transaction(function () use ($user, $snapshot) {
            $user->update([
                'page_layout' => $snapshot['page_layout'] ?? 'classic',
            ]);

            $user->profileModules()->delete();

            $modules = collect($snapshot['modules'] ?? [])
                ->map(function ($module, $index) {
                    return [
                        'type' => $module['type'],
                        'zone' => $module['zone'],
                        'order' => $module['order'] ?? $index,
                        'settings' => $module['settings'] ?? $this->defaultSettingsForType($module['type']),
                    ];
                })
                ->all();

            if (! empty($modules)) {
                $user->profileModules()->createMany($modules);
            }
        });
    }

    protected function defaultSettingsForType(string $type): array
    {
        return match ($type) {
            'banner' => ['color' => 'var(--bg-panel)', 'image' => ''],
            'avatar_info' => ['show_follow' => true],
            'bio' => ['text' => 'Welcome to my atelier. I draw weird, wonderful, and beautiful things.'],
            'text_block' => ['text' => 'New custom text block.'],
            'gallery_feed' => ['layout' => 'grid', 'images' => []],
            'comm_slots' => ['slots_open' => 3, 'next_open_date' => '', 'base_types' => [], 'addons' => []],
            'kanban_tracker' => ['title' => 'Commission Tracker'],
            'links' => ['links' => ['Twitter' => '#', 'Bluesky' => '#']],
            'tip_jar' => ['message' => 'Drop a coffee in the jar to support my work.', 'emoji' => '☕'],
            default => [],
        };
    }

    protected function defaultConfigs(): array
    {
        return [
            [
                'key' => 'starter_classic',
                'name' => 'Starter Classic',
                'description' => 'A safe default with a banner, intro, gallery, queue, links, and support widgets.',
                'page_layout' => 'classic',
                'modules' => [
                    ['type' => 'banner', 'zone' => 'header', 'order' => 0, 'settings' => ['color' => 'var(--bg-panel)', 'image' => '']],
                    ['type' => 'avatar_info', 'zone' => 'header', 'order' => 1, 'settings' => ['show_follow' => true]],
                    ['type' => 'bio', 'zone' => 'main', 'order' => 0, 'settings' => ['text' => "# Welcome\nTell visitors who you are, what you make, and what kind of commissions you love doing."]],
                    ['type' => 'gallery_feed', 'zone' => 'main', 'order' => 1, 'settings' => ['layout' => 'grid', 'images' => []]],
                    ['type' => 'kanban_tracker', 'zone' => 'main', 'order' => 2, 'settings' => ['title' => 'Commission Tracker']],
                    ['type' => 'comm_slots', 'zone' => 'sidebar', 'order' => 0, 'settings' => ['slots_open' => 3, 'next_open_date' => '', 'base_types' => [], 'addons' => []]],
                    ['type' => 'links', 'zone' => 'sidebar', 'order' => 1, 'settings' => ['links' => ['Twitter' => '#', 'Bluesky' => '#']]],
                    ['type' => 'tip_jar', 'zone' => 'sidebar', 'order' => 2, 'settings' => ['message' => 'Drop a coffee in the jar to support my work.', 'emoji' => '☕']],
                ],
            ],
            [
                'key' => 'minimal_portfolio',
                'name' => 'Minimal Portfolio',
                'description' => 'Cleaner and lighter — good if you want the art and bio to do most of the talking.',
                'page_layout' => 'editorial',
                'modules' => [
                    ['type' => 'avatar_info', 'zone' => 'header', 'order' => 0, 'settings' => ['show_follow' => true]],
                    ['type' => 'bio', 'zone' => 'main', 'order' => 0, 'settings' => ['text' => "# About My Work\nA tidy intro, your specialties, and a little personality go a long way."]],
                    ['type' => 'gallery_feed', 'zone' => 'main', 'order' => 1, 'settings' => ['layout' => 'featured', 'images' => []]],
                    ['type' => 'links', 'zone' => 'sidebar', 'order' => 0, 'settings' => ['links' => ['Portfolio' => '#', 'Bluesky' => '#']]],
                ],
            ],
            [
                'key' => 'commission_focused',
                'name' => 'Commission Focused',
                'description' => 'Puts openings, pricing, and queue visibility front and center.',
                'page_layout' => 'fixed_left',
                'modules' => [
                    ['type' => 'avatar_info', 'zone' => 'header', 'order' => 0, 'settings' => ['show_follow' => true]],
                    ['type' => 'comm_slots', 'zone' => 'main', 'order' => 0, 'settings' => ['slots_open' => 3, 'next_open_date' => '', 'base_types' => [['name' => 'Bust', 'price' => '80', 'type' => 'flat'], ['name' => 'Full Body', 'price' => '180', 'type' => 'flat']], 'addons' => [['name' => 'Extra Character', 'price' => '75', 'type' => 'flat']]]],
                    ['type' => 'bio', 'zone' => 'main', 'order' => 1, 'settings' => ['text' => "## What I Offer\nUse this block to set expectations, turnaround, and communication style."]],
                    ['type' => 'kanban_tracker', 'zone' => 'sidebar', 'order' => 0, 'settings' => ['title' => 'Current Queue']],
                    ['type' => 'links', 'zone' => 'sidebar', 'order' => 1, 'settings' => ['links' => ['Terms of Service' => '#', 'Contact' => '#']]],
                ],
            ],
        ];
    }
}
