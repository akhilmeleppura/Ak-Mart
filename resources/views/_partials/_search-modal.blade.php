<!-- Global Search Modal (Ctrl + K) -->
<div class="modal fade" id="globalSearchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-bottom p-3">
                <div class="input-group input-group-merge">
                    <span class="input-group-text border-0 ps-0 fs-4"><i class="bx bx-search text-muted"></i></span>
                    <input type="text" id="global-search-input" class="form-control border-0 fs-5 ps-2" placeholder="Search Products, Orders, Customers, Suppliers... (ESC to close)" autofocus>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 400px; overflow-y: auto;">
                <div id="global-search-results">
                    <div class="text-center py-4 text-muted small">
                        Type at least 2 characters to search across AK-Mart database...
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-3 justify-content-between text-muted small">
                <span><kbd class="bg-light text-dark">Ctrl</kbd> + <kbd class="bg-light text-dark">K</kbd> to open anytime</span>
                <span>AK-Mart Global Search Engine</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        const modalEl = document.getElementById('globalSearchModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            setTimeout(() => document.getElementById('global-search-input')?.focus(), 200);
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('global-search-input');
    const container = document.getElementById('global-search-results');
    let timer = null;

    if (input && container) {
        input.addEventListener('input', function() {
            clearTimeout(timer);
            const val = this.value.trim();
            if (val.length < 2) {
                container.innerHTML = '<div class="text-center py-4 text-muted small">Type at least 2 characters to search...</div>';
                return;
            }

            timer = setTimeout(() => {
                fetch(`{{ url('app/global-search') }}?query=${encodeURIComponent(val)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.results || data.results.length === 0) {
                            container.innerHTML = `<div class="text-center py-4 text-muted small">No results matching "${val}"</div>`;
                            return;
                        }
                        let html = '<div class="list-group list-group-flush">';
                        data.results.forEach(res => {
                            html += `
                                <a href="${res.url}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2 px-3 border-0 rounded-2 mb-1">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-sm flex-shrink-0">
                                            <span class="avatar-initial rounded bg-label-primary"><i class="${res.icon}"></i></span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 small fw-bold text-heading">${res.title}</h6>
                                            <small class="text-muted">${res.subtitle}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-label-secondary small">${res.type}</span>
                                </a>
                            `;
                        });
                        html += '</div>';
                        container.innerHTML = html;
                    });
            }, 300);
        });
    }
});
</script>
