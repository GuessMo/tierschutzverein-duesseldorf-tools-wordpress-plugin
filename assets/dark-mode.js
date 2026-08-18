(function() {
    var STORAGE_KEY = 'tsvd_admin_theme';
    var CYCLE = ['system', 'light', 'dark'];
    var root = document.documentElement;

    function setThemeAttribute(el, choice) {
        if (choice === 'dark') {
            el.setAttribute('data-theme', 'dark');
        } else if (choice === 'light') {
            el.setAttribute('data-theme', 'light');
        } else {
            el.removeAttribute('data-theme');
        }
    }

    function syncTinymceIframeThemes(choice) {
        var iframes = document.querySelectorAll('iframe[id$="_ifr"]');
        iframes.forEach(function(iframe) {
            try {
                setThemeAttribute(iframe.contentDocument.documentElement, choice);
            } catch (e) {}
        });
    }

    function applyTheme(choice) {
        setThemeAttribute(root, choice);
        syncTinymceIframeThemes(choice);
    }

    function observeNewTinymceIframes(getCurrentChoice) {
        var observer = new MutationObserver(function() {
            syncTinymceIframeThemes(getCurrentChoice());
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    function setVisibleIcon(icons, choice) {
        icons.forEach(function(icon) {
            icon.hidden = icon.getAttribute('data-theme-icon') !== choice;
        });
    }

    function nextChoice(choice) {
        var index = CYCLE.indexOf(choice);
        return CYCLE[(index + 1) % CYCLE.length];
    }

    function init() {
        var current = localStorage.getItem(STORAGE_KEY) || 'system';
        syncTinymceIframeThemes(current);
        observeNewTinymceIframes(function() { return current; });

        var toggle = document.getElementById('tsvd-theme-toggle');
        if (!toggle) return;

        var icons = Array.prototype.slice.call(toggle.querySelectorAll('.tsvd-theme-icon'));
        setVisibleIcon(icons, current);

        toggle.addEventListener('click', function() {
            current = nextChoice(current);
            localStorage.setItem(STORAGE_KEY, current);
            applyTheme(current);
            setVisibleIcon(icons, current);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
