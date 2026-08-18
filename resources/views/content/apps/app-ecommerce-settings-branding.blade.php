@extends('layouts/layoutMaster')

@section('title', 'Logos & Branding Settings - Apps')

@section('vendor-style')
@vite('resources/assets/vendor/libs/select2/select2.scss')
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js'])
@endsection

@section('page-script')
@vite('resources/assets/js/app-ecommerce-settings.js')
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="icon-base bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('app-ecommerce-settings-branding-save') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-6">
        <!-- Navigation -->
        <div class="col-12 col-lg-4">
            @include('content.apps._settings-sidebar')
        </div>
        <!-- /Navigation -->

        <!-- Options -->
        <div class="col-12 col-lg-8 pt-6 pt-lg-0">
            <div class="tab-content p-0">
                <div class="tab-pane fade show active" id="branding_settings" role="tabpanel">
                    
                    <!-- Main Logo Card -->
                    <div class="card mb-6">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title m-0">Main Store Logo (Light Header)</h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="openImageEditor('site_logo')">
                                <i class="icon-base bx bx-edit-alt me-1"></i> Interactive Editor
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center g-4">
                                <div class="col-12 col-md-4 text-center">
                                    <div class="p-3 border rounded bg-light d-flex align-items-center justify-content-center min-h-100" style="min-height: 110px;">
                                        @php $siteLogo = $settings['site_logo'] ?? ''; @endphp
                                        <img id="preview_site_logo" src="{{ $siteLogo ? asset($siteLogo) : asset('images/brand/ak-mart-icon.svg') }}" alt="Main Logo Preview" style="max-height: 70px; max-width: 100%; object-fit: contain;">
                                    </div>
                                    <small class="text-muted d-block mt-2">Current Header Logo</small>
                                </div>
                                <div class="col-12 col-md-8">
                                    <label class="form-label fw-medium" for="site_logo_file">Upload New Logo Image</label>
                                    <input type="file" class="form-control mb-2" id="site_logo_file" name="site_logo_file" accept="image/*" onchange="previewFile(this, 'preview_site_logo')">
                                    <input type="hidden" name="site_logo_base64" id="site_logo_base64">
                                    <span class="form-text text-muted">Recommended format: PNG, SVG or WebP. Max height: 80px. Transparent background works best.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dark Mode Logo Card -->
                    <div class="card mb-6">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title m-0">Dark Theme Logo</h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="openImageEditor('site_logo_dark')">
                                <i class="icon-base bx bx-edit-alt me-1"></i> Interactive Editor
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center g-4">
                                <div class="col-12 col-md-4 text-center">
                                    <div class="p-3 border rounded bg-dark d-flex align-items-center justify-content-center text-white" style="min-height: 110px;">
                                        @php $siteLogoDark = $settings['site_logo_dark'] ?? ''; @endphp
                                        <img id="preview_site_logo_dark" src="{{ $siteLogoDark ? asset($siteLogoDark) : asset('images/brand/ak-mart-icon.svg') }}" alt="Dark Logo Preview" style="max-height: 70px; max-width: 100%; object-fit: contain;">
                                    </div>
                                    <small class="text-muted d-block mt-2">Dark Mode Preview</small>
                                </div>
                                <div class="col-12 col-md-8">
                                    <label class="form-label fw-medium" for="site_logo_dark_file">Upload Dark Mode Logo</label>
                                    <input type="file" class="form-control mb-2" id="site_logo_dark_file" name="site_logo_dark_file" accept="image/*" onchange="previewFile(this, 'preview_site_logo_dark')">
                                    <input type="hidden" name="site_logo_dark_base64" id="site_logo_dark_base64">
                                    <span class="form-text text-muted">High-contrast logo optimized for dark backgrounds.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Favicon & Site Icons Card -->
                    <div class="card mb-6">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title m-0">Favicon & Browser Tab Icon</h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="openImageEditor('site_favicon')">
                                <i class="icon-base bx bx-crop me-1"></i> Crop Favicon (1:1)
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center g-4">
                                <div class="col-12 col-md-4 text-center">
                                    <div class="p-3 border rounded bg-light d-flex flex-column align-items-center justify-content-center" style="min-height: 110px;">
                                        @php $siteFavicon = $settings['site_favicon'] ?? ''; @endphp
                                        <div class="d-flex align-items-center border px-3 py-1 rounded bg-white shadow-sm mb-2" style="width: 100%; max-width: 180px;">
                                            <img id="preview_site_favicon" src="{{ $siteFavicon ? asset($siteFavicon) : asset('images/brand/favicon.svg') }}" alt="Favicon Preview" style="width: 24px; height: 24px; object-fit: contain;" class="me-2">
                                            <span class="small fw-semibold text-truncate">AK-Mart Admin</span>
                                        </div>
                                        <small class="text-muted">Browser Tab Mockup</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8">
                                    <label class="form-label fw-medium" for="site_favicon_file">Upload Favicon (.ico, .png, .svg)</label>
                                    <input type="file" class="form-control mb-2" id="site_favicon_file" name="site_favicon_file" accept="image/*,.ico" onchange="previewFile(this, 'preview_site_favicon')">
                                    <input type="hidden" name="site_favicon_base64" id="site_favicon_base64">
                                    <span class="form-text text-muted">Recommended square dimension: 32x32px or 64x64px. SVG, PNG or ICO.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email & Notification Header Logo Card -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title m-0">Email & Receipt Notification Header Logo</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center g-4">
                                <div class="col-12 col-md-4 text-center">
                                    <div class="p-3 border rounded bg-light d-flex align-items-center justify-content-center" style="min-height: 110px;">
                                        @php $emailLogo = $settings['email_logo'] ?? ''; @endphp
                                        <img id="preview_email_logo" src="{{ $emailLogo ? asset($emailLogo) : ($siteLogo ? asset($siteLogo) : asset('images/brand/ak-mart-icon.svg')) }}" alt="Email Logo Preview" style="max-height: 60px; max-width: 100%; object-fit: contain;">
                                    </div>
                                    <small class="text-muted d-block mt-2">Email Header Preview</small>
                                </div>
                                <div class="col-12 col-md-8">
                                    <label class="form-label fw-medium" for="email_logo_file">Upload Email Logo</label>
                                    <input type="file" class="form-control mb-2" id="email_logo_file" name="email_logo_file" accept="image/*" onchange="previewFile(this, 'preview_email_logo')">
                                    <input type="hidden" name="email_logo_base64" id="email_logo_base64">
                                    <span class="form-text text-muted">Appears at the top of customer order confirmation emails and PDF invoices.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="d-flex justify-content-end gap-4">
                <button type="reset" class="btn btn-label-secondary">Discard</button>
                <button type="submit" class="btn btn-primary">Save All Branding Changes</button>
            </div>
        </div>
    </div>
