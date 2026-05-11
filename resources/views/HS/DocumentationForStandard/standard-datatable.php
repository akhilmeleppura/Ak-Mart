1. Blade File Integration
* Scripts Include (Standard DataTable JS Files)

@section('page-script')
    @vite(['resources/js/HS/standard-datatable.js', 'resources/js/HS/standard-offcanvas-right.js'])
@endsection

2. Dynamic Table Header Generation

@foreach ($standardDataTableConfig['table'] as $column)
    <th>{{ ucfirst(str_replace('_', ' ', $column['headerName'])) }}</th>
@endforeach
Reads table headers from the passed config in controller.

Ensures table columns are dynamic based on controller definition.

3. standardDataTableConfig Structure

'standardDataTableConfig' => [
    'table' => [
        'id' => ['type' => 'text', 'dbColumn' => 'id', 'headerName' => 'ID'],
        'name' => ['type' => 'nameWithAvatar', 'responsivePriority' => 4, 'dbColumn' => 'name', 'headerName' => 'name'],
        'email' => ['type' => 'email', 'dbColumn' => 'email', 'headerName' => 'email'],
        'email_verified_at' => ['type' => 'verification', 'className' => 'text-center', 'dbColumn' => 'email_verified_at', 'headerName' => 'email_verified_at'],
    ],
    'otherConfig' => [
        'ajaxUrl' => 'user-list',
    ]
]
Defines columns (type, DB mapping, header label, optional class).

AJAX URL for DataTable data fetching (user-list).

4. JavaScript (standard-datatable.js)
Expected to handle:

Initializing DataTable with AJAX URL from standardDataTableConfig.otherConfig.ajaxUrl.

Column rendering based on type (email, nameWithAvatar, verification status, etc.).