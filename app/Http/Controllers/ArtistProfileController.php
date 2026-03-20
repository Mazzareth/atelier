<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesProfileSnapshots;
use App\Models\ProfileModule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArtistProfileController extends Controller
{
    use ManagesProfileSnapshots;

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

    protected function seedDefaultProfile(User $user): void
    {
        $snapshot = collect($this->defaultConfigs())->firstWhere('key', 'starter_classic');

        $this->applyProfileSnapshot($user, [
            'page_layout' => $snapshot['page_layout'] ?? 'classic',
            'modules' => $snapshot['modules'] ?? [],
        ]);
    }

}
