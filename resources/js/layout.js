// resources/js/layout.js
// Import di resources/js/app.js:  import './layout.js'

document.addEventListener('DOMContentLoaded', () => {

    // ── Sidebar toggle ────────────────────────────────────────
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar   = document.getElementById('sidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            // Simpan preferensi ke localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed ? '1' : '0');
        });

        // Restore preferensi dari localStorage
        const stored = localStorage.getItem('sidebar_collapsed');
        if (stored === '1') sidebar.classList.add('collapsed');
        if (stored === '0') sidebar.classList.remove('collapsed');
    }

    // ── Search — submit on Enter ──────────────────────────────
    const searchInput = document.querySelector('.sb-search-wrap input');
    if (searchInput) {
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const url = new URL(window.location.href);
                url.searchParams.set('q', searchInput.value);
                url.searchParams.delete('page');
                window.location = url.toString();
            }
        });
    }

    // ── Auto-dismiss flash messages ───────────────────────────
    document.querySelectorAll('.alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });

    // ── Check-all checkbox ────────────────────────────────────
    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', () => {
            document.querySelectorAll('.row-check')
                    .forEach(c => { c.checked = checkAll.checked; });
        });
    }

});
