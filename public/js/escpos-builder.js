/**
 * escpos-builder.js
 * ─────────────────────────────────────────────────────────────
 * Pure ESC/POS command builder for 80mm thermal printers.
 * Width: 48 characters @ normal size.
 * No dependencies — works with QZ Tray rawPrint.
 * ─────────────────────────────────────────────────────────────
 */

const ESC  = '\x1B';
const GS   = '\x1D';
const LF   = '\x0A';

const ESCPOS = {
    // ── Initialise ──────────────────────────────────────────
    INIT          : ESC + '\x40',

    // ── Alignment ───────────────────────────────────────────
    ALIGN_LEFT    : ESC + '\x61\x00',
    ALIGN_CENTER  : ESC + '\x61\x01',
    ALIGN_RIGHT   : ESC + '\x61\x02',

    // ── Bold ────────────────────────────────────────────────
    BOLD_ON       : ESC + '\x45\x01',
    BOLD_OFF      : ESC + '\x45\x00',

    // ── Font size ───────────────────────────────────────────
    SIZE_NORMAL   : GS  + '\x21\x00',   // 1x1
    SIZE_DOUBLE_H : GS  + '\x21\x01',   // 1x2  (double height)
    SIZE_DOUBLE_W : GS  + '\x21\x10',   // 2x1  (double width)
    SIZE_DOUBLE   : GS  + '\x21\x11',   // 2x2  (double both)

    // ── Underline ───────────────────────────────────────────
    UNDERLINE_ON  : ESC + '\x2D\x01',
    UNDERLINE_OFF : ESC + '\x2D\x00',

    // ── Cut ─────────────────────────────────────────────────
    CUT_FULL      : GS  + '\x56\x00',   // full cut
    CUT_PARTIAL   : GS  + '\x56\x01',   // partial cut

    // ── Barcode (Code 128) ───────────────────────────────────
    // GS k n m data NUL
    BARCODE_CODE128_SETUP : GS + '\x68\x50'   // height 80 dots
                          + GS + '\x77\x02'   // width  2
                          + GS + '\x48\x02'   // HRI below barcode
                          + GS + '\x66\x00',  // font A for HRI
};

const COLS = 48;   // printable chars at normal size on 80mm roll

// ── Helpers ─────────────────────────────────────────────────

/**
 * Pad / truncate a string to exactly `len` chars.
 * @param {string}  s
 * @param {number}  len
 * @param {string}  dir  'left' | 'right' | 'center'
 * @param {string}  pad  pad character (default ' ')
 */
function fixedWidth(s, len, dir = 'left', pad = ' ') {
    s = String(s ?? '');
    if (s.length > len) return s.slice(0, len);
    const diff = len - s.length;
    if (dir === 'right')  return pad.repeat(diff) + s;
    if (dir === 'center') {
        const l = Math.floor(diff / 2);
        const r = diff - l;
        return pad.repeat(l) + s + pad.repeat(r);
    }
    return s + pad.repeat(diff);   // left (default)
}

/**
 * Center-pad a string to COLS width, then add LF.
 */
function centerLine(text) {
    return ESCPOS.ALIGN_CENTER + text + LF;
}

/**
 * Left-aligned line + LF.
 */
function leftLine(text) {
    return ESCPOS.ALIGN_LEFT + text + LF;
}

/**
 * Two-column row: left text + right text aligned to COLS.
 * If combined length > COLS the right column wraps (best effort).
 */
function twoColLine(left, right, totalCols = COLS) {
    left  = String(left  ?? '');
    right = String(right ?? '');
    const gap = totalCols - left.length - right.length;
    if (gap < 1) {
        // Truncate left to make room
        const leftMax = totalCols - right.length - 1;
        left = left.slice(0, leftMax);
        return ESCPOS.ALIGN_LEFT + left + ' ' + right + LF;
    }
    return ESCPOS.ALIGN_LEFT + left + ' '.repeat(gap) + right + LF;
}

/**
 * Dashed separator line (full COLS width).
 */
function dashLine(char = '-') {
    return ESCPOS.ALIGN_LEFT + char.repeat(COLS) + LF;
}

// ── Public builder ───────────────────────────────────────────

/**
 * Build a complete ESC/POS receipt string.
 *
 * @param {Object} data
 * @param {string} data.invoiceNumber
 * @param {string} data.invoiceDate
 * @param {string} data.staffName
 * @param {Object|null} data.customer       { id, name, address, city, postalCode, phone }
 * @param {Array}  data.items               [{ name, qty, rate, discount, lineTotal }]
 * @param {number} data.subtotal
 * @param {number} data.taxAmount
 * @param {number} data.discountAmount
 * @param {number} data.totalAmount
 * @param {string|null} data.paymentMode
 * @param {string|null} data.notes
 * @param {string|null} data.jobDescription
 */
