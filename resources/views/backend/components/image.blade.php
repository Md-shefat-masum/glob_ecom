@if (isset($aws) && $aws)
    <img src="{{ isset($src) ? asset_url($src) : '' }}" class="{{ $classes ?? '' }}" style="object-fit: cover; object-position: center; {{ $style ?? '' }}" loading="lazy" alt="{{ $alt ?? '' }}">
@else
    <img src="{{ isset($src) ? asset($src) : '' }}" class="{{ $classes ?? '' }}" style="object-fit: cover; object-position: center; {{ $style ?? '' }}" loading="lazy" alt="{{ $alt ?? '' }}">
@endif
