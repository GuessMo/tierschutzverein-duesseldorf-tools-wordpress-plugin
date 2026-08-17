(function() {
    var STORAGE_KEY = 'tsvd_admin_theme';
    var CYCLE = ['system', 'light', 'dark'];
    var root = document.documentElement;

    function applyTheme(choice) {
        if (choice === 'dark') {
            root.setAttribute('data-theme', 'dark');
        } else if (choice === 'light') {
            root.setAttribute('data-theme', 'light');
        } else {
            root.removeAttribute('data-theme');
        }
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
        var toggle = document.getElementById('tsvd-theme-toggle');
        if (!toggle) return;

        var icons = Array.prototype.slice.call(toggle.querySelectorAll('.tsvd-theme-icon'));
        var current = localStorage.getItem(STORAGE_KEY) || 'system';
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