function buildReceipt(data) {
    let r = '';

    // ── Initialize printer ──────────────────────────────────
    r += ESCPOS.INIT;

    // ── Business Header ─────────────────────────────────────
    r += ESCPOS.ALIGN_CENTER;
    r += ESCPOS.BOLD_ON;
    r += ESCPOS.SIZE_DOUBLE_H;
    r += 'Prism Eyewear' + LF;
    r += ESCPOS.SIZE_NORMAL;
    r += ESCPOS.BOLD_OFF;
    r += '6/100 Queens Road' + LF;
    r += 'Panmure Auckland-1072' + LF;
    r += 'PH: 09 948 8080 / 02108321242' + LF;
    r += 'GST# 138-002-128' + LF;

    // ── Divider ─────────────────────────────────────────────
    r += dashLine('-');

    // ── Invoice / Transaction line ───────────────────────────
    r += ESCPOS.ALIGN_LEFT;
    r += ESCPOS.SIZE_NORMAL;
    r += 'Invoice : ' + data.invoiceNumber + LF;
    r += 'Date    : ' + data.invoiceDate   + LF;
    r += 'Staff   : ' + data.staffName     + LF;

    // ── Divider ─────────────────────────────────────────────
    r += dashLine('-');

    // ── Customer block ──────────────────────────────────────
    if (data.customer) {
        const c = data.customer;
        r += ESCPOS.ALIGN_LEFT;
        r += ESCPOS.SIZE_NORMAL;
        r += 'Cst #' + c.id + LF;
        r += ESCPOS.BOLD_ON;
        r += (c.name || 'CUSTOMER').toUpperCase() + LF;
        r += ESCPOS.BOLD_OFF;
        if (c.address)    r += c.address + LF;
        const cityPost = [c.city, c.postalCode].filter(Boolean).join(' ');
        if (cityPost)     r += cityPost + LF;
        if (c.phone)      r += 'Ph: ' + c.phone + LF;
    } else {
        r += ESCPOS.BOLD_ON;
        r += 'WALK-IN CUSTOMER' + LF;
        r += ESCPOS.BOLD_OFF;
    }

    // ── Job description (Repair) ─────────────────────────────
    if (data.jobDescription) {
        r += dashLine('.');
        r += ESCPOS.BOLD_ON;
        r += data.jobDescription + LF;
        r += ESCPOS.BOLD_OFF;
    }

    // ── Divider ─────────────────────────────────────────────
    r += dashLine('-');

    // ── Items header ─────────────────────────────────────────
    r += ESCPOS.BOLD_ON;
    r += twoColLine('ITEM', 'AMOUNT');
    r += ESCPOS.BOLD_OFF;
    r += dashLine('.');

    // ── Line items ───────────────────────────────────────────
    r += ESCPOS.ALIGN_LEFT;
    r += ESCPOS.SIZE_NORMAL;
    (data.items || []).forEach(item => {
        const name  = item.qty > 1 ? `${item.name} x${item.qty}` : item.name;
        const price = '$' + Number(item.lineTotal).toFixed(2);
        r += twoColLine(name, price);
    });

    // ── Divider ─────────────────────────────────────────────
    r += dashLine('-');

    // ── Totals ───────────────────────────────────────────────
    r += ESCPOS.SIZE_NORMAL;

    // Sub-totals (tax, discount) if present
    if (data.taxAmount > 0) {
        r += twoColLine('GST Incl.', '$' + Number(data.taxAmount).toFixed(2));
    }
    if (data.discountAmount > 0) {
        r += twoColLine('Discount', '-$' + Number(data.discountAmount).toFixed(2));
    }

    // Grand Total — bold + double-height
    r += dashLine('=');
    r += ESCPOS.BOLD_ON;
    r += ESCPOS.SIZE_DOUBLE_H;
    r += twoColLine('TOTAL', '$' + Number(data.totalAmount).toFixed(2), Math.floor(COLS / 1));
    r += ESCPOS.SIZE_NORMAL;
    r += ESCPOS.BOLD_OFF;
    r += dashLine('=');

    r += ESCPOS.ALIGN_LEFT;

    // ── Payment Mode ─────────────────────────────────────────
    if (data.paymentMode) {
        r += 'Paid by : ' + data.paymentMode + LF;
    }

    // ── Notes ────────────────────────────────────────────────
    if (data.notes) {
        r += dashLine('.');
        r += 'Note: ' + data.notes + LF;
    }

    // ── Footer ───────────────────────────────────────────────
    r += dashLine('-');
    r += ESCPOS.ALIGN_CENTER;
    r += ESCPOS.BOLD_ON;
    r += 'Thank You!' + LF;
    r += ESCPOS.BOLD_OFF;
    r += 'Prism Eyewear' + LF;
    r += '9429051081454' + LF;
    r += data.invoiceDate + LF;
    r += LF + LF + LF + LF + LF + LF;   // feed before cut

    // ── Cut ──────────────────────────────────────────────────
    r += ESCPOS.CUT_PARTIAL;

    return r;
}

// ── Barcode helper (Code 128) ───────────────────────────────
/**
 * Append a Code128 barcode for `value` to an existing ESC/POS string.
 * Call AFTER buildReceipt() if you want barcode on the slip.
 */
function appendBarcode(receiptStr, value) {
    const GS = '\x1D';
    const barcode =
        ESCPOS.ALIGN_CENTER
        + GS + '\x68\x50'           // height 80 dots
        + GS + '\x77\x02'           // module width 2
        + GS + '\x48\x02'           // HRI chars below barcode
        + GS + '\x66\x00'           // font A for HRI
        + GS + '\x6B\x49'           // Code 128, length prefixed
        + String.fromCharCode(value.length)
        + value
        + '\x0A';
    return receiptStr + barcode;
}
