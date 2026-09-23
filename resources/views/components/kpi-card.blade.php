@props(['label', 'value', 'icon' => 'bi-bar-chart', 'href' => null, 'hint' => 'View details'])

@php $tag = $href ? 'a' : 'div'; @endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif class="kpi-card">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <p class="kpi-label">{{ $label }}</p>
            <p class="kpi-value">{{ $value }}</p>
        </div>
        <i class="bi {{ $icon }} kpi-icon"></i>
    </div>
    @if($href)
        <div class="kpi-drill mt-2"><i class="bi bi-arrow-right-circle"></i> {{ $hint }}</div>
    @endif
</{{ $tag }}>
