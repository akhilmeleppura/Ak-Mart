@extends('layouts/layoutMaster')

@section('title', 'Sample Page 1 - Project Details')

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
                <div class="tab-pane fade show active" id="sample_page_3" role="tabpanel">
                    <div class="container px-0">
                        <!-- Project Details Card -->
                        <div class="card shadow rounded mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Add Project Details</h5>
                                <span class="badge bg-info text-dark">Demo Page</span>
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

                                <form id="projectForm" action="#" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="project_name" class="form-label">Project Name</label>
                                            <input type="text" name="project_name" id="project_name" class="form-control"
                                                placeholder="Enter project name" value="Demo Project">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="client_name" class="form-label">Client Name</label>
                                            <input type="text" name="client_name" id="client_name" class="form-control"
                                                placeholder="Enter client name" value="ABC Corporation">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="start_date" class="form-label">Start Date</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                value="{{ date('Y-m-01') }}">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="end_date" class="form-label">End Date</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control"
                                                value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="project_status" class="form-label">Project Status</label>
                                        <select name="project_status" id="project_status" class="form-select">
                                            <option selected>Ongoing</option>
                                            <option>Completed</option>
                                            <option>On Hold</option>
                                            <option>Cancelled</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save Project</button>
                                </form>
                            </div>
                        </div>

                        <!-- Progress Section -->
                        <div class="card shadow rounded mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Project Progress Overview</h5>
                            </div>
                            <div class="card-body">
                                <p class="fw-semibold mb-2">Design Phase <span class="float-end">80%</span></p>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 80%"></div>
                                </div>

                                <p class="fw-semibold mb-2">Development Phase <span class="float-end">60%</span></p>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: 60%"></div>
                                </div>

                                <p class="fw-semibold mb-2">Testing Phase <span class="float-end">45%</span></p>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: 45%"></div>
                                </div>

                                <p class="fw-semibold mb-2">Deployment <span class="float-end">25%</span></p>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-danger" style="width: 25%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Project List Table -->
                        <div class="card shadow rounded">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Projects</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Project Name</th>
                                            <th>Client</th>
                                            <th>Status</th>
                                            <th>Deadline</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Alpha ERP System</td>
                                            <td>Global Tech</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>2025-09-30</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Beta CRM Portal</td>
                                            <td>NextGen Solutions</td>
                                            <td><span class="badge bg-warning text-dark">Ongoing</span></td>
                                            <td>2025-10-25</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Gamma E-Commerce</td>
                                            <td>Infoweb</td>
                                            <td><span class="badge bg-danger">Delayed</span></td>
                                            <td>2025-11-05</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
