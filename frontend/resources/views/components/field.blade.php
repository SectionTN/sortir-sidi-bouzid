@props(['label', 'for', 'help' => null])

<div {{ $attributes }}>
    <label for="{{ $for }}" class="block text-sm font-semibold">{{ $label }}</label>

    @if ($help)
        <p class="mt-0.5 text-sm text-stone-500">{{ $help }}</p>
    @endif

    <div class="mt-2">{{ $slot }}</div>

    @error($for)
        <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p>
    @enderror
</div>
