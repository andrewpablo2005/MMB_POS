/* ═══════════════════════════════════════════════════════════════════
   MMB Barcode — real Code 128-B barcodes, drawn on canvas (v1.0)
   GitHub issue #4 item 4: generated product codes now render as an
   actual line barcode with a preview and a downloadable PNG label
   users can stick onto product packaging.

   100% self-contained — no external library (the fixed app stack
   forbids new dependencies). The Code 128 symbol table below is the
   standard one (107 symbols: 0–102 data, 103–105 start A/B/C, 106
   stop) expressed as bar/space module bit strings.

   API
   ─ mmbBarcodeModules(code)         → module bit-string (Code 128-B)
   ─ mmbBarcodeCanvas(code, opts)    → HTMLCanvasElement label
   ─ mmbBarcodePreview(canvas, code, opts)  → draw into existing canvas
   ─ mmbBarcodeDownload(code, name)  → save PNG (barcode-<code>.png)
   ─ mmbShowBarcodeModal(code, name) → open the shared preview modal
                                        (#mmbBarcodeModal — added in
                                        productmanagement.php)
   ═══════════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    /* Code 128 symbol table — value 0-102, START_A=103, START_B=104,
       START_C=105, STOP=106 (STOP is 13 modules wide). */
    var BARS = [
        '11011001100', '11001101100', '11001100110', '10010011000', '10010001100',
        '10001001100', '10011001000', '10011000100', '10001100100', '11001001000',
        '11001000100', '11000100100', '10110011100', '10011011100', '10011001110',
        '10111001100', '10011101100', '10011100110', '11001110010', '11001011100',
        '11001001110', '11011100100', '11001110100', '11101101110', '11101001100',
        '11100101100', '11100100110', '11101100100', '11100110100', '11100110010',
        '11011011000', '11011000110', '11000110110', '10100011000', '10001011000',
        '10001000110', '10110001000', '10001101000', '10001100010', '11010001000',
        '11000101000', '11000100010', '10110111000', '10110001110', '10001101110',
        '10111011000', '10111000110', '10001110110', '11101110110', '11010001110',
        '11000101110', '11011101000', '11011100010', '11011101110', '11101011000',
        '11101000110', '11100010110', '11101101000', '11101100010', '11100011010',
        '11101111010', '11001000010', '11110001010', '10100110000', '10100001100',
        '10010110000', '10010000110', '10000101100', '10000100110', '10110010000',
        '10110000100', '10011010000', '10011000010', '10000110100', '10000110010',
        '11000010010', '11001010000', '11110111010', '11000010100', '10001111010',
        '10100111100', '10010111100', '10010011110', '10111100100', '10011110100',
        '10011110010', '11110100100', '11110010100', '11110010010', '11011011110',
        '11011110110', '11110110110', '10101111000', '10100011110', '10001011110',
        '10111101000', '10111100010', '11110101000', '11110100010', '10111011110',
        '10111101110', '11101011110', '11110101110',
        '11010000100',  /* 103 START A */
        '11010010000',  /* 104 START B */
        '11010011100',  /* 105 START C */
        '1100011101011' /* 106 STOP (13 modules) */
    ];
    var START_B = 104;
    var STOP = 106;
    var QUIET = 10; /* quiet zone in modules on each side */

    /* ── Encoding ─────────────────────────────────────────────────── */
    function sanitize(code) {
        /* Code B covers ASCII 32-126; anything else is dropped. */
        code = String(code === null || code === undefined ? '' : code);
        var out = '';
        for (var i = 0; i < code.length; i++) {
            var c = code.charCodeAt(i);
            if (c >= 32 && c <= 126) out += code.charAt(i);
        }
        return out;
    }

    function mmbBarcodeModules(code) {
        code = sanitize(code);
        if (!code) return null;

        var values = [];
        for (var i = 0; i < code.length; i++) values.push(code.charCodeAt(i) - 32);

        /* checksum: (start + Σ position × value) mod 103, position from 1 */
        var sum = START_B;
        for (var p = 0; p < values.length; p++) sum += (p + 1) * values[p];
        var check = sum % 103;

        var modules = BARS[START_B];
        for (var d = 0; d < values.length; d++) modules += BARS[values[d]];
        modules += BARS[check];
        modules += BARS[STOP];
        return modules;
    }

    /* ── Canvas label rendering ───────────────────────────────────── */
    function textLines(ctx, text, maxWidth, font, weight) {
        ctx.font = weight + ' ' + font;
        var words = String(text || '').split(/\s+/).filter(Boolean);
        var lines = [], line = '';
        for (var i = 0; i < words.length; i++) {
            var test = line ? line + ' ' + words[i] : words[i];
            if (ctx.measureText(test).width > maxWidth && line) {
                lines.push(line);
                line = words[i];
            } else {
                line = test;
            }
        }
        if (line) lines.push(line);
        return lines.slice(0, 2); /* keep labels compact */
    }

    function drawLabel(canvas, code, opts) {
        opts = opts || {};
        var modules = mmbBarcodeModules(code);
        if (!modules) { canvas.width = 0; return null; }

        code = sanitize(code);
        var moduleWidth = opts.moduleWidth || 3;
        var barHeight = opts.barHeight || 80;
        var scale = opts.scale || 2; /* render at 2x for crisp printing */
        var showText = opts.showText !== false;

        var barW = moduleWidth * scale;
        var barAreaW = modules.length * barW;
        var quietW = QUIET * barW;
        var padX = Math.max(quietW, 12 * scale);
        var padY = 10 * scale;

        var ctx = canvas.getContext('2d');
        var nameFont = (13 * scale) + 'px Arial, sans-serif';
        var codeFont = 'bold ' + (15 * scale) + 'px "Courier New", monospace';

        var labelLines = showText && opts.label ? textLines(ctx, opts.label, barAreaW, nameFont, 'bold') : [];
        var nameH = labelLines.length ? labelLines.length * 17 * scale + 6 * scale : 0;
        var codeH = showText ? 24 * scale : 6 * scale;

        var width = barAreaW + padX * 2;
        var height = padY + nameH + barHeight + 8 * scale + codeH + padY;
        canvas.width = width;
        canvas.height = height;

        /* white label background */
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, width, height);

        var y = padY;

        /* product name (max 2 lines, centered) */
        if (labelLines.length) {
            ctx.fillStyle = '#111827';
            ctx.textAlign = 'center';
            ctx.font = 'bold ' + nameFont;
            for (var l = 0; l < labelLines.length; l++) {
                y += 15 * scale;
                ctx.fillText(labelLines[l], width / 2, y);
                y += 2 * scale;
            }
            y += 4 * scale;
        }

        /* bars */
        var x = padX;
        ctx.fillStyle = '#000000';
        for (var m = 0; m < modules.length; m++) {
            if (modules.charAt(m) === '1') {
                ctx.fillRect(x, y, barW, barHeight);
            }
            x += barW;
        }
        y += barHeight + 8 * scale + 14 * scale;

        /* human readable code (letter-spaced by drawing per char) */
        if (showText) {
            ctx.fillStyle = '#000000';
            ctx.font = codeFont;
            ctx.textAlign = 'center';
            ctx.fillText(code, width / 2, y);
        }

        canvas.style.maxWidth = '100%';
        canvas.dataset.code = code;
        return canvas;
    }

    function mmbBarcodeCanvas(code, opts) {
        return drawLabel(document.createElement('canvas'), code, opts);
    }

    function mmbBarcodePreview(canvasEl, code, opts) {
        if (!canvasEl) return null;
        return drawLabel(canvasEl, code, opts);
    }

    /* ── Download & shared preview modal ──────────────────────────── */
    function safeName(name) {
        return String(name || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 40);
    }

    function mmbBarcodeDownload(code, name) {
        var canvas = mmbBarcodeCanvas(code, { label: name });
        if (!canvas) return false;
        var link = document.createElement('a');
        var slug = safeName(name);
        link.download = 'barcode' + (slug ? '-' + slug : '') + '-' + sanitize(code) + '.png';
        link.href = canvas.toDataURL('image/png');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        return true;
    }

    function mmbShowBarcodeModal(code, name) {
        var modal = document.getElementById('mmbBarcodeModal');
        if (!modal) {
            if (window.mmbNotify) mmbNotify({ type: 'warning', title: 'Preview unavailable' });
            return;
        }
        var big = mmbBarcodeCanvas(code, { label: name, moduleWidth: 3, barHeight: 90 });
        var print = mmbBarcodeCanvas(code, { label: name, moduleWidth: 3, barHeight: 90 });
        var holder = modal.querySelector('#mmbBarcodeCanvasHolder');
        var printHolder = modal.querySelector('#mmbBarcodePrintHolder');
        if (!big || !holder) return;
        holder.innerHTML = '';
        big.style.width = '100%';
        holder.appendChild(big);
        if (printHolder) { printHolder.innerHTML = ''; printHolder.appendChild(print); }

        var titleEl = modal.querySelector('#mmbBarcodeModalTitle');
        if (titleEl) titleEl.textContent = name || 'Product barcode';

        var codeEl = modal.querySelector('#mmbBarcodeCodeText');
        if (codeEl) codeEl.textContent = sanitize(code);

        modal.dataset.code = sanitize(code);
        modal.dataset.name = name || '';

        if (window.bootstrap) bootstrap.Modal.getOrCreateInstance(modal).show();
        else modal.classList.add('show');
    }

    /* Buttons inside #mmbBarcodeModal (delegated so the modal markup
       only needs plain buttons — no inline onclick handlers). */
    document.addEventListener('click', function (e) {
        var modal = e.target.closest('#mmbBarcodeModal');
        if (!modal) return;

        if (e.target.closest('[data-mmb-barcode-download]')) {
            mmbBarcodeDownload(modal.dataset.code, modal.dataset.name);
            if (window.mmbNotify) {
                mmbNotify({ type: 'success', title: 'Barcode downloaded', message: 'PNG saved — ready to print and stick on the packaging.' });
            }
        }
        if (e.target.closest('[data-mmb-barcode-print]')) {
            var holder = modal.querySelector('#mmbBarcodePrintHolder canvas');
            if (!holder) return;
            var w = window.open('', '_blank', 'width=420,height=560');
            if (!w) {
                if (window.mmbNotify) mmbNotify({ type: 'warning', title: 'Pop-up blocked', message: 'Allow pop-ups to print the barcode label.' });
                return;
            }
            w.document.write('<html><head><title>Barcode label</title><style>@page{margin:6mm}body{margin:0;display:flex;align-items:center;justify-content:center;min-height:100vh}img{max-width:96mm}</style></head><body><img src="' + holder.toDataURL('image/png') + '"></body></html>');
            w.document.close();
            setTimeout(function () { w.print(); }, 250);
        }
    });

    window.mmbBarcodeModules = mmbBarcodeModules;
    window.mmbBarcodeCanvas = mmbBarcodeCanvas;
    window.mmbBarcodePreview = mmbBarcodePreview;
    window.mmbBarcodeDownload = mmbBarcodeDownload;
    window.mmbShowBarcodeModal = mmbShowBarcodeModal;
})();
