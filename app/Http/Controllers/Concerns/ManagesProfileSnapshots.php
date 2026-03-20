<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ProfileModule;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait ManagesProfileSnapshots
{
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