</form>

<!-- Interactive Image Editor Modal -->
<div class="modal fade" id="imageEditorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editorModalTitle"><i class="icon-base bx bx-palette me-2"></i>Interactive Logo Studio & Editor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="row g-4">
                    <div class="col-12 col-md-8">
                        <div class="border rounded p-3 bg-light text-center d-flex align-items-center justify-content-center overflow-hidden" style="min-height: 320px; position: relative;">
                            <canvas id="editorCanvas" style="max-width: 100%; max-height: 300px; border: 1px dashed #ccc; background: repeating-conic-gradient(#eee 0% 25%, transparent 0% 50%) 50% / 16px 16px;"></canvas>
                        </div>
                        
                        <!-- Editing Controls Toolbar -->
                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateCanvas(-90)" title="Rotate Left"><i class="icon-base bx bx-rotate-left"></i> -90°</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateCanvas(90)" title="Rotate Right"><i class="icon-base bx bx-rotate-right"></i> +90°</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="zoomCanvas(1.2)" title="Zoom In"><i class="icon-base bx bx-zoom-in"></i> Zoom In</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="zoomCanvas(0.8)" title="Zoom Out"><i class="icon-base bx bx-zoom-out"></i> Zoom Out</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="flipCanvas('h')" title="Flip Horizontal"><i class="icon-base bx bx-reflect-vertical"></i> Flip H</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetEditor()" title="Reset"><i class="icon-base bx bx-refresh"></i> Reset</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 text-start">
                        <div class="card bg-secondary-subtle border-0 h-100 p-3">
                            <h6 class="fw-bold mb-3"><i class="icon-base bx bx-sliders me-1"></i>Image Adjustments</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-medium" for="sliderBrightness">Brightness</label>
                                <input type="range" class="form-range" id="sliderBrightness" min="50" max="150" value="100" oninput="applyFilters()">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-medium" for="sliderContrast">Contrast</label>
                                <input type="range" class="form-range" id="sliderContrast" min="50" max="150" value="100" oninput="applyFilters()">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-medium">Live Background Check</label>
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-sm btn-light border" onclick="toggleCanvasBg('#ffffff')">Light</button>
                                    <button type="button" class="btn btn-sm btn-dark" onclick="toggleCanvasBg('#1e293b')">Dark</button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="toggleCanvasBg('transparent')">Grid</button>
                                </div>
                            </div>
                            
                            <div class="alert alert-info py-2 px-3 small mt-auto">
                                <i class="icon-base bx bx-info-circle me-1"></i> Edit & click <strong>Apply Edited Image</strong> to save.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="applyEditorResult()"><i class="icon-base bx bx-check me-1"></i> Apply Edited Image</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentTargetField = 'site_logo';
