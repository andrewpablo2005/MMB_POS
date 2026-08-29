<!-- ═══════════════ PROCESS RETURN & REFUND MODAL ═══════════════ -->
<div class="wepos-modal-overlay" id="returnModal" style="display:none;" onclick="event.stopPropagation()">
    <div class="wepos-modal" style="max-width: 650px;" onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div class="wepos-modal-head">
            <h5 style="margin:0; display:flex; align-items:center; gap:.65rem; font-weight:700; color:#1a2535;">
                <span class="modal-head-icon"><i class="fas fa-undo-alt"></i></span> Process Product Return / Refund
            </h5>
            <button type="button" onclick="closeReturnModal()" style="color:#64748b;"><i class="fas fa-times"></i></button>
        </div>

        <!-- Modal Body -->
        <div class="wepos-modal-body" style="padding: 20px;">
            
            <!-- Step 1: Search Original Transaction -->
            <div class="mb-3">
                <label style="font-weight: 600; font-size: 0.85rem; color: #374151; margin-bottom: 5px; display: block;">
                    Enter Transaction Ref # / Receipt ID
                </label>
                <div style="display: flex; gap: 8px;">
                    <div style="position: relative; flex: 1;">
                        <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="number" id="returnSearchTxId" placeholder="e.g. 12" style="width: 100%; padding: 8px 12px 8px 36px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;" onkeypress="if(event.key==='Enter'){ event.preventDefault(); fetchReturnTxDetails(); }">
                    </div>
                    <button type="button" class="btn btn-primary" onclick="fetchReturnTxDetails()" style="padding: 8px 16px;">
                        Lookup
                    </button>
                </div>
                <div id="returnSearchError" class="text-danger mt-1" style="font-size: 0.82rem; display: none;"></div>
            </div>

            <!-- Transaction Details Container (hidden until found) -->
            <div id="returnTxContainer" style="display: none;">
                
                <!-- Info Header Card -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; margin-bottom: 15px; font-size: 0.83rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span>Ref #: <strong id="lblReturnTxId" style="color:#c0392b;">#000000</strong></span>
                        <span id="lblReturnTxDate" style="color: #64748b;"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Customer: <strong id="lblReturnCustomer">Walk-in</strong></span>
                        <span>Original Total: <strong id="lblReturnTxTotal" style="color: #b91c1c;">₱0.00</strong></span>
                    </div>
                </div>

                <!-- Items Table -->
                <div style="max-height: 220px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px;">
                    <div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.83rem;">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 2;">
                            <tr>
                                <th style="width: 30px;"></th>
                                <th>Product</th>
                                <th class="text-center" style="width: 70px;">Price</th>
                                <th class="text-center" style="width: 100px;">Return Qty</th>
                                <th class="text-center" style="width: 100px;">Condition</th>
                            </tr>
                        </thead>
                        <tbody id="returnItemsBody">
                            <!-- Populated via JS -->
                        </tbody>
                    </table></div>
                </div>

                <!-- Return Reason & Method -->
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 3px;">Return Reason</label>
                        <select id="returnReason" class="form-select form-select-sm">
                            <option value="Customer Request / Change of Mind">Customer Request / Change of Mind</option>
                            <option value="Wrong Item Purchased">Wrong Item Purchased</option>
                            <option value="Defective / Damaged Packaging">Defective / Damaged Packaging</option>
                            <option value="Near Expiry / Expired">Near Expiry / Expired</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 3px;">Refund Method</label>
                        <select id="returnRefundMethod" class="form-select form-select-sm">
                            <option value="Cash">Cash Refund</option>
                        </select>
                    </div>
                </div>

                <!-- Manager PIN Authorization -->
                <div style="background: #fff3cd; border: 1px solid #ffeeba; border-radius: 8px; padding: 12px; margin-bottom: 15px;">
                    <div style="font-size: 0.82rem; font-weight: 700; color: #856404; margin-bottom: 6px;">
                        <i class="fas fa-shield-alt me-1"></i> Manager or Owner PIN Required
                    </div>
                    <input type="password" id="returnVoidPin" placeholder="Enter 7-Digit Void PIN" maxlength="7" autocomplete="off" required style="width: 100%; border-radius: 6px; border: 1px solid #d97706; padding: 8px; font-size: 1rem; text-align: center; letter-spacing: 4px;">
                </div>
                <div id="returnPinError" class="text-danger mt-1" style="font-size: 0.82rem; display: none;"></div>

                <!-- Total Refund Summary -->
                <div style="display: flex; justify-content: space-between; align-items: center; background: #fff5f5; border: 1px solid #fee2e2; border-radius: 8px; padding: 12px 16px;">
                    <div>
                        <div style="font-size: 0.78rem; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Total Refund Amount</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: #c0392b;" id="returnTotalAmount">₱0.00</div>
                    </div>
                    <button type="button" class="btn btn-danger" id="btnSubmitReturn" onclick="submitReturnProcess()" style="padding: 10px 20px; font-weight: 700;">
                        <i class="fas fa-check-circle me-1"></i> Confirm Refund
                    </button>
                </div>

            </div>

        </div>

    </div>
