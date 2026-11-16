@props(['name', 'id' => null, 'multiple' => false, 'hint' => 'PDF, Word, Excel ou image · 5 Mo max par fichier', 'compact' => false])

@php $id ??= str_replace(['[', ']'], '', $name).'-'.uniqid(); @endphp

<div x-data="{ files: [], dragging: false,
        sync(e) { this.files = Array.from(e.target.files).map(f => ({ name: f.name, size: f.size < 1048576 ? Math.max(1, Math.round(f.size / 1024)) + ' Ko' : (f.size / 1048576).toFixed(1) + ' Mo' })) } }">
    <label for="{{ $id }}"
           @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
           @drop.prevent="dragging = false; $refs.input.files = $event.dataTransfer.files; $refs.input.dispatchEvent(new Event('change'))"
           :class="dragging ? 'border-primary bg-primary-50' : 'border-gray-300 bg-white hover:border-primary hover:bg-primary-50/40'"
           class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-dashed transition {{ $compact ? 'px-3 py-3' : 'px-4 py-5' }}">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.17 7l-6.58 6.59a2 2 0 102.83 2.83l6.41-6.59a4 4 0 00-5.66-5.66l-6.4 6.58a6 6 0 108.49 8.49L20.5 13"/></svg>
        </span>
        <span class="min-w-0 text-sm">
            <span class="font-semibold text-primary">{{ $multiple ? 'Ajouter des fichiers' : 'Ajouter un fichier' }}</span>
            <span class="hidden text-gray-500 sm:inline"> ou glissez-les ici</span>
            <span class="block text-xs text-gray-500">{{ $hint }}</span>
        </span>
        <input x-ref="input" id="{{ $id }}" type="file" name="{{ $name }}" @if($multiple) multiple @endif
               accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" class="sr-only" @change="sync($event)">
    </label>
    <ul x-show="files.length" x-cloak class="mt-2 space-y-1">
        <template x-for="file in files" :key="file.name">
            <li class="flex items-center justify-between gap-3 rounded-lg bg-cream px-3 py-2 text-sm">
                <span class="truncate text-dark" x-text="file.name"></span>
                <span class="shrink-0 text-xs text-gray-500" x-text="file.size"></span>
            </li>
        </template>
    </ul>
</div>