let editorCanvas = null;
let editorCtx = null;
let activeImage = null;
let rotationAngle = 0;
let scaleFactor = 1;
let flipH = false;

function previewFile(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function openImageEditor(fieldTarget) {
    currentTargetField = fieldTarget;
    document.getElementById('editorModalTitle').innerText = 'Interactive Editor: ' + fieldTarget.replace(/_/g, ' ').toUpperCase();
    const previewImg = document.getElementById('preview_' + fieldTarget);
    
    activeImage = new Image();
    activeImage.crossOrigin = 'Anonymous';
    activeImage.src = previewImg.src;
    activeImage.onload = function() {
        rotationAngle = 0;
        scaleFactor = 1;
        flipH = false;
        document.getElementById('sliderBrightness').value = 100;
        document.getElementById('sliderContrast').value = 100;
        drawCanvas();
        const modal = new bootstrap.Modal(document.getElementById('imageEditorModal'));
        modal.show();
    };
}

function drawCanvas() {
    editorCanvas = document.getElementById('editorCanvas');
    editorCtx = editorCanvas.getContext('2d');
    
    if (!activeImage) return;

    const b = document.getElementById('sliderBrightness').value;
    const c = document.getElementById('sliderContrast').value;

    let w = activeImage.width * scaleFactor;
    let h = activeImage.height * scaleFactor;

    if (w > 500 || h > 500) {
        const ratio = Math.min(500 / w, 500 / h);
        w *= ratio;
        h *= ratio;
    }

    if (rotationAngle % 180 !== 0) {
        editorCanvas.width = h;
        editorCanvas.height = w;
    } else {
        editorCanvas.width = w;
        editorCanvas.height = h;
    }

    editorCtx.save();
    editorCtx.filter = `brightness(${b}%) contrast(${c}%)`;
    editorCtx.translate(editorCanvas.width / 2, editorCanvas.height / 2);
    editorCtx.rotate((rotationAngle * Math.PI) / 180);
    if (flipH) editorCtx.scale(-1, 1);
    editorCtx.drawImage(activeImage, -w / 2, -h / 2, w, h);
    editorCtx.restore();
}

function rotateCanvas(deg) {
    rotationAngle = (rotationAngle + deg) % 360;
    drawCanvas();
}

function zoomCanvas(factor) {
    scaleFactor *= factor;
    drawCanvas();
}

function flipCanvas(dir) {
    if (dir === 'h') flipH = !flipH;
    drawCanvas();
}

function applyFilters() {
    drawCanvas();
}

function toggleCanvasBg(bg) {
    const parent = document.getElementById('editorCanvas').parentElement;
    if (bg === 'transparent') {
        parent.style.background = 'repeating-conic-gradient(#eee 0% 25%, transparent 0% 50%) 50% / 16px 16px';
    } else {
        parent.style.background = bg;
    }
}

function resetEditor() {
    rotationAngle = 0;
    scaleFactor = 1;
    flipH = false;
    document.getElementById('sliderBrightness').value = 100;
    document.getElementById('sliderContrast').value = 100;
    drawCanvas();
}

function applyEditorResult() {
    if (!editorCanvas) return;
    const base64Data = editorCanvas.toDataURL('image/png');
    document.getElementById('preview_' + currentTargetField).src = base64Data;
    document.getElementById(currentTargetField + '_base64').value = base64Data;
    
    const modalEl = document.getElementById('imageEditorModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
}
</script>
@endsection