</div>

<!-- ═══════════════ REFUND RECEIPT MODAL ═══════════════ -->
<div class="wepos-modal-overlay" id="refundReceiptModal" style="display:none;" onclick="event.stopPropagation()">
    <div class="wepos-modal" onclick="event.stopPropagation()" style="max-width:380px; border-radius:8px;">
        <div class="wepos-modal-head" style="background:#fff5f5; border-bottom:1px solid #fee2e2;">
            <h5 style="color:#c0392b;"><i class="fas fa-undo"></i> Refund Voucher</h5>
            <button type="button" onclick="closeRefundReceiptModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="wepos-modal-body" style="padding:0;">
            <div id="refundReceiptPrint" style="padding:20px; font-family:'Courier New',monospace; font-size:13px;">
                <div style="text-align:center; margin-bottom:12px;">
                    <div style="font-size:16px; font-weight:700;">MMB'S DRUGSTORE</div>
                    <div style="font-size:11px; color:#c0392b; font-weight:700;">CREDIT VOUCHER / REFUND</div>
                    <div style="font-size:11px; color:#64748b;" id="refundReceiptDateTime"></div>
                </div>
                <div style="border-top:1px dashed #cbd5e1; border-bottom:1px dashed #cbd5e1; padding:6px 0; margin-bottom:10px; font-size:12px;">
                    <div><span style="color:#64748b;">Return Ref #:</span> <strong id="refundReceiptRefNo"></strong></div>
                    <div><span style="color:#64748b;">Original Tx #:</span> <strong id="refundReceiptOrigTx"></strong></div>
                </div>
                <div id="refundReceiptItems" style="margin-bottom:10px;"></div>
                <div style="border-top:1px dashed #cbd5e1; padding-top:8px; font-size:14px; font-weight:700; display:flex; justify-content:space-between; color:#c0392b;">
                    <span>TOTAL REFUNDED</span><span id="refundReceiptTotal"></span>
                </div>
                <div style="text-align:center; margin-top:14px; font-size:11px; color:#94a3b8;">
                    Refund processed & verified.<br>
                    Thank you.
                </div>
            </div>
        </div>
        <div class="wepos-modal-foot" style="gap:10px;">
            <button type="button" class="wepos-btn wepos-btn-outline" onclick="printRefundReceipt()">
                <i class="fas fa-print"></i> Print Voucher
            </button>
            <button type="button" class="wepos-btn wepos-btn-primary" onclick="closeRefundReceiptModal()">
                Done
            </button>
        </div>
    </div>
</div>

