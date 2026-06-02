<?php

@extends('layouts/layoutMaster')

@section('title', 'AI Settings - Apps')

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
<form method="POST" action="{{ route('app-ecommerce-settings-ai-save') }}">
    @csrf
    <div class="row g-6">
        <!-- Navigation -->
        <div class="col-12 col-lg-4">
            <div class="d-flex justify-content-between flex-column mb-4 mb-md-0">
                <h5 class="mb-4">AI Configuration</h5>
                <ul class="nav nav-align-left nav-pills flex-column">
                    <li class="nav-item mb-1">
                        <a class="nav-link active" href="javascript:void(0);">
                            <i class="icon-base bx bx-brain icon-18px me-1_5"></i>
                            <span class="align-middle">General Settings</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /Navigation -->
        <div class="col-12 col-lg-8 pt-6 pt-lg-0">
            <div class="tab-content p-0">
                <div class="tab-pane fade show active" id="ai_general" role="tabpanel">
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title m-0">General AI Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4 g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="ai-enabled">Enable AI</label>
                                    <select id="ai-enabled" name="enabled" class="select2 form-select">
                                        <option value="1" {{ ($settings['enabled'] ?? false) ? 'selected' : '' }}>Enabled</option>
                                        <option value="0" {{ !($settings['enabled'] ?? false) ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="default-provider">Default Provider</label>
                                    <select id="default-provider" name="default_provider" class="select2 form-select">
                                        <option value="gemini" {{ ( ($settings['default_provider'] ?? '') == 'gemini') ? 'selected' : '' }}>Gemini</option>
                                        <option value="openai" {{ ( ($settings['default_provider'] ?? '') == 'openai') ? 'selected' : '' }}>OpenAI</option>
                                        <option value="claude" {{ ( ($settings['default_provider'] ?? '') == 'claude') ? 'selected' : '' }}>Claude</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="assistant-name">Assistant Name</label>
                                    <input type="text" class="form-control" id="assistant-name" name="assistant_name" value="{{ $settings['assistant_name'] ?? '' }}" placeholder="Assistant Name" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="assistant-prompt">Assistant Prompt</label>
                                    <textarea class="form-control" id="assistant-prompt" name="assistant_prompt" rows="3" placeholder="System prompt for the assistant">{{ $settings['assistant_prompt'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Gemini Settings -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title m-0">Gemini Configuration</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="gemini-api-key">API Key</label>
                                    <input type="text" class="form-control" id="gemini-api-key" name="gemini[api_key]" value="{{ $settings['gemini']['api_key'] ?? '' }}" placeholder="Gemini API Key" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="gemini-model">Model</label>
                                    <input type="text" class="form-control" id="gemini-model" name="gemini[model]" value="{{ $settings['gemini']['model'] ?? '' }}" placeholder="gemini-pro" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="gemini-temperature">Temperature</label>
                                    <input type="number" step="0.01" class="form-control" id="gemini-temperature" name="gemini[temperature]" value="{{ $settings['gemini']['temperature'] ?? '' }}" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="gemini-max-tokens">Max Tokens</label>
                                    <input type="number" class="form-control" id="gemini-max-tokens" name="gemini[max_tokens]" value="{{ $settings['gemini']['max_tokens'] ?? '' }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- OpenAI Settings -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title m-0">OpenAI Configuration</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="openai-api-key">API Key</label>
                                    <input type="text" class="form-control" id="openai-api-key" name="openai[api_key]" value="{{ $settings['openai']['api_key'] ?? '' }}" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="openai-model">Model</label>
                                    <input type="text" class="form-control" id="openai-model" name="openai[model]" value="{{ $settings['openai']['model'] ?? '' }}" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="openai-temperature">Temperature</label>
                                    <input type="number" step="0.01" class="form-control" id="openai-temperature" name="openai[temperature]" value="{{ $settings['openai']['temperature'] ?? '' }}" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="openai-max-tokens">Max Tokens</label>
                                    <input type="number" class="form-control" id="openai-max-tokens" name="openai[max_tokens]" value="{{ $settings['openai']['max_tokens'] ?? '' }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Claude Settings -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title m-0">Claude Configuration</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="claude-api-key">API Key</label>
                                    <input type="text" class="form-control" id="claude-api-key" name="claude[api_key]" value="{{ $settings['claude']['api_key'] ?? '' }}" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="claude-model">Model</label>
                                    <input type="text" class="form-control" id="claude-model" name="claude[model]" value="{{ $settings['claude']['model'] ?? '' }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-4">
                <button type="reset" class="btn btn-label-secondary">Discard</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</form>
@endsection
