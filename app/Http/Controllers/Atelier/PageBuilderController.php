<?php

namespace App\Http\Controllers\Atelier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileModule;

class PageBuilderController extends Controller
{
    /**
     * Show the page builder interface.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Seed some default modules if the user has none
        if ($user->profileModules()->count() === 0) {
            $user->profileModules()->createMany([
                ['type' => 'banner', 'zone' => 'header', 'order' => 0, 'settings' => ['color' => 'var(--bg-panel)']],
                ['type' => 'avatar_info', 'zone' => 'header', 'order' => 1, 'settings' => ['show_follow' => true]],
                ['type' => 'bio', 'zone' => 'main', 'order' => 0, 'settings' => ['text' => 'Welcome to my atelier. I draw weird, wonderful, and beautiful things.']],
                ['type' => 'gallery_feed', 'zone' => 'main', 'order' => 1, 'settings' => ['layout' => 'grid']],
                ['type' => 'comm_slots', 'zone' => 'sidebar', 'order' => 0, 'settings' => ['slots_open' => 3, 'next_open_date' => null]],
                ['type' => 'tip_jar', 'zone' => 'sidebar', 'order' => 1, 'settings' => ['message' => 'Drop a coffee in the jar to support my work.']],
            ]);
        }

        $modules = $user->profileModules()->orderBy('order')->get()->groupBy('zone');

        return view('atelier.builder', compact('modules'));
    }

    /**
     * Save module order after drag-and-drop.
     */
    public function saveOrder(Request $request)
    {
        $payload = $request->validate([
            'zones' => 'required|array',
            'zones.*' => 'array', // Array of module IDs in order
        ]);

        foreach ($payload['zones'] as $zone => $moduleIds) {
            foreach ($moduleIds as $index => $id) {
                ProfileModule::where('id', $id)
                    ->where('user_id', $request->user()->id)
                    ->update(['zone' => $zone, 'order' => $index]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
