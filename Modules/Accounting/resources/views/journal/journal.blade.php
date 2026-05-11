@extends('accounting::components.layouts.master')

@section('title', 'Journal Entries')

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="datatables-users table" id="journalEntriesTable">
                <thead>
                    <tr></tr> <!-- Headers Rendered Dynamically -->
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('page-script')
    @vite(['resources/js/HS/data-table.js'])
    <script>
        window.dataTableConfig = {
            ajaxUrl: "{{ route('accounting.journal.entriesList') }}", // Correct route name
            actionsRoutePrefix: '/accounting/journal',
            addButton: {
                label: 'Add Journal',
                url: "{{ route('accounting.journal.create') }}"
            },
            permissions: {
                canView: @json(auth()->user()->is_supreme_admin == 1 || auth()->user()->can('accounting.journal.view')),
                canEdit: @json(auth()->user()->is_supreme_admin == 1 || auth()->user()->can('accounting.journal.edit')),
                canDelete: @json(auth()->user()->is_supreme_admin == 1 || auth()->user()->can('accounting.journal.delete')),
                canAdd: @json(auth()->user()->is_supreme_admin == 1 || auth()->user()->can('accounting.journal.create'))
            },
            columns: {
                id: {
                    type: 'text'
                },
                created_at: {
                    type: 'text'
                },
                transaction_date: {
                    type: 'text'
                },
                journal_number: {
                    type: 'text'
                },
                created_by: {
                    type: 'text'
                },
                entries_count: {
                    type: 'text'
                },
                summary: {
                    type: 'text'
                }
            }
        };
    </script>
@endsection
