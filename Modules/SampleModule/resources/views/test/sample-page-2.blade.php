@extends('layouts/layoutMaster')

@section('title', 'Sample Page 2 - Demo Data')

@section('content')
    <div class="row g-6">
        <!-- Navigation -->
        <div class="col-12 col-lg-4">
            <div class="d-flex justify-content-between flex-column mb-4 mb-md-0">
                <h5 class="mb-4">Setting For {{ ucfirst($moduleName) }}</h5>
                @include('samplemodule::test.sample-menu')
            </div>
        </div>

        <!-- Content -->
        <div class="col-12 col-lg-8 pt-6 pt-lg-0">
            <div class="tab-content p-0">
                <div class="tab-pane fade show active" id="sample_page_2" role="tabpanel">
                    <div class="container px-0">
                        <div class="card shadow rounded mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Add Sample Information</h5>
                            </div>

                            <div class="card-body">
                                {{-- Success Message --}}
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                {{-- Validation Errors --}}
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form id="addSampleForm" action="#" method="POST">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="sample_name" class="form-label">Sample Name</label>
                                            <input type="text" name="sample_name" id="sample_name" class="form-control"
                                                placeholder="Enter sample name" value="Demo Sample Name" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="sample_type" class="form-label">Sample Type</label>
                                            <select name="sample_type" id="sample_type" class="form-select">
                                                <option>Type A</option>
                                                <option>Type B</option>
                                                <option>Type C</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="created_by" class="form-label">Created By</label>
                                            <input type="text" name="created_by" id="created_by" class="form-control"
                                                placeholder="Enter creator name" value="Admin">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="date_created" class="form-label">Date Created</label>
                                            <input type="date" name="date_created" id="date_created" class="form-control"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save Sample</button>
                                </form>
                            </div>
                        </div>

                        <!-- Demo Data Table -->
                        <div class="card shadow rounded mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Samples</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Sample Name</th>
                                            <th>Type</th>
                                            <th>Created By</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Alpha Entry</td>
                                            <td>Type A</td>
                                            <td>John Doe</td>
                                            <td>2025-10-14</td>
                                            <td><span class="badge bg-success">Active</span></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Beta Entry</td>
                                            <td>Type B</td>
                                            <td>Jane Smith</td>
                                            <td>2025-10-12</td>
                                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Gamma Entry</td>
                                            <td>Type C</td>
                                            <td>Akhil S</td>
                                            <td>2025-10-10</td>
                                            <td><span class="badge bg-danger">Inactive</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Summary Card -->
                        <div class="card shadow rounded">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Total Samples Recorded</h6>
                                <h3 class="fw-bold text-primary">3</h3>
                                <p class="text-secondary mb-0">Last updated: {{ now()->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
