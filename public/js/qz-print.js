/**
 * qz-print.js
 * ─────────────────────────────────────────────────────────────
 * QZ Tray connection manager + print dispatcher.
 * Depends on:  escpos-builder.js  (must be loaded first)
 *              qz-tray.js         (official QZ Tray client lib)
 * ─────────────────────────────────────────────────────────────
 */

/* ─── Configuration ──────────────────────────────────────── */
const QZ_CONFIG = {
    /** Exact Windows printer name as shown in Devices & Printers */
    printerName : 'Tysso Thermal Print',

    /** Set true to print a Code128 barcode of the invoice number */
    printBarcode : false,

    /** Retry connection this many times before giving up */
    maxRetries   : 3,
};

/* ─── Internal state ─────────────────────────────────────── */
let _retries = 0;
let _qzSetup  = false;

/* ─── QZ Tray security (certificate + signing) ───────────── */

/**
 * Set up the QZ Tray security callbacks once.
 * Uses the server-issued certificate and signs each request
 * with the private key via /qz-sign so the printer always
 * allows the job without a user prompt.
 */
function setupQZSecurity() {
    if (_qzSetup) return;
    _qzSetup = true;

    // ── Certificate ─────────────────────────────────────────
    qz.security.setCertificatePromise(function (resolve, reject) {
        fetch('/qz-cert', { cache: 'no-store' })
            .then(r => r.ok ? r.text() : Promise.reject('cert fetch failed'))
            .then(resolve)
            .catch(reject);
    });

    // ── Request signing ──────────────────────────────────────
    qz.security.setSignatureAlgorithm('SHA512');
    qz.security.setSignaturePromise(function (toSign) {
        return function (resolve, reject) {
            fetch('/qz-sign', {
                method  : 'POST',
                headers : {
                    'Content-Type' : 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]')?.content
                                     || '',
                },
                body    : 'request=' + encodeURIComponent(toSign),
            })
            .then(r => r.ok ? r.text() : Promise.reject('sign failed'))
            .then(resolve)
            .catch(reject);
        };
    });
}

/* ─── QZ Tray connection ─────────────────────────────────── */

/**
 * Connect to QZ Tray.  Resolves when connected, rejects on failure.
 */
async function connectQZ() {
    setupQZSecurity();                    // must be called before connect
    if (qz.websocket.isActive()) return;   // already connected

    try {
        await qz.websocket.connect();
        console.log('[QZ] Connected.');
        _retries = 0;
    } catch (err) {
        _retries++;
        console.warn(`[QZ] Connection attempt ${_retries} failed:`, err);

        if (_retries < QZ_CONFIG.maxRetries) {
            await new Promise(r => setTimeout(r, 1000));
            return connectQZ();   // retry
        }

        throw new Error(
            '⚠️ QZ Tray is not running.\n\n' +
            'Please:\n' +
            '1. Download QZ Tray from https://qz.io/download/\n' +
            '2. Install and launch it\n' +
            '3. Refresh this page'
        );
    }
}

/**
 * Resolve the printer name.
 * Returns QZ_CONFIG.printerName if set, otherwise picks the first
 * available printer and logs the full list.
 */
async function resolvePrinter() {
    if (QZ_CONFIG.printerName) {
        return qz.printers.find(QZ_CONFIG.printerName);
    }

    const printers = await qz.printers.find();
    console.log('[QZ] Available printers:', printers);
    if (!printers || printers.length === 0) {
        throw new Error('No printers found via QZ Tray.');
    }
    console.warn('[QZ] No printerName configured — using first printer:', printers[0]);
    return printers[0];
}

/* ─── Main print function ────────────────────────────────── */

/**
 * Build and dispatch the ESC/POS receipt.
 *
 * @param {Object} invoiceData  — same shape as buildReceipt() expects
 */
async function printReceipt(invoiceData) {
    try {
        // 1. Connect
        await connectQZ();

        // 2. Resolve printer
        let printer;
        try {
            printer = await resolvePrinter();
        } catch (printerErr) {
            // List all printers as fallback
            const all = await qz.printers.find();
            console.error('[QZ] Could not find printer "' + QZ_CONFIG.printerName + '"');
            console.info('[QZ] Available printers:', all);
            throw new Error(
                'Printer "' + QZ_CONFIG.printerName + '" not found.\n' +
                'Available printers are listed in the browser console.\n\n' +
                'Update QZ_CONFIG.printerName in qz-print.js.'
            );
        }

        // 3. Build ESC/POS raw string
        let rawString = buildReceipt(invoiceData);

        // 4. Optional barcode
        if (QZ_CONFIG.printBarcode && invoiceData.invoiceNumber) {
            rawString = appendBarcode(rawString, invoiceData.invoiceNumber);
        }

        // 5. Convert to Uint8Array → base64 (preserves all ESC/POS bytes)
        const encoder = new TextEncoder();
        const bytes   = encoder.encode(rawString);
        const base64  = btoa(String.fromCharCode(...bytes));

        // 6. Create QZ config
        const config = qz.configs.create(printer, {
            raw : true,
        });

        // 7. Build QZ data array — base64-encoded raw bytes
        const printData = [
            { type: 'raw', format: 'base64', data: base64 }
        ];

        // 7. Send to printer
        await qz.print(config, printData);
        console.log('[QZ] Print job sent to', printer);

    } catch (err) {
        console.error('[QZ] Print error:', err);
        alert(err.message || 'Print failed. See console for details.');
    }
}

/* ─── Auto-disconnect on page unload ───────────────────────── */
window.addEventListener('beforeunload', () => {
    if (qz.websocket.isActive()) {
        qz.websocket.disconnect();
    }
});
