@php
    $text = $module->settings['text'] ?? 'Welcome to my atelier. I draw weird, wonderful, and beautiful things.';

    // Render basic markdown (shared helper)
    $html = e($text);
    $html = preg_replace('/^# (.+)/m', '<h1 class="text-3xl font-bold font-serif text-accent mb-4 mt-8 first:mt-0">$1</h1>', $html);
    $html = preg_replace('/^## (.+)/m', '<h2 class="text-2xl font-bold font-serif text-accent mb-3 mt-6">$1</h2>', $html);
    $html = preg_replace('/^### (.+)/m', '<h3 class="text-xl font-bold font-serif text-accent mb-2 mt-4">$1</h3>', $html);
    $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
    $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
    $html = preg_replace('/`(.+?)`/', '<code class="bg-surface px-1.5 py-0.5 rounded text-accent font-mono text-sm">$1</code>', $html);
    $html = preg_replace('/!\[(.*?)\]\((.+?)\)/', '<img src="$2" alt="$1" class="max-w-full rounded-lg my-4 border border-border">', $html);
    $html = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2" target="_blank" class="text-accent hover:underline">$1</a>', $html);
    $html = preg_replace('/^&gt; (.+)/m', '<blockquote class="border-l-4 border-accent pl-4 py-2 text-muted bg-surface/50 rounded-r my-4 italic">$1</blockquote>', $html);
    $html = preg_replace('/^- (.+)/m', '<li class="ml-6 list-disc mb-1">$1</li>', $html);
    $html = nl2br($html);
@endphp

<x-profile-module :module="$module" :isEditMode="$isEditMode ?? false" type="bio" class="group">
    <x-card padding="lg" class="border-l-4 border-l-accent relative overflow-hidden">
        {{-- Decorative element for extreme/mommy themes via CSS pseudo-elements if needed, handled by tokens.css --}}
        
        <div class="font-mono text-xs uppercase tracking-widest text-accent mb-4 opacity-70">
            About
        </div>
        
        <div class="prose prose-invert max-w-none text-main leading-relaxed">
            {!! $html !!}
        </div>
    </x-card>
</x-profile-module>
