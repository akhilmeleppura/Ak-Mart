@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Gemini API Settings</h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('app-ecommerce-settings-details-save') }}">
        @csrf
        <div class="mb-3">
            <label for="gemini_api_key" class="form-label">Gemini API Key</label>
            <input type="text" class="form-control" id="gemini_api_key" name="gemini_api_key"
                   value="{{ old('gemini_api_key', setting('gemini_api_key')) }}" placeholder="Enter your Gemini API key">
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>
@endsection
