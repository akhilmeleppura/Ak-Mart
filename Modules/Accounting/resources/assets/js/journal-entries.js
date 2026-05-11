document.addEventListener('DOMContentLoaded', function () {
  initializeCustomDataTable({
    tableSelector: '.datatables-journal-entries',
    ajaxUrl: 'journal-entries-list',
    columnRenderConfig: {
      date: { dbColumn: 'date', type: 'text' },
      account: { dbColumn: 'account_name', type: 'text' },
      debit: { dbColumn: 'debit_amount', type: 'text' },
      credit: { dbColumn: 'credit_amount', type: 'text' },
      description: { dbColumn: 'description', type: 'text' }
    },
    actionsRenderFn: function (data, type, full, meta) {
      return `
        <div class="d-flex align-items-center gap-4">
          <button class="btn btn-sm btn-icon edit-entry" data-id="${full.id}"><i class="icon-base bx bx-edit icon-22px"></i></button>
          <button class="btn btn-sm btn-icon delete-entry" data-id="${full.id}"><i class="icon-base bx bx-trash icon-22px"></i></button>
        </div>
      `;
    }
  });
});
