<ul class="nav nav-align-left nav-pills flex-column">
    @foreach ($menu as $item)
        <li class="nav-item mb-1">
            <a class="nav-link {{ $item['active'] ? 'active' : '' }}" href="{{ url($item['url']) }}"
                @if (str_starts_with($item['url'], '#')) onclick="handleMenuClick(event)" @endif>
                <i class="icon-base {{ $item['icon'] }} icon-18px me-1_5"></i>
                <span class="align-middle">{{ $item['label'] }}</span>
            </a>
        </li>
    @endforeach
</ul>

@push('scripts')
    <script>
        function handleMenuClick(event) {
            event.preventDefault();
            const link = event.currentTarget;

            // Remove active class from all menu items
            document.querySelectorAll('.nav-link').forEach(item => {
                item.classList.remove('active');
            });

            // Add active class to clicked item
            link.classList.add('active');

            // Handle your content loading here
            if (link.getAttribute('href') === '#chart-of-accounts') {
                loadChartOfAccounts();
            }
        }
    </script>
@endpush