<script>

    // XSS guard for user-editable product/customer names
    function escapeHtmlRm(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

let currentReturnTxData = null;

function openReturnModal() {
    document.getElementById('returnSearchTxId').value = '';
    document.getElementById('returnSearchError').style.display = 'none';
    document.getElementById('returnTxContainer').style.display = 'none';
    document.getElementById('returnModal').style.display = 'flex';
    setTimeout(() => document.getElementById('returnSearchTxId')?.focus(), 100);
}

function closeReturnModal() {
    document.getElementById('returnModal').style.display = 'none';
}

async function fetchReturnTxDetails() {
    const txId = document.getElementById('returnSearchTxId').value.trim();
    const errEl = document.getElementById('returnSearchError');
    errEl.style.display = 'none';

    if (!txId) {
        errEl.textContent = 'Please enter a transaction Ref # / ID.';
        errEl.style.display = 'block';
        return;
    }

    try {
        const res = await fetch(`../function/get_transaction_details.php?id=${txId}`);
        const data = await res.json();

        if (!data.success) {
            errEl.textContent = data.error || 'Transaction not found.';
            errEl.style.display = 'block';
            document.getElementById('returnTxContainer').style.display = 'none';
            return;
        }

        currentReturnTxData = data;
        renderReturnTxUI(data);

    } catch (err) {
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
    }
}

function renderReturnTxUI(data) {
    const tx = data.transaction;
    document.getElementById('lblReturnTxId').textContent = '#' + String(tx.id).padStart(6, '0');
    document.getElementById('lblReturnTxDate').textContent = tx.created_at;
    document.getElementById('lblReturnCustomer').textContent = tx.customer_name || 'Walk-in';
    document.getElementById('lblReturnTxTotal').textContent = '₱' + parseFloat(tx.total_amount).toFixed(2);

    let html = '';
    data.items.forEach(item => {
        const avail = item.available_for_return;
        const disabled = avail <= 0;

        html += `
            <tr style="${disabled ? 'opacity:0.5; background:#f8fafc;' : ''}">
                <td class="text-center">
                    <input type="checkbox" class="form-check-input chk-return-item" data-pid="${item.product_id}" data-price="${item.price}" data-max="${avail}" ${disabled ? 'disabled' : ''} onchange="calcReturnTotal()">
                </td>
                <td>
                    <div style="font-weight:600;">${escapeHtmlRm(item.product_name)}</div>
                    <small style="color:#64748b;">Purchased: ${item.quantity} | Returned: ${item.already_returned} | Avail: ${avail}</small>
                </td>
                <td class="text-center">₱${parseFloat(item.price).toFixed(2)}</td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm num-return-qty" id="returnQty_${item.product_id}" value="${avail > 0 ? 1 : 0}" min="1" max="${avail}" ${disabled ? 'disabled' : ''} style="width:65px; text-align:center; margin:0 auto;" oninput="calcReturnTotal()">
                </td>
                <td class="text-center">
                    <select class="form-select form-select-sm sel-return-condition" id="returnCond_${item.product_id}" ${disabled ? 'disabled' : ''} style="font-size:0.75rem;">
                        <option value="1">Restock</option>
                        <option value="0">Dispose</option>
                    </select>
                </td>
            </tr>
        `;
    });

    document.getElementById('returnItemsBody').innerHTML = html;
    const pinEl = document.getElementById('returnVoidPin');
    if (pinEl) pinEl.value = '';
    const pinErrEl = document.getElementById('returnPinError');
    if (pinErrEl) pinErrEl.style.display = 'none';
    document.getElementById('returnTxContainer').style.display = 'block';
    calcReturnTotal();
}

function calcReturnTotal() {
    let total = 0;
    document.querySelectorAll('.chk-return-item').forEach(chk => {
        if (chk.checked && !chk.disabled) {
            const pid = chk.dataset.pid;
            const price = parseFloat(chk.dataset.price) || 0;
            const qtyInput = document.getElementById(`returnQty_${pid}`);
            const qty = parseInt(qtyInput?.value) || 0;
            total += price * qty;
        }
    });

    document.getElementById('returnTotalAmount').textContent = '₱' + total.toFixed(2);
}

async function submitReturnProcess() {
    if (!currentReturnTxData) return;

    const pinEl = document.getElementById('returnVoidPin');
    const pin = pinEl ? pinEl.value.trim() : '';
    const pinErr = document.getElementById('returnPinError');
    if (pinErr) pinErr.style.display = 'none';

    if (!pin) {
        if (pinErr) {
            pinErr.textContent = 'Please enter Manager Void PIN.';
            pinErr.style.display = 'block';
        }
        return;
    }

    const selectedItems = [];
    document.querySelectorAll('.chk-return-item').forEach(chk => {
        if (chk.checked && !chk.disabled) {
            const pid = chk.dataset.pid;
            const price = parseFloat(chk.dataset.price) || 0;
            const qty = parseInt(document.getElementById(`returnQty_${pid}`)?.value) || 0;
            const isRestockable = document.getElementById(`returnCond_${pid}`)?.value === '1';

            if (qty > 0) {
                selectedItems.push({
                    product_id: parseInt(pid),
                    qty: qty,
                    price: price,
                    is_restockable: isRestockable
                });
            }
        }
    });

    if (selectedItems.length === 0) {
        pinErr.textContent = 'Please select at least one item to return.';
        pinErr.style.display = 'block';
        return;
    }

    const btn = document.getElementById('btnSubmitReturn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';

    try {
        const payload = {
            original_transaction_id: currentReturnTxData.transaction.id,
            void_pin: pin,
            refund_method: document.getElementById('returnRefundMethod').value,
            reason: document.getElementById('returnReason').value,
            items: selectedItems
        };

        const res = await fetch('../function/process_return.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.success) {
            closeReturnModal();
            showRefundReceipt(data, selectedItems);
        } else {
            if (pinErr) {
                pinErr.textContent = data.error || 'Failed to process return.';
                pinErr.style.display = 'block';
            } else {
                alert(data.error || 'Failed to process return.');
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirm Refund';
        }

    } catch (err) {
        if (pinErr) {
            pinErr.textContent = 'Network error. Please try again.';
            pinErr.style.display = 'block';
        } else {
            alert('Network error. Please try again.');
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirm Refund';
    }
}

function showRefundReceipt(data, items) {
    const now = new Date();
    document.getElementById('refundReceiptDateTime').textContent =
        now.toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' }) + ' ' +
        now.toLocaleTimeString('en-PH', { hour:'2-digit', minute:'2-digit' });
    document.getElementById('refundReceiptRefNo').textContent = '#' + String(data.return_id).padStart(6, '0');
    document.getElementById('refundReceiptOrigTx').textContent = '#' + String(currentReturnTxData.transaction.id).padStart(6, '0');

    let itemsHtml = '';
    items.forEach(item => {
        const origItem = currentReturnTxData.items.find(i => i.product_id == item.product_id);
        const name = origItem ? origItem.product_name : 'Product #' + item.product_id;
        itemsHtml += `
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <div>${escapeHtmlRm(name)}<br><small style="color:#64748b;">${item.qty} x ₱${item.price.toFixed(2)}</small></div>
                <div style="font-weight:700;">₱${(item.qty * item.price).toFixed(2)}</div>
            </div>
        `;
    });

    document.getElementById('refundReceiptItems').innerHTML = itemsHtml;
    document.getElementById('refundReceiptTotal').textContent = '₱' + parseFloat(data.refund_total).toFixed(2);
    document.getElementById('refundReceiptModal').style.display = 'flex';
}

function closeRefundReceiptModal() {
    document.getElementById('refundReceiptModal').style.display = 'none';
    window.location.reload();
}

function printRefundReceipt() {
    const content = document.getElementById('refundReceiptPrint').innerHTML;
    const win = window.open('', '_blank', 'width=320,height=600');
    win.document.write(`
        <html><head><title>Refund Voucher</title>
        <style>
            body { font-family: 'Courier New', monospace; font-size: 13px; width: 280px; margin: 0 auto; padding: 10px; }
            @media print { body { margin: 0; } }
        </style></head>
        <body>${content}</body></html>
    `);
    win.document.close();
    win.focus();
    setTimeout(() => { win.print(); win.close(); }, 300);
}
</script>
