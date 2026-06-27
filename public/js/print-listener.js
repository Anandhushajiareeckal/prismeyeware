/**
 * Global Print Listener for QZ Tray Print Queue
 */

let printQueueInterval = null;
let isPrinting = false;

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('print-server-toggle');
    const badge  = document.getElementById('print-server-badge');
    
    if (!toggle) return;

    // Load state
    const isActive = localStorage.getItem('printServerActive') === 'true';
    toggle.checked = isActive;
    updateBadge(isActive);

    if (isActive) {
        startPolling();
    }

    toggle.addEventListener('change', (e) => {
        const checked = e.target.checked;
        localStorage.setItem('printServerActive', checked);
        updateBadge(checked);

        if (checked) {
            startPolling();
        } else {
            stopPolling();
        }
    });

    function updateBadge(active) {
        if (!badge) return;
        if (active) {
            badge.textContent = 'ON';
            badge.classList.remove('bg-secondary');
            badge.classList.add('bg-success');
        } else {
            badge.textContent = 'OFF';
            badge.classList.remove('bg-success');
            badge.classList.add('bg-secondary');
        }
    }

    function startPolling() {
        if (printQueueInterval) clearInterval(printQueueInterval);
        printQueueInterval = setInterval(pollPrintJobs, 4000);
        console.log('[PrintListener] Started polling.');
    }

    function stopPolling() {
        if (printQueueInterval) clearInterval(printQueueInterval);
        printQueueInterval = null;
        console.log('[PrintListener] Stopped polling.');
    }

    async function pollPrintJobs() {
        if (isPrinting) return; // Wait until current batch is processed
        
        try {
            const res = await fetch('/print-jobs/pending');
            const jobs = await res.json();

            if (jobs && jobs.length > 0) {
                isPrinting = true;
                await processJobs(jobs);
                isPrinting = false;
            }
        } catch (err) {
            console.error('[PrintListener] Polling error:', err);
        }
    }

    async function processJobs(jobs) {
        if (typeof connectQZ === 'undefined' || typeof buildReceipt === 'undefined') {
            console.warn('[PrintListener] QZ scripts missing. Cannot print.');
            return;
        }

        try {
            await connectQZ();
            const printer = await resolvePrinter();

            for (const job of jobs) {
                try {
                    const payload = typeof job.payload === 'string' ? JSON.parse(job.payload) : job.payload;
                    
                    // Payload is an array of invoice data objects
                    for (const invoiceData of payload) {
                        let rawString = buildReceipt(invoiceData);
                        
                        const encoder = new TextEncoder();
                        const bytes   = encoder.encode(rawString);
                        const base64  = btoa(String.fromCharCode(...bytes));
                        
                        const config  = qz.configs.create(printer, { raw: true });
                        await qz.print(config, [{ type: 'raw', format: 'base64', data: base64 }]);
                    }

                    // Mark printed
                    await fetch(`/print-jobs/${job.id}/mark-printed`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    console.log(`[PrintListener] Completed job #${job.id}`);
                } catch (err) {
                    console.error(`[PrintListener] Error printing job #${job.id}:`, err);
                }
            }
        } catch (err) {
            console.error('[PrintListener] Printer connection error:', err);
            
            // Show error state on badge so user is aware
            const badge = document.getElementById('print-server-badge');
            if (badge) {
                badge.textContent = 'ERR';
                badge.classList.remove('bg-success');
                badge.classList.add('bg-danger');
                badge.title = err.message || 'Printer Error';
            }
        }
    }
});
