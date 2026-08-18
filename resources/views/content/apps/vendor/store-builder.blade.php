@extends('layouts/layoutMaster')

@section('title', __('Visual Store Builder'))

@section('content')
<div class="row">
    {{-- Customization Form --}}
    <div class="col-md-5">
        <div class="card mb-6">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">{{ __('Store Customization') }}</h5>
                <p class="text-muted small mb-0">{{ __('Customize your store look and feel.') }}</p>
            </div>
            <div class="card-body pt-6">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('app-vendor-store-builder-save') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <h6 class="mb-3">{{ __('Branding') }}</h6>
                    <div class="mb-4">
                        <label class="form-label">{{ __('Store Logo') }}</label>
                        <input type="file" name="store_logo" class="form-control" accept="image/*" id="logoInput">
                        @if($theme['store_logo'])
                            <div class="mt-2">
                                <img src="{{ $theme['store_logo'] }}" alt="Logo" class="img-fluid rounded border" style="max-height: 50px;">
                            </div>
                        @endif
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label">{{ __('Primary Color') }}</label>
                            <input type="color" name="theme_primary_color" class="form-control form-control-color w-100" value="{{ $theme['theme_primary_color'] }}" id="primaryColor">
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('Secondary Color') }}</label>
                            <input type="color" name="theme_secondary_color" class="form-control form-control-color w-100" value="{{ $theme['theme_secondary_color'] }}" id="secondaryColor">
                        </div>
                    </div>

                    <hr class="my-6">
                    <h6 class="mb-3">{{ __('Hero Section') }}</h6>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Hero Title') }}</label>
                        <input type="text" name="hero_title" class="form-control" value="{{ $theme['hero_title'] }}" id="heroTitleInput">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">{{ __('Hero Subtitle') }}</label>
                        <textarea name="hero_subtitle" class="form-control" rows="2" id="heroSubtitleInput">{{ $theme['hero_subtitle'] }}</textarea>
                    </div>

                    <hr class="my-6">
                    <h6 class="mb-3">{{ __('Contact & Social') }}</h6>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Support Email') }}</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ $theme['contact_email'] }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Facebook URL') }}</label>
                        <input type="url" name="facebook_url" class="form-control" value="{{ $theme['facebook_url'] }}">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">{{ __('Instagram URL') }}</label>
                        <input type="url" name="instagram_url" class="form-control" value="{{ $theme['instagram_url'] }}">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-save me-1"></i> {{ __('Save Changes') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Live Preview --}}
    <div class="col-md-7">
        <div class="card mb-6">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('Live Preview') }}</h5>
                <span class="badge bg-label-info">{{ __('Store View') }}</span>
            </div>
            <div class="card-body p-0">
                <div id="previewContainer" class="rounded-bottom overflow-hidden border">
                    {{-- Preview Header --}}
                    <nav class="p-4 d-flex justify-content-between align-items-center bg-white border-bottom">
                        <div id="previewLogo">
                            @if($theme['store_logo'])
                                <img src="{{ $theme['store_logo'] }}" alt="Logo" style="max-height: 30px;">
                            @else
                                <h4 class="mb-0" style="color: {{ $theme['theme_primary_color'] }}">{{ __('LOGO') }}</h4>
                            @endif
                        </div>
                        <div class="d-flex gap-3 small text-muted">
                            <span>{{ __('Home') }}</span>
                            <span>{{ __('Products') }}</span>
                            <span>{{ __('Contact') }}</span>
                        </div>
                    </nav>

                    {{-- Preview Hero --}}
                    <div id="previewHero" class="p-10 text-center text-white" style="background-color: {{ $theme['theme_primary_color'] }}">
                        <h1 class="display-5 fw-bold mb-3" id="previewTitle" style="color: #fff">{{ $theme['hero_title'] }}</h1>
                        <p class="lead mb-6" id="previewSubtitle">{{ $theme['hero_subtitle'] }}</p>
                        <button class="btn btn-lg px-8 border-0" id="previewBtn" style="background-color: #fff; color: {{ $theme['theme_primary_color'] }}">{{ __('Shop Now') }}</button>
                    </div>

                    {{-- Preview Content --}}
                    <div class="p-6 bg-light">
                        <div class="row">
                            @for($i=1; $i<=3; $i++)
                            <div class="col-4">
                                <div class="card h-100 shadow-none border">
                                    <div class="bg-label-secondary p-8 text-center">
                                        <i class="bx bx-image-alt display-4"></i>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="h-2 w-50 bg-secondary rounded mb-2"></div>
                                        <div class="h-4 w-75 bg-dark rounded mb-2"></div>
                                        <div class="h-3 w-25 bg-secondary rounded"></div>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const primaryColor = document.getElementById('primaryColor');
    const heroTitle = document.getElementById('heroTitleInput');
    const heroSubtitle = document.getElementById('heroSubtitleInput');

    const previewHero = document.getElementById('previewHero');
    const previewTitle = document.getElementById('previewTitle');
    const previewSubtitle = document.getElementById('previewSubtitle');
    const previewBtn = document.getElementById('previewBtn');

    primaryColor.addEventListener('input', function() {
        previewHero.style.backgroundColor = this.value;
        previewBtn.style.color = this.value;
    });

    heroTitle.addEventListener('input', function() {
        previewTitle.innerText = this.value;
    });

    heroSubtitle.addEventListener('input', function() {
        previewSubtitle.innerText = this.value;
    });
});
</script>
@endsection
