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
    printerName : 'PRP-250CL',

    /** Set true to print a Code128 barcode of the invoice number */
    printBarcode : false,

    /** Retry connection this many times before giving up */
    maxRetries   : 3,
};

/* ─── Internal state ─────────────────────────────────────── */
let _retries = 0;

/* ─── QZ Tray connection ─────────────────────────────────── */

/**
 * Connect to QZ Tray.  Resolves when connected, rejects on failure.
 */
async function connectQZ() {
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

        // 5. Create QZ config
        const config = qz.configs.create(printer, {
            raw : true,
        });

        // 6. Build QZ data array — single raw byte string
        const printData = [
            { type: 'raw', format: 'plain', data: rawString }
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
