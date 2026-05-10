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
                    refreshStatus();
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

    function refreshStatus() {
        var fd = new FormData();
        fd.append('action', 'tsvd_tools_get_status');
        fd.append('nonce', tsvdTools.nonce);
        fetch(tsvdTools.ajaxUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.success && d.data) {
                    var infoDiv = document.querySelector('.tsvd-tools-info');
                    if (infoDiv) {
                        var paragraphs = infoDiv.querySelectorAll('p');
                        if (paragraphs.length >= 2) {
                            paragraphs[0].innerHTML = '<strong>Rassen-JSON:</strong> ' + (d.data.breeds_file || '?') + ' (' + d.data.breeds_count + ' Rassen in DB) — Letzter Sync: ' + (d.data.breeds_last_sync || 'nie');
                            paragraphs[1].innerHTML = '<strong>Farben-JSON:</strong> ' + (d.data.colors_file || '?') + ' (' + d.data.colors_count + ' Farben in DB) — Letzter Sync: ' + (d.data.colors_last_sync || 'nie');
                        }
                    }
                }
            })
            .catch(function() {});
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

    var saveTokenBtn = document.getElementById('tsvd-tools-save-token');
    var tokenInput = document.getElementById('tsvd-tools-token');
    if (saveTokenBtn) {
        saveTokenBtn.addEventListener('click', function() {
            var token = tokenInput.value.trim();
            var fd = new FormData();
            fd.append('action', 'tsvd_tools_save_token');
            fd.append('nonce', tsvdTools.nonce);
            fd.append('token', token);
            saveTokenBtn.disabled = true;
            saveTokenBtn.textContent = 'Speichere...';
            fetch(tsvdTools.ajaxUrl, { method: 'POST', body: fd })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    log(d.success ? 'Token gespeichert.' : 'Fehler: ' + (d.data ? d.data.message : '?'));
                    saveTokenBtn.disabled = false;
                    saveTokenBtn.textContent = 'Speichern';
                })
                .catch(function(e) {
                    log('FEHLER: ' + e.message);
                    saveTokenBtn.disabled = false;
                    saveTokenBtn.textContent = 'Speichern';
                });
        });
    }
});