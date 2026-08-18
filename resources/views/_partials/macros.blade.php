@php
    $customLogo = \App\Models\StoreSetting::get('site_logo');
    $height = $height ?? '36';
@endphp

<span class="app-brand-logo d-inline-flex align-items-center">
    @if($customLogo)
        <img src="{{ asset($customLogo) }}" alt="AK-Mart Logo Icon" height="{{ $height }}" class="ak-brand-icon rounded-3">
    @else
        <img src="{{ asset('images/brand/ak-mart-cartoon-logo.png') }}" alt="AK-Mart Cartoon Mascot Logo" height="{{ $height }}" class="ak-brand-icon rounded-3" style="object-fit: contain;">
    @endif
</span>
