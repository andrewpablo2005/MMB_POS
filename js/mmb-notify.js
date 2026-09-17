/* ═══════════════════════════════════════════════════════════════════
   MMB Notify — custom notifications & confirmations (v1.0)
   Replaces every native alert()/confirm() in the app with the
   crimson-branded container UI requested in GitHub issue #4.
   No libraries, no dependencies. Loaded in <head> via
   conn/connection_links.php so inline PHP echoes can use it too.

   API
   ─ mmbNotify({type, title, message, duration})   → toast (top-right)
   ─ mmbAlert({type, title, message, okLabel})     → Promise<void>
   ─ mmbConfirm({title, message, okLabel, cancelLabel, danger})
                                                   → Promise<boolean>
   ─ data-mmb-confirm="message" on <a>/<button>    → auto confirmation
   ═══════════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    var ICONS = {
        success: 'fas fa-circle-check',
        danger:  'fas fa-circle-exclamation',
        warning: 'fas fa-triangle-exclamation',
        info:    'fas fa-circle-info'
    };

    function el(tag, cls, html) {
        var node = document.createElement(tag);
        if (cls) node.className = cls;
        if (html !== undefined) node.innerHTML = html;
        return node;
    }
    function esc(text) {
        return String(text === null || text === undefined ? '' : text)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    /* ── Toasts ───────────────────────────────────────────────────── */
    function container() {
        var c = document.getElementById('mmbToastStack');
        if (!c) {
            c = el('div', 'mmb-toast-stack');
            c.id = 'mmbToastStack';
            c.setAttribute('role', 'status');
            c.setAttribute('aria-live', 'polite');
            document.body.appendChild(c);
        }
        return c;
    }

    function mmbNotify(opts) {
        opts = opts || {};
        var type = ICONS[opts.type] ? opts.type : 'info';
        var duration = typeof opts.duration === 'number' ? opts.duration : 4200;

        var stack = container();
        var duplicateKey = type + '|' + String(opts.title || '') + '|' + String(opts.message || '');
        var existing = Array.prototype.find.call(stack.children, function (toast) {
            return toast.dataset.notifyKey === duplicateKey && !toast._closing;
        });
        if (existing) {
            clearTimeout(existing._timer);
            existing.classList.remove('mmb-toast--in');
            void existing.offsetWidth;
            existing.classList.add('mmb-toast--in');
            if (duration > 0) {
                existing._timer = setTimeout(function () { dismiss(existing); }, duration);
            }
            return existing;
        }

        while (stack.children.length >= 4) {
            dismiss(stack.firstElementChild);
        }

        var toast = el('div', 'mmb-toast mmb-toast--' + type);
        toast.dataset.notifyKey = duplicateKey;
        toast.innerHTML =
            '<span class="mmb-toast-icon"><i class="' + ICONS[type] + '"></i></span>' +
            '<div class="mmb-toast-body">' +
                (opts.title ? '<div class="mmb-toast-title">' + esc(opts.title) + '</div>' : '') +
                (opts.message ? '<div class="mmb-toast-message">' + esc(opts.message) + '</div>' : '') +
            '</div>' +
            '<button type="button" class="mmb-toast-close" aria-label="Dismiss"><i class="fas fa-times"></i></button>';

        toast.querySelector('.mmb-toast-close').addEventListener('click', function () { dismiss(toast); });
        stack.appendChild(toast);

        /* enter animation */
        requestAnimationFrame(function () { toast.classList.add('mmb-toast--in'); });

        if (duration > 0) {
            toast._timer = setTimeout(function () { dismiss(toast); }, duration);
        }
        return toast;
    }

    function dismiss(toast) {
        if (!toast || toast._closing) return;
        toast._closing = true;
        clearTimeout(toast._timer);
        toast.classList.remove('mmb-toast--in');
        setTimeout(function () { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 220);
    }

    /* ── Shared modal shell (confirm / alert) ─────────────────────── */
    function buildModal(kind, opts) {
        opts = opts || {};
        var type = ICONS[opts.type] ? opts.type : (kind === 'confirm' ? 'warning' : 'info');
        var icon = opts.icon || ICONS[type];

        var backdrop = el('div', 'mmb-modal-backdrop');
        var dialog = el('div', 'mmb-modal mmb-modal--' + type);
        dialog.setAttribute('role', 'dialog');
        dialog.setAttribute('aria-modal', 'true');

        dialog.innerHTML =
            '<div class="mmb-modal-icon"><i class="' + icon + '"></i></div>' +
            '<div class="mmb-modal-title">' + esc(opts.title || (kind === 'confirm' ? 'Please confirm' : 'Notice')) + '</div>' +
            (opts.message ? '<div class="mmb-modal-message">' + esc(opts.message) + '</div>' : '') +
            '<div class="mmb-modal-actions">' +
                (kind === 'confirm'
                    ? '<button type="button" class="mmb-modal-btn mmb-modal-btn--cancel">' + esc(opts.cancelLabel || 'Cancel') + '</button>'
                    : '') +
                '<button type="button" class="mmb-modal-btn mmb-modal-btn--ok ' + (opts.danger ? 'mmb-modal-btn--danger' : '') + '">' +
                    esc(opts.okLabel || (kind === 'confirm' ? 'Confirm' : 'OK')) +
                '</button>' +
            '</div>';

        backdrop.appendChild(dialog);
        document.body.appendChild(backdrop);
        requestAnimationFrame(function () { backdrop.classList.add('mmb-modal-backdrop--in'); });

        var done = false;
        function close(result) {
            if (done) return;
            done = true;
            document.removeEventListener('keydown', onKey);
            backdrop.classList.remove('mmb-modal-backdrop--in');
            setTimeout(function () { if (backdrop.parentNode) backdrop.parentNode.removeChild(backdrop); }, 180);
            if (kind === 'confirm') resolve(result); else resolve();
        }
        var resolve;

        var okBtn = dialog.querySelector('.mmb-modal-btn--ok');
        var cancelBtn = dialog.querySelector('.mmb-modal-btn--cancel');
        okBtn.addEventListener('click', function () { close(true); });
        if (cancelBtn) cancelBtn.addEventListener('click', function () { close(false); });
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) close(kind === 'confirm' ? false : true);
        });
        function onKey(e) { if (e.key === 'Escape') close(false); }
        document.addEventListener('keydown', onKey);

        var focusBtn = (kind === 'confirm' && opts.danger) ? cancelBtn : okBtn;
        setTimeout(function () { focusBtn.focus(); }, 60);

        return new Promise(function (res) { resolve = res; });
    }

    function mmbConfirm(opts) { return buildModal('confirm', opts); }
    function mmbAlert(opts)   { return buildModal('alert', opts); }

    /* ── data-mmb-confirm delegation (links, buttons, form submits) ─ */
    var pending = new WeakSet();

    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('[data-mmb-confirm]');
        if (!trigger || pending.has(trigger)) return;

        e.preventDefault();
        e.stopPropagation();
        pending.add(trigger);

        var danger = /delete|remove|dispose|void|disable/i.test(trigger.getAttribute('data-mmb-confirm') || '') ||
                     (trigger.className || '').indexOf('btn-danger') !== -1;

        mmbConfirm({
            title: danger ? 'Are you sure?' : 'Please confirm',
            message: trigger.getAttribute('data-mmb-confirm'),
            okLabel: trigger.getAttribute('data-mmb-ok') || (danger ? 'Yes, proceed' : 'Confirm'),
            cancelLabel: 'Cancel',
            danger: danger
        }).then(function (yes) {
            pending.delete(trigger);
            if (!yes) return;

            if (trigger.tagName === 'A' && trigger.getAttribute('href')) {
                window.location.href = trigger.href;
                return;
            }
            var form = trigger.closest('form');
            if (form) {
                if (typeof form.requestSubmit === 'function') {
                    /* requestSubmit(submitter) keeps the button's name/value in the POST */
                    form.requestSubmit(trigger);
                } else {
                    trigger.removeAttribute('data-mmb-confirm');
                    trigger.click();
                }
                return;
            }
            /* generic element: re-run its own click handlers without our gate */
            trigger.removeAttribute('data-mmb-confirm');
            trigger.click();
        });
    }, true);

    /* Expose globals (available to inline PHP echoes) */
    window.mmbNotify = mmbNotify;
    window.mmbAlert = mmbAlert;
    window.mmbConfirm = mmbConfirm;
})();
