(function() {
    var STORAGE_KEY = 'tsvd_admin_theme';
    var CYCLE = ['system', 'light', 'dark'];
    var root = document.documentElement;
    var currentChoice = 'system';

    function setThemeAttribute(el, choice) {
        if (choice === 'dark') {
            el.setAttribute('data-theme', 'dark');
        } else if (choice === 'light') {
            el.setAttribute('data-theme', 'light');
        } else {
            el.removeAttribute('data-theme');
        }
    }

    function applyToIframe(iframe) {
        try {
            setThemeAttribute(iframe.contentDocument.documentElement, currentChoice);
        } catch (e) {}
    }

    function syncTinymceIframeThemes() {
        var iframes = document.querySelectorAll('iframe[id$="_ifr"]');
        iframes.forEach(function(iframe) {
            applyToIframe(iframe);
            if (!iframe.dataset.tsvdThemeBound) {
                iframe.dataset.tsvdThemeBound = '1';
                iframe.addEventListener('load', function() {
                    applyToIframe(iframe);
                });
            }
        });
    }

    function applyTheme(choice) {
        currentChoice = choice;
        setThemeAttribute(root, choice);
        syncTinymceIframeThemes();
    }

    function observeNewTinymceIframes() {
        var observer = new MutationObserver(function() {
            syncTinymceIframeThemes();
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
        currentChoice = localStorage.getItem(STORAGE_KEY) || 'system';
        syncTinymceIframeThemes();
        observeNewTinymceIframes();

        var toggle = document.getElementById('tsvd-theme-toggle');
        if (!toggle) return;

        var icons = Array.prototype.slice.call(toggle.querySelectorAll('.tsvd-theme-icon'));
        setVisibleIcon(icons, currentChoice);

        toggle.addEventListener('click', function() {
            applyTheme(nextChoice(currentChoice));
            localStorage.setItem(STORAGE_KEY, currentChoice);
            setVisibleIcon(icons, currentChoice);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
