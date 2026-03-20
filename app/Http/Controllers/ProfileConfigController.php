<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesProfileSnapshots;
use App\Models\ProfileConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileConfigController extends Controller
{
    use ManagesProfileSnapshots;

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
}
