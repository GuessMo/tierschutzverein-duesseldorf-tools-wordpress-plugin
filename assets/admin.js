document.addEventListener('DOMContentLoaded', function () {
    var syncBreedsBtn = document.getElementById('tsvd-tools-sync-breeds');
    var syncColorsBtn = document.getElementById('tsvd-tools-sync-colors');
    var syncBothBtn = document.getElementById('tsvd-tools-sync-both');
    var forceSyncCheck = document.getElementById('tsvd-tools-force-sync');
    var logOutput = document.getElementById('tsvd-tools-log');

    function log(msg) {
        var ts = new Date().toLocaleTimeString('de-DE');
        logOutput.textContent += '[' + ts + '] ' + msg + '\n';
        logOutput.scrollTop = logOutput.scrollHeight;
    }

    function fetchSync(action, btn) {
        var origText = btn ? btn.textContent : '';
        if (btn) { btn.disabled = true; btn.textContent = 'Läuft...'; }
        var force = forceSyncCheck && forceSyncCheck.checked ? 'true' : 'false';

        var fd = new FormData();
        fd.append('action', action);
        fd.append('nonce', tsvdTools.nonce);
        fd.append('force', force);

        fetch(tsvdTools.ajaxUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.success) {
                    log('OK: ' + (d.data.message || ''));
                    if (d.data.imported !== undefined) {
                        log('   Neu: ' + d.data.imported + ' | Aktualisiert: ' + d.data.updated + ' | Gelöscht: ' + d.data.deleted);
                    }
                } else {
                    log('FEHLER: ' + (d.data ? d.data.message : 'Unbekannt'));
                }
                if (btn) { btn.textContent = origText; btn.disabled = false; }
            })
            .catch(function(e) {
                log('NETZWERKFEHLER: ' + e.message);
                if (btn) { btn.textContent = origText; btn.disabled = false; }
            });
    }

    if (syncBreedsBtn) {
        syncBreedsBtn.addEventListener('click', function() {
            log('Starte Rassen-Sync...');
            fetchSync('tsvd_tools_sync_breeds', syncBreedsBtn);
        });
    }
    if (syncColorsBtn) {
        syncColorsBtn.addEventListener('click', function() {
            log('Starte Farben-Sync...');
            fetchSync('tsvd_tools_sync_colors', syncColorsBtn);
        });
    }
    if (syncBothBtn) {
        syncBothBtn.addEventListener('click', function() {
            log('Starte kombinierten Sync...');
            fetchSync('tsvd_tools_sync_breeds', syncBothBtn);
            setTimeout(function() { fetchSync('tsvd_tools_sync_colors', syncBothBtn); }, 500);
        });
    }
});