<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Atelier — Your art. Your rules.</title>

    <!-- Theme detection: runs before CSS paint to prevent flash -->
    <script>
        (function() {
            try {
                var saved = localStorage.getItem('atelier_theme');
                var allowedThemes = ['default', 'rubber', 'femboy', 'dominant'];
                if (saved && !allowedThemes.includes(saved)) {
                    localStorage.setItem('atelier_theme', 'default');
                    saved = 'default';
                }
                if (saved && saved !== 'default') {
                    document.documentElement.setAttribute('data-theme', saved);
                }
            } catch(e) {}
        })();
    </script>

    @vite(['resources/css/app.css'])
</head>
<body class="{{ request()->routeIs('conversations.*') ? 'chat-route' : '' }}">
    {{-- Stripe (optional, theme-controlled via CSS) --}}
    <div id="flag-stripe" aria-hidden="true"></div>

    {{-- Navigation --}}
    @include('partials.nav')

    {{-- Main content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer (hidden on chat) --}}
    @unless(request()->routeIs('conversations.*'))
        <footer class="footer">
            <div class="font-mono">© 2026 atelier — made by artists</div>
            <div class="footer-links">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
                <a href="#">Contact</a>
            </div>
        </footer>
    @endunless

    {{-- Session status toast --}}
    @if(session('status'))
        <div id="toast-notification" class="toast show">
            <span class="toast-icon">◆</span>
            <span class="font-mono">{{ session('status') }}</span>
        </div>
    @endif

    @stack('scripts')

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        /* ── Theme Selector ── */
        var themeSelectors = Array.from(document.querySelectorAll('[data-theme-selector]'));
        var allowedThemes = ['default', 'rubber', 'femboy', 'dominant'];
        var currentTheme = localStorage.getItem('atelier_theme') || 'default';
        if (!allowedThemes.includes(currentTheme)) {
            currentTheme = 'default';
            localStorage.setItem('atelier_theme', currentTheme);
        }

        function applyTheme(theme) {
            if (!allowedThemes.includes(theme)) {
                theme = 'default';
            }
            localStorage.setItem('atelier_theme', theme);
            if (theme === 'default') {
                document.documentElement.removeAttribute('data-theme');
            } else {
                document.documentElement.setAttribute('data-theme', theme);
            }
            themeSelectors.forEach(function(s) { s.value = theme; });
        }

        if (themeSelectors.length) {
            themeSelectors.forEach(function(sel) { sel.value = currentTheme; });
            themeSelectors.forEach(function(sel) {
                sel.addEventListener('change', function() {
                    applyTheme(this.value);
                });
            });
            applyTheme(currentTheme);
        }

        /* ── Toast auto-dismiss ── */
        var toast = document.getElementById('toast-notification');
        if (toast) {
            setTimeout(function() { toast.classList.remove('show'); }, 4000);
        }

        /* ── Requests Drawer ── */
        var drawer = document.getElementById('requests-drawer');
        var drawerBackdrop = document.getElementById('requests-drawer-backdrop');
        var unreadBadge = document.getElementById('requests-unread-badge');

        function openDrawer() {
            if (drawer) drawer.style.right = '0';
            if (drawerBackdrop) { drawerBackdrop.style.opacity = '1'; drawerBackdrop.style.pointerEvents = 'auto'; }
            refreshDrawer();
        }
        function closeDrawer() {
            if (drawer) drawer.style.right = '-420px';
            if (drawerBackdrop) { drawerBackdrop.style.opacity = '0'; drawerBackdrop.style.pointerEvents = 'none'; }
        }

        document.getElementById('requests-drawer-toggle')?.addEventListener('click', openDrawer);
        document.getElementById('requests-drawer-close')?.addEventListener('click', closeDrawer);
        drawerBackdrop?.addEventListener('click', closeDrawer);

        var drawerList = document.getElementById('requests-drawer-list');
        var lastUnread = 0;

        async function refreshDrawer() {
            if (!drawerList) return;
            try {
                var res = await fetch('{{ route('conversations.notifications.summary') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                var data = await res.json();
                var items = data.items || [];

                if (!items.length) {
                    drawerList.innerHTML = '<p class="font-mono text-sm text-muted">No request threads yet.</p>';
                } else {
                    drawerList.innerHTML = items.map(function(item) {
                        return '<a href="' + item.url + '" class="commission-card" style="text-decoration:none;">' +
                            '<div>' +
                            '<div class="commission-card-meta">' + (item.otherParty.username ? '/' + item.otherParty.username : 'request') + ' — ' + String(item.status).replace('_',' ') + '</div>' +
                            '<div class="commission-card-title">' + item.title + '</div>' +
                            '<div class="commission-card-body">' + (item.latestMessage ? item.latestMessage.message : 'No messages yet.') + '</div>' +
                            '</div>' +
                            '<div class="commission-card-side">' +
                            (item.unread > 0 ? '<span class="badge badge-accent badge-sm">' + item.unread + '</span>' : '') +
                            '<span class="font-mono text-xs" style="color:var(--color-muted);">' + item.updatedHuman + '</span>' +
                            '</div></a>';
                    }).join('');
                }

                var totalUnread = Number(data.totalUnread || 0);
                if (unreadBadge) {
                    unreadBadge.textContent = totalUnread > 0 ? (totalUnread > 99 ? '99+' : String(totalUnread)) : '';
                    unreadBadge.style.display = totalUnread > 0 ? 'inline-flex' : 'none';
                }
            } catch(e) { console.error(e); }
        }

        if (unreadBadge) {
            refreshDrawer();
            setInterval(refreshDrawer, 15000);
        }

        /* ── Mode Switcher ── */
        var modeSwitcher = document.querySelector('.mode-switcher');
        if (modeSwitcher) {
            var modeButtons = Array.from(modeSwitcher.querySelectorAll('.mode-btn'));
            var modeMap = { commissioner: '0%', artist: '100%', admin: '200%' };

            modeButtons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    if (btn.classList.contains('active')) return;
                    e.preventDefault();
                    var form = btn.closest('form');
                    modeButtons.forEach(function(b) { b.classList.remove('is-clicked'); });
                    btn.classList.add('is-clicked');
                    modeSwitcher.classList.add('is-switching');
                    setTimeout(function() { form && form.submit(); }, 200);
                });
            });
        }
    });
    </script>
</body>
</html>
