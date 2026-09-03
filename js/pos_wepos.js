let weposCart = {};
let barcodeBuffer = '';
let barcodeTimer = null;
let currentPayMethod = 'Cash';
let pendingOverride = null;
let pendingVoid = null;           // stores cart item id pending void auth
let pendingVoidAction = null;     // 'delete', 'decrement', or 'set'
let pendingVoidTargetQty = null;  // target quantity for set actions
let pendingClearCart = false;     // tracks if void auth is for clearing entire cart
let pendingDiscountIndex = null;  // stores previous discount index if user cancels verify
let weposVerified = false;        // tracks if Senior/PWD customer has been verified this session
let weposCustomerType = null;     // tracks customer type: 'senior', 'pwd', or null for regular
let weposCustomerName = null;     // tracks customer name for senior/pwd customers
let weposCustomerId = null;       // tracks customer ID linking to senior_customers/pwd_customers table
let weposLastReceiptData = null;  // stores last receipt data for printing
let weposIdLookupTimer = null;    // debounce timer for the ID-number name lookup
let weposIdLookupSeq = 0;         // guards against out-of-order lookup responses
let weposIdNameAutoFilled = '';   // value last auto-filled by the lookup (so manual typing is never clobbered)

// XSS guard: product/customer names are user-editable data and must be
// escaped before being interpolated into innerHTML templates.
function weposEscapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

document.addEventListener('DOMContentLoaded', () => {
    try {
        console.log('DOMContentLoaded event firing, initializing wePOS...');
        weposSetupScanner();
        weposSetupSearch();
        weposSetupKeyboard();
        weposSetupIdLookup();
        weposUpdateCart();
        console.log('wePOS initialization complete');
    } catch (error) {
        console.error('Error during wePOS initialization:', error);
    }
});

// ═════ BARCODE SCANNER ═════
function weposSetupScanner() {
    document.addEventListener('keypress', function(e) {
        const active = document.activeElement;
        const isSearch = active && active.id === 'weposSearch';
        const isModalInput = active && (active.id === 'weposTendered' || active.id === 'weposCustomer' || active.id === 'overrideReason' || active.id === 'overrideUsername' || active.id === 'overridePassword' || active.id === 'overridePercent' || active.id === 'verifyIdName' || active.id === 'verifyIdNumber' || active.id === 'voidAuthPin');

        if (isModalInput) return;

        if (e.key === 'Enter') {
            e.preventDefault();
            if (isSearch && active.value.trim().length > 0) {
                weposFindAndAdd(active.value.trim());
                active.value = '';
            } else if (barcodeBuffer.length > 2) {
                weposFindAndAdd(barcodeBuffer);
            }
            barcodeBuffer = '';
            return;
        }

        if (!isSearch) {
            barcodeBuffer += e.key;
            clearTimeout(barcodeTimer);
            barcodeTimer = setTimeout(() => { barcodeBuffer = ''; }, 80);
        }
    });
}

function weposFindAndAdd(code) {
    const cards = document.querySelectorAll('.wepos-product-card:not(.out-of-stock):not(.expired)');
    for (const card of cards) {
        if (card.dataset.barcode === code) {
            weposAddToCart(card);
            return true;
        }
    }
    const lc = code.toLowerCase();
    for (const card of cards) {
        if (card.dataset.name.toLowerCase().includes(lc)) {
            weposAddToCart(card);
            return true;
        }
    }
    mmbNotify({ type: 'warning', title: 'Product not found', message: 'No product matches "' + code + '" (or it is out of stock).' });
    return false;
}

// ═════ SEARCH & FILTER ═════
function weposSetupSearch() {
    const search = document.getElementById('weposSearch');
    if (search) {
        search.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.wepos-product-card').forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const barcode = (card.dataset.barcode || '').toLowerCase();
                card.style.display = (!query || name.includes(query) || barcode.includes(query)) ? '' : 'none';
            });
        });
    }
}

function weposFilterCat(category) {
    document.querySelectorAll('.wepos-cat-btn').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');

    document.querySelectorAll('.wepos-product-card').forEach(card => {
        if (category === 'All' || card.dataset.category === category) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
    const search = document.getElementById('weposSearch');
    if (search) search.value = '';
}

// ═════ CART LOGIC ═════
function weposAddToCart(cardEl) {
    console.log('weposAddToCart called with element:', cardEl?.dataset?.id);
    
    // Validate that cardEl is provided and is an element
    if (!cardEl || !cardEl.dataset) {
        console.error('Invalid element passed to weposAddToCart');
        mmbNotify({ type: 'danger', title: 'Could not add product', message: 'Please refresh the page and try again.' });
        return;
    }
    
    const id = cardEl.dataset.id;
    const stock = parseInt(cardEl.dataset.stock) || 0;
    const isExpired = cardEl.dataset.expired === '1';
    
    // Validate required data
    if (!id) {
        console.error('Product ID missing from data attributes', cardEl.dataset);
        mmbNotify({ type: 'danger', title: 'Incomplete product data', message: 'Please refresh the page and try again.' });
        return;
    }
    
    console.log('Product ID:', id, 'Stock:', stock, 'Expired:', isExpired);

    if (isExpired) {
        mmbNotify({ type: 'danger', title: 'Expired product', message: 'This product has expired and cannot be sold.' });
        return;
    }

    if (stock <= 0) {
        mmbNotify({ type: 'warning', title: 'Out of stock', message: 'This item has no available stock.' });
        return;
    }

    if (weposCart[id]) {
        if (weposCart[id].qty >= stock) {
            mmbNotify({ type: 'warning', title: 'Stock limit reached', message: 'Only ' + stock + ' unit(s) available.' });
            return;
        }
        weposCart[id].qty++;
    } else {
        const price = parseFloat(cardEl.dataset.price);
        const net = parseFloat(cardEl.dataset.net || cardEl.dataset.price);
        const unitsPerPackage = parseInt(cardEl.dataset.units_per_package || cardEl.dataset.pcs) || 1;
        
        // Validate prices
        if (isNaN(price) || price < 0) {
            console.error('Invalid price:', cardEl.dataset.price, 'parsed to:', price);
            mmbNotify({ type: 'danger', title: 'Invalid price', message: 'Product price looks wrong — please refresh the page.' });
            return;
        }
        
        weposCart[id] = {
            id: id,
            name: cardEl.dataset.name,
            branded: cardEl.dataset.branded || '',
            generic: cardEl.dataset.generic || '',
            strength: cardEl.dataset.strength || '',
            form: cardEl.dataset.form || '',
            price: price,
            net: isNaN(net) ? price : net,
            qty: 1,
            unitsPerPackage: unitsPerPackage,
            // Category-based flags (auto-apply from DB)
            // Default to VATable when attribute is missing to ensure VAT shows
            hasVat: (('hasVat' in cardEl.dataset) ? cardEl.dataset.hasVat === '1' : true),
            senior: cardEl.dataset.senior === '1',
            pwd: cardEl.dataset.pwd === '1',
            stock: stock,
            // Override state (set during checkout)
            override: false,
            overrideRate: 0,
            overrideApprover: null
        };
        
        // Debug: log when a product is marked non-VATable to assist troubleshooting
        if (!weposCart[id].hasVat) {
            console.debug('Product marked non-VATable:', id, weposCart[id].name, 'data-has-vat=', cardEl.dataset.hasVat);
        }
        console.log('Added product to cart:', weposCart[id]);
    }

    // Visual feedback
    cardEl.classList.add('flash-add');
    setTimeout(() => cardEl.classList.remove('flash-add'), 300);

    weposUpdateCart();
}

function weposUpdateQty(id, delta) {
    // Ensure id and delta are proper types
    id = String(id).trim();
    delta = Number(delta);
    
    if (!weposCart[id]) {
        console.warn('Item not in cart:', id);
        return;
    }

    // Require void auth for any decrement action
    if (delta < 0) {
        weposRequestVoidAuth(id, 'decrement');
        return;
    }

    weposCart[id].qty = Number(weposCart[id].qty) + delta;
    if (weposCart[id].qty <= 0) {
        delete weposCart[id];
    } else if (weposCart[id].qty > weposCart[id].stock) {
        weposCart[id].qty = weposCart[id].stock;
        mmbNotify({ type: 'warning', title: 'Maximum stock reached' });
    }
    
    weposUpdateCart();
}

function weposSetQty(id, value) {
    id = String(id).trim();
    if (!weposCart[id]) {
        console.warn('Item not in cart:', id);
        return;
    }

    const originalQty = weposCart[id].qty;
    let qty = parseInt(String(value).replace(/[^0-9]/g, ''), 10);
    if (Number.isNaN(qty)) {
        qty = originalQty;
    }

    if (qty <= 0) {
        weposRequestVoidAuth(id, 'delete');
        // Restore the original quantity immediately until auth completes
        const input = document.querySelector(`input[data-cart-item="${id}"]`);
        if (input) input.value = originalQty;
        return;
    }

    if (qty < originalQty) {
        // require void auth for decreasing quantity
        pendingVoidTargetQty = qty;
        weposRequestVoidAuth(id, 'set');
        // Restore the original quantity immediately until auth completes
        const input = document.querySelector(`input[data-cart-item="${id}"]`);
        if (input) input.value = originalQty;
        return;
    }

    if (qty > weposCart[id].stock) {
        qty = weposCart[id].stock;
        mmbNotify({ type: 'warning', title: 'Maximum stock reached' });
    }

    weposCart[id].qty = qty;
    weposUpdateCart();
}

function weposStoreQtyOriginal(input) {
    input.dataset.originalQty = input.value;
}

function weposHandleQtyKey(event, input) {
    const id = input.getAttribute('data-cart-item');
    if (!id || !weposCart[id]) return;
    const originalQty = Number(input.dataset.originalQty || weposCart[id].qty);

    if (event.key === 'ArrowDown' || event.key === 'PageDown') {
        event.preventDefault();
        if (originalQty > 1) {
            pendingVoidTargetQty = originalQty - 1;
            weposRequestVoidAuth(id, 'set');
        } else {
            weposRequestVoidAuth(id, 'delete');
        }
        input.value = originalQty;
    }
}

function weposHandleQtyInput(event, input) {
    const id = input.getAttribute('data-cart-item');
    if (!id || !weposCart[id]) return;
    const originalQty = Number(input.dataset.originalQty || weposCart[id].qty);
    const newQty = parseInt(String(input.value).replace(/[^0-9]/g, ''), 10);

    if (Number.isNaN(newQty) || newQty === originalQty) {
        return;
    }

    if (newQty < originalQty) {
        pendingVoidTargetQty = newQty;
        weposRequestVoidAuth(id, 'set');
        input.value = originalQty;
        return;
    }

    if (newQty > weposCart[id].stock) {
        input.value = weposCart[id].stock;
        mmbNotify({ type: 'warning', title: 'Maximum stock reached' });
        return;
    }
}

function weposRequestVoidAuth(id, action) {
    const item = weposCart[id];
    if (!item) {
        console.warn('Item not found in cart:', id);
        return;
    }

    pendingVoid = id;
    pendingVoidAction = action;
    pendingVoidTargetQty = action === 'set' ? pendingVoidTargetQty : null;

    document.getElementById('voidItemPreview').innerHTML =
        `<strong>${weposEscapeHtml(item.name)}</strong> &times; ${item.qty} &mdash; &#8369;${(item.price * item.qty).toFixed(2)}`;
    const modalHead = document.querySelector('#voidAuthModal .wepos-modal-head h5');
    if (modalHead) {
        modalHead.innerHTML = '<i class="fas fa-trash-alt"></i> Void Authorization';
    }
    const instructions = document.querySelector('#voidAuthModal .wepos-modal-body p');
    if (instructions) {
        instructions.textContent = 'Please enter the 7-digit Manager Void PIN to authorize quantity decrease or item removal.';
    }
    document.getElementById('voidAuthPin').value = '';
    document.getElementById('voidAuthError').style.display = 'none';
    const btn = document.getElementById('voidAuthBtn');
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash"></i> Confirm Remove';
    }
    document.getElementById('voidAuthModal').style.display = 'flex';
    setTimeout(() => document.getElementById('voidAuthPin')?.focus(), 100);
}

function weposRemoveItem(id) {
    id = String(id).trim();
    const item = weposCart[id];
    
    if (!item) {
        console.warn('Item not found in cart:', id);
        return;
    }

    // Intercept with void authorization modal
    pendingVoid = id;
    pendingVoidAction = 'delete';
    document.getElementById('voidItemPreview').innerHTML =
        `<strong>${weposEscapeHtml(item.name)}</strong> &times; ${item.qty} &mdash; &#8369;${(item.price * item.qty).toFixed(2)}`;
    document.getElementById('voidAuthPin').value = '';
    document.getElementById('voidAuthError').style.display = 'none';
    const btn = document.getElementById('voidAuthBtn');
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash"></i> Confirm Remove';
    }
    document.getElementById('voidAuthModal').style.display = 'flex';
    setTimeout(() => document.getElementById('voidAuthPin')?.focus(), 100);
}

function weposClearCart() {
    if (Object.keys(weposCart).length === 0) return;
    
    // Show void auth modal for clearing entire cart
    pendingClearCart = true;
    const cartItems = Object.values(weposCart);
    const cartTotal = cartItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const itemCount = cartItems.length;
    
    document.getElementById('voidItemPreview').innerHTML =
        `<strong><i class="fas fa-exclamation-triangle" style="color: #dc2626; margin-right: 8px;"></i>Clear Entire Cart</strong><br><br>` +
        `<small>${itemCount} item(s) • Total: &#8369;${cartTotal.toFixed(2)}</small>`;
    document.getElementById('voidAuthPin').value = '';
    document.getElementById('voidAuthError').style.display = 'none';
    
    const btn = document.getElementById('voidAuthBtn');
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash-alt"></i> Clear Cart';
    }
    
    // Update modal title
    const modalHead = document.querySelector('#voidAuthModal .wepos-modal-head h5');
    if (modalHead) {
        modalHead.innerHTML = '<i class="fas fa-trash-alt"></i> Clear Cart Authorization';
    }
    
    // Update modal instructions
    const instructions = document.querySelector('#voidAuthModal .wepos-modal-body p');
    if (instructions) {
        instructions.textContent = 'Please enter the 7-digit Manager Void PIN to clear the entire cart.';
    }
    
    document.getElementById('voidAuthModal').style.display = 'flex';
    setTimeout(() => document.getElementById('voidAuthPin')?.focus(), 100);
}

// ═════ MATH & DISCOUNT CALCULATION ═════
function weposNormalizeRate(rate) {
    const numeric = parseFloat(rate) || 0;
    return numeric > 1 ? numeric / 100 : numeric;
}

function weposFormatCurrency(value) {
    const numeric = Number(value) || 0;
    return '₱' + numeric.toFixed(2);
}

function weposCalcItem(item, dRate, isVatExempt, discountRule = 'regular') {
    // Helper: round to 2 decimals (for display and stored receipt values)
    const round2 = (n) => Math.round((Number(n) + Number.EPSILON) * 100) / 100;

    const gross = Number(item.price) * Number(item.qty || 1);
    let vatExempt = 0;
    let discount = 0;
    let vatAmount = 0;
    let finalPrice = gross;

    // Category-level VAT: treat all categories as VAT-inclusive per configuration
    // (This enforces VAT display for every product regardless of product-level flags)
    const isVatable = true;

    // Manual override short-circuit
    if (item.override && item.overrideRate > 0) {
        let base = gross;
        if (isVatable) {
            const net = gross / 1.12;
            vatExempt = net ? gross - net : 0;
            base = net;
        }
        discount = base * item.overrideRate;
        finalPrice = gross - vatExempt - discount;
        return {
            gross: round2(gross),
            vatExempt: round2(vatExempt),
            discount: round2(discount),
            vatAmount: round2(vatAmount),
            final: round2(finalPrice)
        };
    }

    // Compute VAT portion when price is VAT-inclusive
    if (isVatable) {
        const net = gross / 1.12;
        vatAmount = gross - net;      // VAT portion of the gross price
        // Do not change finalPrice yet; discount rules below will set payable
    } else {
        vatAmount = 0;
    }

    // Apply discounts
    const isStatutory = discountRule === 'statutory';
    const seniorEligible = item.senior === true;
    const pwdEligible = item.pwd === true;

    if (dRate > 0) {
        if (isStatutory) {
            if (seniorEligible || pwdEligible) {
                if (isVatable) {
                    // Official law: remove VAT first, then compute 20% on VAT-exclusive price
                    const net = gross / 1.12;
                    vatExempt = gross - net;           // amount of VAT removed
                    discount = net * dRate;           // discount computed on VAT-exclusive price
                    finalPrice = net - discount;      // amount payable after discount
                } else {
                    // If product is non-VATable, apply discount on gross (no VAT split)
                    discount = gross * dRate;
                    finalPrice = gross - discount;
                }
            } else {
                // Statutory discount does not apply to non-eligible items
                discount = 0;
                finalPrice = gross;
            }
        } else {
            // Non-statutory discounts apply on gross
            discount = gross * dRate;
            finalPrice = gross - discount;
        }
    }

    return {
        gross: round2(gross),
        vatExempt: round2(vatExempt),
        discount: round2(discount),
        vatAmount: round2(vatAmount),
        final: round2(finalPrice)
    };
}

function weposUpdateCart() {
    const tbody = document.getElementById('weposCartBody');
    
    // Safety check - if cart body element doesn't exist, exit
    if (!tbody) {
        console.error('Cart body element (weposCartBody) not found in DOM');
        return;
    }
    
    const entries = Object.values(weposCart);

    if (entries.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="wepos-empty-cart">
                    <div class="wepos-empty-cart-icon"><i class="fas fa-basket-shopping"></i></div>
                    <p>Cart is empty</p>
                    <small>Scan barcode or click products to add</small>
                </td>
            </tr>`;
        weposSetTotals(0, 0, 0, 0, 0, 0);
        document.getElementById('weposPayBtn').disabled = true;
        return;
    }

    // Get discount info
    const discountSelect = document.getElementById('weposDiscount');
    if (!discountSelect) {
        console.warn('Discount select element not found');
        weposSetTotals(0, 0, 0, 0, 0, 0);
        return;
    }
    
    const selOpt = discountSelect.options[discountSelect.selectedIndex];
    if (!selOpt) {
        console.warn('No discount option selected or available');
        weposSetTotals(0, 0, 0, 0, 0, 0);
        return;
    }
    
    const discountRule = selOpt?.dataset?.rule || 'regular';
    const isSpecialDiscount = discountRule === 'statutory';
    const selectedRate = weposNormalizeRate(selOpt.dataset.rate);
    const dRate = isSpecialDiscount ? (weposVerified ? selectedRate : 0) : selectedRate;
    const isVatExempt = selOpt.dataset.exempt === '1';

    let html = '';
    let rawSubtotal = 0;
    let totalDiscount = 0;
    let totalVatExemption = 0;
    let rawVat = 0;
    let totalFinalAmount = 0;

    entries.forEach(item => {
        const lineTotal = item.price * item.qty;
        rawSubtotal += lineTotal;

        const c = weposCalcItem(item, dRate, isVatExempt, discountRule);
        totalDiscount += c.discount;
        totalVatExemption += c.vatExempt;
        rawVat += c.vatAmount;
        totalFinalAmount += c.final;

        const overrideBadge = item.override
            ? ` <span style="font-size:10px; color:#b91c1c; font-weight:600;"><i class="fas fa-tag"></i> ${(item.overrideRate*100).toFixed(0)}% OFF</span>`
            : '';
        const quantityPresets = [1, 6, 12]
            .filter(quantity => quantity <= item.stock)
            .concat(item.qty > 0 && ![1, 6, 12].includes(item.qty) ? [item.qty] : [])
            .filter((quantity, index, values) => values.indexOf(quantity) === index)
            .sort((first, second) => first - second);


             //Minus Product
        html += `
            <tr class="wepos-cart-row">
                <td class="wepos-col-name">
                    <div class="wepos-cart-item-name">${weposEscapeHtml(item.name)}${overrideBadge}</div>
                </td>
                <td class="wepos-col-price text-muted">₱${item.price.toFixed(2)}</td>
                <td class="wepos-col-qty">
                    <div class="wepos-qty-ctrl">
                        <button onclick="weposUpdateQty('${item.id}', -1)"><i class="fas fa-minus"></i></button>
                        <input type="number" min="1" max="${item.stock}" value="${item.qty}"
                            data-cart-item="${item.id}"
                            onfocus="weposStoreQtyOriginal(this)"
                            onkeydown="weposHandleQtyKey(event, this)"
                            onchange="weposSetQty('${item.id}', this.value)"
                            oninput="weposHandleQtyInput(event, this)"
                            style="width: 3rem; text-align:center; border:1px solid #d1d5db; border-radius:4px; padding:2px;">
                        <button onclick="weposUpdateQty('${item.id}', 1)"><i class="fas fa-plus"></i></button>
                    </div>
                    <div style="margin-top:6px;">
                        <select class="wepos-qty-preset" onchange="weposSetQty('${item.id}', this.value)" style="width:100%; padding:4px; border:1px solid #d1d5db; border-radius:4px;">
                            ${quantityPresets.map(q => `<option value="${q}"${item.qty === q ? ' selected' : ''}>${q} pcs</option>`).join('')}
                        </select>
                    </div>
                </td>
                <td class="wepos-col-total">₱${c.final.toFixed(2)}</td>
                <td class="wepos-col-action">
                    <button class="wepos-btn-icon text-danger" onclick="weposRemoveItem('${item.id}')"><i class="fas fa-times"></i></button>
                </td>
            </tr>`;
    });

    tbody.innerHTML = html;

    // ═══ CORRECT TOTAL CALCULATION (VAT IS INCLUSIVE) ═══
    // Products' prices already include VAT. Senior/PWD statutory discounts
    // may remove the VAT portion (tracked in totalVatExemption). The VAT
    // shown to the cashier should be the collectible VAT after exemptions:
    // collectibleVat = rawVat - totalVatExemption. Do NOT add VAT to the payable total.
    const collectibleVat = Math.max(0, rawVat - totalVatExemption);
    const finalTotal = totalFinalAmount; // use per-item final prices so VAT exemption is applied
    weposSetTotals(rawSubtotal, totalDiscount, dRate, totalVatExemption, collectibleVat, finalTotal);
    document.getElementById('weposPayBtn').disabled = false;
}

function weposSetTotals(sub, disc, dRate, vatExempt, vat, total) {
    const calcSub = document.getElementById('calcSub');
    if (calcSub) calcSub.textContent = '₱' + sub.toFixed(2);
    else console.warn('Element calcSub not found');
    
    const rowDisc = document.getElementById('rowDiscount');
    if (rowDisc) {
        if (disc > 0) {
            rowDisc.style.display = 'flex';
            const discLabel = document.getElementById('calcDiscountLabel');
            const calcDisc = document.getElementById('calcDiscount');
            if (discLabel) discLabel.textContent = (dRate * 100).toFixed(0) + '%';
            if (calcDisc) calcDisc.textContent = '-₱' + disc.toFixed(2);
        } else {
            rowDisc.style.display = 'none';
        }
    }

    const rowVat = document.getElementById('rowVat');
    if (rowVat) {
        rowVat.style.display = 'flex';
        const calcVat = document.getElementById('calcVat');
        // Show VAT amount as informational only (no plus sign)
        if (calcVat) calcVat.textContent = '₱' + vat.toFixed(2);
    }

    const rowVatEx = document.getElementById('rowVatExempt');
    if (rowVatEx) {
        rowVatEx.style.display = 'flex';
        const calcVatEx = document.getElementById('calcVatExempt');
        if (calcVatEx) calcVatEx.textContent = '-₱' + vatExempt.toFixed(2);
    }

    const calcTotal = document.getElementById('calcTotal');
    if (calcTotal) calcTotal.textContent = '₱' + total.toFixed(2);

    const btnTotal = document.getElementById('btnTotalAmount');
    if (btnTotal) btnTotal.textContent = '₱' + total.toFixed(2);
}

function weposSetupKeyboard() {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            document.getElementById('weposSearch')?.focus();
        }
        if (e.key === 'F8') {
            e.preventDefault();
            weposClearCart();
        }
        if (e.key === 'F12' && !document.getElementById('weposPayBtn').disabled) {
            e.preventDefault();
            weposOpenPayModal();
        }
        if (e.key === 'Escape') {
            weposClosePayModal();
            weposCancelOverride();
            weposCancelVerifyId();
            weposCancelVoidAuth();
        }
    });
}

// ═════ PAYMENT MODAL ═════
function weposOpenPayModal() {
    if (Object.keys(weposCart).length === 0) return;

    if (typeof weposRegisterOpened !== 'undefined' && !weposRegisterOpened) {
        if (typeof weposOpenOpeningModal === 'function') weposOpenOpeningModal();
        return;
    }

    // Gate: Senior/PWD requires verification before payment
    const discountSelect = document.getElementById('weposDiscount');
    const selOpt = discountSelect.options[discountSelect.selectedIndex];
    const discountName = (selOpt?.text || '').toLowerCase();
    const needsVerify = discountName.includes('senior') || discountName.includes('pwd');

    if (needsVerify && !weposVerified) {
        // Trigger the verification modal instead of pay modal
        weposOnDiscountChange(discountSelect);
        return;
    }

    const totalText = document.getElementById('btnTotalAmount').textContent.replace('₱', '');
    const total = parseFloat(totalText);
    
    document.getElementById('modalAmountDue').textContent = weposFormatCurrency(total);
    document.getElementById('weposTendered').value = '';
    document.getElementById('weposChangeBox').style.display = 'none';
    const balanceBoxReset = document.getElementById('weposBalanceBox');
    if (balanceBoxReset) balanceBoxReset.style.display = 'none';
    document.getElementById('modalConfirmBtn').disabled = true;

    // Render checkout items with override buttons
    weposRenderCheckoutItems();

    weposGenerateQuickCash(total);
    document.getElementById('weposPayModal').style.display = 'flex';
    setTimeout(() => document.getElementById('weposTendered')?.focus(), 100);
}

function weposClosePayModal(e) {
    if (e && e.target !== e.currentTarget) return;
    document.getElementById('weposPayModal').style.display = 'none';
}

function weposOpenConfirmModal() {
    const amountDue = document.getElementById('modalAmountDue')?.textContent || '₱0.00';
    const tendered = document.getElementById('weposTendered')?.value || '0';
    const changeText = document.getElementById('modalChange')?.textContent || '₱0.00';
    const methodText = currentPayMethod || 'Cash';
    const confirmBtn = document.getElementById('confirmPayBtn');

    if (confirmBtn) {
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = 'Pay Now';
    }

    document.getElementById('confirmAmount').textContent = amountDue;
    document.getElementById('confirmMethod').textContent = methodText;
    document.getElementById('confirmTendered').textContent = weposFormatCurrency(parseFloat(tendered) || parseFloat(amountDue.replace('₱', '')));
    document.getElementById('confirmChange').textContent = changeText;
    document.getElementById('weposPayModal').style.display = 'none';
    const confirmModal = document.getElementById('weposConfirmModal');
    confirmModal.style.zIndex = '10001';
    confirmModal.style.display = 'flex';
}

function weposCloseConfirmModal(e) {
    if (e && e.target !== e.currentTarget) return;
    document.getElementById('weposConfirmModal').style.display = 'none';
    document.getElementById('weposPayModal').style.display = 'flex';
}

// ═════ CHECKOUT ITEMS WITH OVERRIDE ═════
function weposRenderCheckoutItems() {
    const container = document.getElementById('weposCheckoutItems');
    if (!container) return;

    const entries = Object.values(weposCart);
    const discountSelect = document.getElementById('weposDiscount');
    const selOpt = discountSelect.options[discountSelect.selectedIndex];
    const discountRule = selOpt?.dataset?.rule || 'regular';
    const dRate = weposNormalizeRate(selOpt.dataset.rate);
    const isVatExempt = selOpt.dataset.exempt === '1';

    let html = '';
    entries.forEach(item => {
        const c = weposCalcItem(item, dRate, isVatExempt, discountRule);
        const overrideLabel = item.override
            ? `<span style="color:#b91c1c; font-weight:600;"><i class="fas fa-tag"></i> ${(item.overrideRate*100).toFixed(0)}% OFF (by ${weposEscapeHtml(item.overrideApprover)})</span>`
            : '';
        
        html += `<div style="display:flex; justify-content:space-between; align-items:center; padding:8px 12px; border-bottom:1px solid #f0f0f1;">
            <div style="flex:1; min-width:0;">
                <div style="font-weight:500; font-size:13px;">${item.name}</div>
                <div style="font-size:12px; color:#50575e;">${item.qty} × ₱${item.price.toFixed(2)} = ₱${c.final.toFixed(2)} ${overrideLabel}</div>
            </div>
            <button onclick="weposRequestOverride('${item.id}')" 
                    style="padding:4px 10px; font-size:11px; border-radius:3px; border:1px solid ${item.override ? '#dc2626' : '#8c8f94'}; background:${item.override ? '#fef2f2' : '#f6f7f7'}; color:${item.override ? '#dc2626' : '#50575e'}; cursor:pointer; white-space:nowrap;">
                <i class="fas fa-tag"></i> ${item.override ? 'Remove' : 'Override'}
            </button>
        </div>`;
    });

    container.innerHTML = html;
    container.style.display = entries.length > 0 ? 'block' : 'none';
}

// ═════ OVERRIDE FLOW ═════
function weposRequestOverride(cartId) {
    const item = weposCart[cartId];
    if (!item) return;

    // If already overridden, remove it
    if (item.override) {
        mmbConfirm({
            title: 'Remove override discount?',
            message: 'Remove the override discount from "' + weposEscapeHtml(item.name) + '"?',
            okLabel: 'Yes, remove it',
            danger: true
        }).then(function (yes) {
            if (!yes) return;
            item.override = false;
            item.overrideRate = 0;
            item.overrideApprover = null;
            weposRenderCheckoutItems();
            weposUpdateCart();
            // Refresh modal amount
            const totalText = document.getElementById('calcTotal').textContent.replace('₱', '');
            document.getElementById('modalAmountDue').textContent = weposFormatCurrency(parseFloat(totalText));
        });
        return;
    }

    // Open the PIN modal
    pendingOverride = { cartId };
    document.getElementById('overrideItemPreview').innerHTML =
        `<strong>${weposEscapeHtml(item.name)}</strong> — ₱${(item.price * item.qty).toFixed(2)} (${item.qty} × ₱${item.price.toFixed(2)})`;
    document.getElementById('overrideReason').value = '';
    document.getElementById('overrideUsername').value = '';
    document.getElementById('overridePassword').value = '';
    document.getElementById('overridePercent').value = 12;
    document.getElementById('overridePinError').style.display = 'none';
    document.getElementById('overridePinModal').style.display = 'flex';
}

function weposCancelOverride() {
    pendingOverride = null;
    const modal = document.getElementById('overridePinModal');
    if (modal) modal.style.display = 'none';
}

async function weposSubmitOverride() {
    const reason   = document.getElementById('overrideReason').value.trim();
    const username = document.getElementById('overrideUsername').value.trim();
    const password = document.getElementById('overridePassword').value.trim();
    const pct      = parseFloat(document.getElementById('overridePercent').value) || 0;
    const errEl    = document.getElementById('overridePinError');

    errEl.style.display = 'none';

    if (!reason)              { errEl.textContent = 'Please enter a reason.';               errEl.style.display = 'block'; return; }
    if (!username || !password){ errEl.textContent = 'Enter manager username and password.'; errEl.style.display = 'block'; return; }
    if (pct <= 0 || pct > 100){ errEl.textContent = 'Enter a valid discount % (1–100).';    errEl.style.display = 'block'; return; }

    try {
        const res = await fetch('../function/verify_override_pin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });
        const result = await res.json();

        if (!result.success) {
            errEl.textContent = result.error || 'Invalid credentials';
            errEl.style.display = 'block';
            return;
        }

        const item = weposCart[pendingOverride.cartId];
        if (!item) { weposCancelOverride(); return; }

        item.override = true;
        item.overrideRate = pct / 100;
        item.overrideApprover = result.approver_name;

        // Log the override
        await fetch('../function/log_override.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                product_id: item.id,
               generic_name: item.name,
                original_price: item.price * item.qty,
                discounted_price: (item.price * item.qty) * (1 - pct / 100),
                discount_amount: (item.price * item.qty) * (pct / 100),
                discount_percent: pct,
                reason,
                approver_id: result.approver_id,
                approver_name: result.approver_name
            })
        });

        weposCancelOverride();
        weposRenderCheckoutItems();
        weposUpdateCart();
        // Refresh modal amount
        const totalText = document.getElementById('calcTotal').textContent.replace('₱', '');
        document.getElementById('modalAmountDue').textContent = weposFormatCurrency(parseFloat(totalText));

    } catch (err) {
        errEl.textContent = 'Network error. Try again.';
        errEl.style.display = 'block';
    }
}

// ═════ PAYMENT METHODS & CASH ═════
function weposSelectMethod(btn, method) {
    document.querySelectorAll('.wepos-pay-method').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentPayMethod = method;

    if (method === 'Cash') {
        document.querySelector('.wepos-tendered-box').style.display = 'block';
        weposCalcChange();
    } else {
        document.querySelector('.wepos-tendered-box').style.display = 'none';
        document.getElementById('weposChangeBox').style.display = 'none';
        const balanceBox = document.getElementById('weposBalanceBox');
        if (balanceBox) balanceBox.style.display = 'none';
        document.getElementById('modalConfirmBtn').disabled = false;
    }
}

function weposGenerateQuickCash(total) {
    const container = document.getElementById('weposQuickCash');
    const rounded = Math.ceil(total / 50) * 50;
    const amounts = [...new Set([total, rounded, rounded + 50, rounded + 100, 500, 1000])].filter(a => a >= total).slice(0, 4);
    
    container.innerHTML = '';
}

function weposSetCash(amt) {
    document.getElementById('weposTendered').value = amt;
    weposCalcChange();
}

function weposCalcChange() {
    if (currentPayMethod !== 'Cash') return;
    const total = parseFloat(document.getElementById('btnTotalAmount').textContent.replace('₱', ''));
    const tendered = parseFloat(document.getElementById('weposTendered').value) || 0;
    const change = tendered - total;

    const changeBox = document.getElementById('weposChangeBox');
    const balanceBox = document.getElementById('weposBalanceBox');
    const confirmBtn = document.getElementById('modalConfirmBtn');

    if (tendered > 0 && tendered < total) {
        // Partial payment — show the remaining balance due
        const balance = total - tendered;
        if (balanceBox) {
            balanceBox.style.display = 'block';
            document.getElementById('modalBalance').textContent = weposFormatCurrency(balance);
            const more = document.getElementById('modalBalanceMore');
            if (more) more.textContent = weposFormatCurrency(balance);
        }
        changeBox.style.display = 'none';
        confirmBtn.disabled = true;
    } else if (tendered >= total) {
        if (balanceBox) balanceBox.style.display = 'none';
        changeBox.style.display = 'block';
        document.getElementById('modalChange').textContent = weposFormatCurrency(change);
        confirmBtn.disabled = false;
    } else {
        if (balanceBox) balanceBox.style.display = 'none';
        changeBox.style.display = 'none';
        confirmBtn.disabled = true;
    }
}

async function weposSubmitTransaction() {
    const btn = document.getElementById('confirmPayBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = 'Processing...';
    }

    const discountSelect = document.getElementById('weposDiscount');
    const selOpt = discountSelect.options[discountSelect.selectedIndex];
    const discountRule = selOpt?.dataset?.rule || 'regular';
    const selectedRate = weposNormalizeRate(selOpt.dataset.rate);
    const dRate = (discountRule === 'statutory') && weposVerified ? selectedRate : (discountRule === 'regular' ? selectedRate : 0);
    const isVatExempt = selOpt.dataset.exempt === '1';

    const items = Object.entries(weposCart).map(([id, item]) => ({
        id: parseInt(id),
        price: item.price,
        qty: item.qty,
        unitsPerPackage: item.unitsPerPackage,
        eligible_for_discount: !!((item.senior === true) || (item.pwd === true))
    }));

    // Calculate receipt totals before clearing cart
    let rawSubtotal = 0, totalDiscount = 0, totalVatExempt = 0, rawVat = 0, totalFinalAmount = 0;
    Object.values(weposCart).forEach(item => {
        rawSubtotal += item.price * item.qty;
        const c = weposCalcItem(item, dRate, isVatExempt, discountRule);
        totalDiscount   += c.discount;
        totalVatExempt  += c.vatExempt;
        rawVat          += c.vatAmount;
        totalFinalAmount += c.final;
    });
    // Collectible VAT after statutory exemptions
    const collectibleVat = Math.max(0, rawVat - totalVatExempt);
    // VAT is inclusive in item prices — do not add VAT again to the payable total
    const finalTotal = totalFinalAmount; // per-item finals already include VAT exemption and discounts
    const tendered   = parseFloat(document.getElementById('weposTendered')?.value) || finalTotal;
    const change     = currentPayMethod === 'Cash' ? tendered - finalTotal : 0;

    // Close confirmation modal when payment proceeds
    document.getElementById('weposConfirmModal').style.display = 'none';

    try {
        const res = await fetch('../function/process_transaction', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                items: items,
                discount_id: discountSelect.value,
                customer_name: weposCustomerName || 'Walk-in',
                customer_id: weposCustomerId,
                customer_type: weposCustomerType,
                discount_total: totalDiscount,
                total_vat_exemption: totalVatExempt,
                collectible_vat: collectibleVat,
                discount_rule: discountRule
            })
        });
        
        const result = await res.json();
        
        if (result.success) {
            // Close payment modal
            document.getElementById('weposPayModal').style.display = 'none';

            // Build receipt data
            const receiptItems = Object.values(weposCart);
            weposLastReceiptData = {
                refNo:        String(result.transaction_id).padStart(6, '0'),
                cashier:      WEPOS_CASHIER,
                items:        receiptItems,
                dRate,
                isVatExempt,
                discountRule,
                rawSubtotal,
                rawVat,
                totalDiscount,
                totalVatExempt,
                customerName: weposCustomerName || 'Walk-in',
                customerType: weposCustomerType || 'regular',
                customerId: weposCustomerId || '—',
                finalVat: collectibleVat,
                finalTotal,
                discountLabel: selOpt.text,
                method:       currentPayMethod,
                tendered,
                change
            };

            // Show receipt
            weposShowReceipt(weposLastReceiptData);

            // Reset state
            weposCart = {};
            weposVerified = false;
            weposCustomerType = null;
        } else {
            mmbNotify({ type: 'danger', title: 'Payment failed', message: result.error || 'The transaction was not completed.' });
            btn.disabled = false;
            btn.innerHTML = 'Confirm Payment';
        }
    } catch (err) {
        mmbNotify({ type: 'danger', title: 'Network error', message: 'Please check your connection and try again.' });
        btn.disabled = false;
        btn.innerHTML = 'Confirm Payment';
    }
}

// ═════ RECEIPT ═════
function weposShowReceipt(data) {
    const now = new Date();
    document.getElementById('receiptDateTime').textContent =
        now.toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' }) + ' ' +
        now.toLocaleTimeString('en-PH', { hour:'2-digit', minute:'2-digit' });
    document.getElementById('receiptRefNo').textContent = '#' + data.refNo;
    document.getElementById('receiptCashier').textContent = data.cashier || 'Unknown';
    document.getElementById('receiptCustomer').textContent = data.customerName || 'Walk-in';
    document.getElementById('receiptCustomerId').textContent = data.customerId || '—';
    document.getElementById('receiptRule').textContent = data.discountRule === 'statutory' ? 'Statutory Senior/PWD' : 'Regular';

    // Items list — columnar (issue #4 item 7): product, dosage, form,
    // qty and amount each get their own column so nothing sticks together.
    let itemsHtml = `
        <div style="display:flex; border-bottom:1px solid #cbd5e1; padding-bottom:3px; margin-bottom:4px; font-size:9.5px; font-weight:700; letter-spacing:0.6px; color:#94a3b8;">
            <div style="flex:1 1 auto; min-width:0;">ITEM</div>
            <div style="flex:0 0 15%; text-align:center;">DOSAGE</div>
            <div style="flex:0 0 14%; text-align:center;">FORM</div>
            <div style="flex:0 0 6%; text-align:center;">QTY</div>
            <div style="flex:0 0 22%; text-align:right;">AMOUNT</div>
        </div>`;
    data.items.forEach(item => {
        const c = weposCalcItem(item, data.dRate, data.isVatExempt, data.discountRule || 'regular');
        const product = (item.branded || item.generic)
            ? ((item.branded || '') + ' ' + (item.generic || '')).trim()
            : (item.name || '');
        const dose = item.strength || '\u2014';
        const form = item.form || '\u2014';
        itemsHtml += `
        <div style="display:flex; align-items:flex-start; padding:4px 0; border-bottom:1px dashed #e2e8f0;">
            <div style="flex:1 1 auto; min-width:0; padding-right:6px;">
                <div style="font-weight:600; line-height:1.25; overflow-wrap:anywhere;">${weposEscapeHtml(product)}</div>
                <div style="font-size:10px; color:#94a3b8; margin-top:1px;">@ &#8369;${item.price.toFixed(2)}</div>
            </div>
            <div style="flex:0 0 15%; text-align:center; font-size:10.5px; color:#475569; line-height:1.25; padding-top:1px; overflow-wrap:anywhere;">${weposEscapeHtml(dose)}</div>
            <div style="flex:0 0 14%; text-align:center; font-size:10.5px; color:#475569; line-height:1.25; padding-top:1px; overflow-wrap:anywhere;">${weposEscapeHtml(form)}</div>
            <div style="flex:0 0 6%; text-align:center; font-size:10.5px; color:#475569; padding-top:1px;">${item.qty}</div>
            <div style="flex:0 0 22%; text-align:right; font-weight:600; font-size:10.5px; color:#1e293b; padding-top:1px; white-space:nowrap;">&#8369;${c.final.toFixed(2)}</div>
        </div>`;
    });
    document.getElementById('receiptItems').innerHTML = itemsHtml;

    // Totals
    document.getElementById('receiptSubtotal').textContent  = '\u20b1' + data.rawSubtotal.toFixed(2);
    document.getElementById('receiptVat').textContent       = '\u20b1' + (typeof data.rawVat !== 'undefined' ? data.rawVat : data.finalVat).toFixed(2);
    document.getElementById('receiptTotal').textContent     = '\u20b1' + data.finalTotal.toFixed(2);
    document.getElementById('receiptMethod').textContent    = data.method;

    const discRow = document.getElementById('receiptDiscountRow');
    if (data.totalDiscount > 0) {
        discRow.style.display = 'flex';
        document.getElementById('receiptDiscLabel').textContent = data.discountLabel;
        document.getElementById('receiptDiscount').textContent  = '-\u20b1' + data.totalDiscount.toFixed(2);
    } else {
        discRow.style.display = 'none';
    }

    const vatExRow = document.getElementById('receiptVatExRow');
    if (vatExRow) {
        vatExRow.style.display = 'flex';
        document.getElementById('receiptVatEx').textContent = '-\u20b1' + data.totalVatExempt.toFixed(2);
    }

    const tenderedRow = document.getElementById('receiptTenderedRow');
    const changeRow   = document.getElementById('receiptChangeRow');
    if (data.method === 'Cash') {
        tenderedRow.style.display = 'flex';
        changeRow.style.display   = 'flex';
        document.getElementById('receiptTendered').textContent = '\u20b1' + data.tendered.toFixed(2);
        document.getElementById('receiptChange').textContent   = '\u20b1' + data.change.toFixed(2);
    } else {
        tenderedRow.style.display = 'none';
        changeRow.style.display   = 'none';
    }

    document.getElementById('weposReceiptModal').style.display = 'flex';
}

function weposCloseReceipt() {
    document.getElementById('weposReceiptModal').style.display = 'none';
    // Clear cart and reset totals instead of reloading
    weposCart = {};
    weposVerified = false;
    weposCustomerType = null;
    weposCustomerName = null;
    weposCustomerId = null;
    weposUpdateCart();
    // Refresh inventory from server
    weposRefreshInventory();
    // Focus on search for next transaction
    setTimeout(() => document.getElementById('weposSearch')?.focus(), 100);
}

// ═════ REFRESH INVENTORY ═════
function weposRefreshInventory() {
    fetch('../function/workingpos.php?action=getProducts', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(products => {
        if (!products || !Array.isArray(products)) return;
        
        // Update each product card with new inventory
        products.forEach(newProduct => {
            const card = document.querySelector(`.wepos-product-card[data-id="${newProduct.id}"]`);
            if (!card) return;
            
            const stock = parseInt(newProduct.stock || 0);
            const isExpired = newProduct.earliest_expiry_date && new Date(newProduct.earliest_expiry_date) < new Date();
            
            // Update classes
            card.classList.toggle('out-of-stock', stock <= 0);
            card.classList.toggle('expired', isExpired);
            
            // Update stock badge
            const badge = card.querySelector('.wepos-stock-badge');
            if (badge) {
                if (isExpired) {
                    badge.textContent = 'EXPIRED';
                    badge.className = 'wepos-stock-badge expired';
                } else if (stock <= 0) {
                    badge.textContent = 'Out of Stock';
                    badge.className = 'wepos-stock-badge empty';
                } else {
                    badge.textContent = stock + ' in stock';
                    badge.className = 'wepos-stock-badge' + (stock <= 10 ? ' low' : '');
                }
            }
            
            // Update data attributes
            card.setAttribute('data-stock', stock);
            card.setAttribute('data-expired', isExpired ? '1' : '0');
        });
    })
    .catch(err => console.log('Inventory refresh:', err));
}

async function weposPrintReceipt() {
    const content = document.getElementById('weposReceiptPrint').innerHTML;

    // Open the print window synchronously inside the click gesture so popup
    // blockers never interfere, then fill it in.
    const win = window.open('', '_blank', 'width=320,height=600');
    if (!win) {
        mmbNotify({ type: 'warning', title: 'Pop-up blocked', message: 'Allow pop-ups for this site to print receipts.' });
        return;
    }
    win.document.write('<html><head><title>Receipt</title></head><body style="font-family:\'Courier New\',monospace; padding:20px; color:#64748b;">Preparing receipt...</body></html>');
    win.document.close();

    // Paper size comes from Settings → Receipt Printing (store-wide).
    // Falls back to 80mm when the setting cannot be read.
    let paper = '80';
    try {
        const res = await fetch('../function/store_settings.php?key=receipt_paper', { cache: 'no-store' });
        const data = await res.json();
        if (data && (data.value === '58' || data.value === '80')) paper = data.value;
    } catch (e) { /* keep the 80mm default */ }

    // 58mm rolls have a ~48mm printable width; 80mm rolls ~72mm.
    // `zoom` shrinks the receipt proportionally so the on-screen layout
    // (designed for 80mm) stays readable on the narrower paper.
    const css = paper === '58'
        ? `body { font-family: 'Courier New', monospace; font-size: 12px; width: 48mm; margin: 0 auto; padding: 6px 1mm; }
           @page { size: 58mm auto; margin: 0; }
           #rc { zoom: 0.85; }`
        : `body { font-family: 'Courier New', monospace; font-size: 13px; width: 72mm; margin: 0 auto; padding: 8px 2mm; }
           @page { size: 80mm auto; margin: 0; }`;

    win.document.open();
    win.document.write(`
        <html><head><title>Receipt</title>
        <style>${css}
            @media print { body { margin: 0; } }
        </style></head>
        <body><div id="rc">${content}</div></body></html>
    `);
    win.document.close();
    win.focus();
    setTimeout(() => { win.print(); win.close(); }, 300);
}
// ═════ ID-NUMBER → NAME AUTO-FETCH ═════
// As the cashier types (or scans) the Senior/PWD ID number, the POS queries
// the verified-customer registry and fills the name in automatically.
function weposSetupIdLookup() {
    const idInput = document.getElementById('verifyIdNumber');
    if (!idInput) return;
    idInput.addEventListener('input', weposOnIdNumberInput);
    idInput.addEventListener('keydown', weposIdNumberKeydown);
}

// Debounced handler — fires 400ms after the cashier stops typing.
function weposOnIdNumberInput() {
    const statusEl = document.getElementById('verifyIdLookupStatus');
    if (!statusEl) return;

    // Show a subtle "checking" state while the debounce runs
    statusEl.style.display = 'flex';
    statusEl.style.background = '#f8fafc';
    statusEl.style.color = '#64748b';
    statusEl.innerHTML = '<i class="fas fa-spinner fa-spin" style="width:14px;"></i> Checking records...';

    clearTimeout(weposIdLookupTimer);
    weposIdLookupTimer = setTimeout(weposRunIdLookup, 400);
}

// Enter in the ID field submits the verification directly. If the name
// hasn't been auto-filled yet, the lookup runs first so Enter completes the
// whole flow in a single keystroke.
async function weposIdNumberKeydown(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        clearTimeout(weposIdLookupTimer);
        const nameInput = document.getElementById('verifyIdName');
        if (nameInput && !nameInput.value.trim()) {
            await weposRunIdLookup();
        }
        weposSubmitVerifyId();
    }
}

async function weposRunIdLookup() {
    const idInput = document.getElementById('verifyIdNumber');
    const nameInput = document.getElementById('verifyIdName');
    const statusEl = document.getElementById('verifyIdLookupStatus');
    const modal = document.getElementById('verifyIdModal');
    if (!idInput || !nameInput || !statusEl) return;

    const id_number = idInput.value.trim();
    const type = modal.getAttribute('data-type');

    // Too short to be a meaningful ID — reset quietly
    if (id_number.length < 3) {
        statusEl.style.display = 'none';
        statusEl.innerHTML = '';
        return;
    }

    const seq = ++weposIdLookupSeq;
    try {
        const res = await fetch('../function/lookup_customer_id', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type, id_number })
        });
        const result = await res.json();

        // A newer keystroke superseded this response — discard it
        if (seq !== weposIdLookupSeq) return;

        if (result.error) {
            statusEl.style.display = 'none';
            return;
        }

        if (result.found) {
            // Fill the name — but never overwrite something the cashier
            // deliberately typed by hand.
            if (nameInput.value.trim() === '' || nameInput.value.trim() === weposIdNameAutoFilled) {
                nameInput.value = result.name;
                weposIdNameAutoFilled = result.name;
            }
            statusEl.style.display = 'flex';
            statusEl.style.background = '#f0fdf4';
            statusEl.style.color = '#15803d';
            statusEl.innerHTML = '<i class="fas fa-circle-check" style="width:14px;"></i> Verified customer on file — name filled in automatically.';
        } else {
            // Unknown ID: clear any stale auto-fill so the cashier knows a
            // manual name entry + physical inspection is required.
            if (nameInput.value.trim() !== '' && nameInput.value.trim() === weposIdNameAutoFilled) {
                nameInput.value = '';
                weposIdNameAutoFilled = '';
            }
            statusEl.style.display = 'flex';
            statusEl.style.background = '#fef3c7';
            statusEl.style.color = '#92400e';
            statusEl.innerHTML = '<i class="fas fa-circle-info" style="width:14px;"></i> Not on file yet — type the name and complete the ID checklist.';
        }
    } catch (e) {
        if (seq !== weposIdLookupSeq) return;
        statusEl.style.display = 'none';
    }
}
// ═════ SENIOR / PWD DISCOUNT INTERCEPT ═════
function weposOnDiscountChange(selectEl) {
    const selOpt = selectEl.options[selectEl.selectedIndex];
    const discountName = selOpt.text.toLowerCase();

    const isSenior = discountName.includes('senior');
    const isPwd    = discountName.includes('pwd');

    // Reset verification whenever discount changes
    weposVerified = false;
    weposCustomerType = null;
    weposCustomerName = null;
    weposCustomerId = null;

    if (isSenior || isPwd) {
        // Save previous index so we can restore on cancel
        pendingDiscountIndex = selectEl.selectedIndex;

        const type = isSenior ? 'senior' : 'pwd';
        document.getElementById('verifyIdTitle').innerHTML =
            isSenior ? '<i class="fas fa-id-card"></i> Senior Citizen Verification' : '<i class="fas fa-id-card"></i> PWD Verification';
        document.getElementById('verifyIdName').value = '';
        document.getElementById('verifyIdNumber').value = '';
        document.getElementById('verifyIdError').style.display = 'none';
        document.getElementById('verifyIdNewMsg').style.display = 'none';
        document.getElementById('verifyIdChecklist').style.display = 'none';
        // Reset the inspection checklist + accept button
        document.querySelectorAll('.verifyIdCheck').forEach(cb => { cb.checked = false; });
        const acceptBtn = document.getElementById('verifyIdAcceptBtn');
        if (acceptBtn) acceptBtn.disabled = true;
        // Optional external registry link (helper only — never required)
        const extLink = document.getElementById('verifyIdExternalLink');
        if (extLink) {
            extLink.href = isSenior
                ? 'https://www.ncsc.gov.ph/registration-verification'
                : 'https://pwd.doh.gov.ph/tbl_pwd_id_verificationlist.php';
        }
        document.getElementById('verifyIdFootInitial').style.display = 'flex';
        document.getElementById('verifyIdFootManual').style.display = 'none';
        document.getElementById('verifyIdBtn').disabled = false;
        document.getElementById('verifyIdBtn').innerHTML = 'Verify';
        // Reset the live ID lookup state for the new session
        weposIdNameAutoFilled = '';
        clearTimeout(weposIdLookupTimer);
        weposIdLookupSeq++;
        const lookupStatus = document.getElementById('verifyIdLookupStatus');
        if (lookupStatus) { lookupStatus.style.display = 'none'; lookupStatus.innerHTML = ''; }
        document.getElementById('verifyIdModal').setAttribute('data-type', type);
        document.getElementById('verifyIdModal').setAttribute('data-discount-index', selectEl.selectedIndex);
        document.getElementById('verifyIdModal').style.display = 'flex';
        // ID number is the primary input — typing it auto-fills the name
        setTimeout(() => document.getElementById('verifyIdNumber')?.focus(), 100);
    } else {
        weposUpdateCart();
    }
}

function weposCancelVerifyId() {
    // Revert discount dropdown to None (index 0)
    const sel = document.getElementById('weposDiscount');
    if (sel) sel.selectedIndex = 0;
    document.getElementById('verifyIdModal').style.display = 'none';
    weposUpdateCart();
}

async function weposSubmitVerifyId() {
    const name      = document.getElementById('verifyIdName').value.trim();
    const id_number = document.getElementById('verifyIdNumber').value.trim();
    const type      = document.getElementById('verifyIdModal').getAttribute('data-type');
    const errEl     = document.getElementById('verifyIdError');
    const btn       = document.getElementById('verifyIdBtn');

    errEl.style.display = 'none';
    document.getElementById('verifyIdNewMsg').style.display = 'none';

    if (!name)      { errEl.textContent = 'Please enter the customer name.';  errEl.style.display = 'block'; return; }
    if (!id_number) { errEl.textContent = 'Please enter the ID number.'; errEl.style.display = 'block'; return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

    try {
        const res = await fetch('../function/verify_customer_id', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type, name, id_number })
        });
        const result = await res.json();

        if (result.error) {
            errEl.textContent = result.error;
            errEl.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = 'Verify';
            return;
        }
        if (!result.exists) {
            // New customer — show the IN-APP physical ID inspection checklist.
            // No external website redirect: the cashier inspects the physical
            // ID card (the legally sufficient step) and ticks each item.
            document.getElementById('verifyIdNewMsg').style.display = 'block';
            document.getElementById('verifyIdChecklist').style.display = 'block';

            // Switch to manual footer (Confirm button stays disabled until
            // every checklist item is ticked)
            document.getElementById('verifyIdFootInitial').style.display = 'none';
            document.getElementById('verifyIdFootManual').style.display = 'flex';
            document.querySelectorAll('.verifyIdCheck').forEach(cb => {
                cb.addEventListener('change', weposUpdateVerifyChecklist);
            });
        } else {
            // Exists — apply discount and close modal
            weposVerified = true;
            weposCustomerType = type; // Store the customer type ('senior' or 'pwd')
            weposCustomerName = name; // Store the customer name
            weposCustomerId = result.customer_id; // Store the customer ID
            document.getElementById('verifyIdModal').style.display = 'none';
            weposUpdateCart();
            
            btn.disabled = false;
            btn.innerHTML = 'Verify';
        }

    } catch (e) {
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = 'Verify';
    }
}

function weposDeclineVerify() {
    weposCancelVerifyId();
}

// Checklist gate: "Confirm ID Verified" only becomes clickable when the
// cashier has ticked every inspection item.
function weposUpdateVerifyChecklist() {
    const checks = document.querySelectorAll('.verifyIdCheck');
    const acceptBtn = document.getElementById('verifyIdAcceptBtn');
    if (!acceptBtn) return;
    const allTicked = Array.from(checks).every(cb => cb.checked);
    acceptBtn.disabled = !allTicked;
    acceptBtn.innerHTML = allTicked
        ? '<i class="fas fa-check"></i> Confirm ID Verified'
        : '<i class="fas fa-clock"></i> Complete checklist first';
}

// Optional helper only — opens the official registry in a new tab.
// The REQUIRED verification is the in-app physical ID checklist.
function weposOpenVerificationSite() {
    const type = document.getElementById('verifyIdModal').getAttribute('data-type');
    const verifyUrl = type === 'senior'
        ? 'https://www.ncsc.gov.ph/registration-verification'
        : 'https://pwd.doh.gov.ph/tbl_pwd_id_verificationlist.php';
    window.open(verifyUrl, '_blank', 'width=900,height=600');
}

async function weposApproveVerify() {
    const name      = document.getElementById('verifyIdName').value.trim();
    const id_number = document.getElementById('verifyIdNumber').value.trim();
    const type      = document.getElementById('verifyIdModal').getAttribute('data-type');
    const errEl     = document.getElementById('verifyIdError');

    // The cashier must have completed every inspection item
    const checks = document.querySelectorAll('.verifyIdCheck');
    if (!Array.from(checks).every(cb => cb.checked)) {
        errEl.textContent = 'Please complete the physical ID inspection checklist first.';
        errEl.style.display = 'block';
        return;
    }

    try {
        const res = await fetch('../function/save_customer_id', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type, name, id_number })
        });
        const result = await res.json();
        
        if (result.success) {
            weposVerified = true;
            weposCustomerType = type; // Store the customer type ('senior' or 'pwd')
            weposCustomerName = name; // Store the customer name
            weposCustomerId = result.customer_id; // Store the customer ID
            document.getElementById('verifyIdModal').style.display = 'none';
            weposUpdateCart();
        } else {
            errEl.textContent = result.error || 'Failed to save customer.';
            errEl.style.display = 'block';
        }
    } catch (e) {
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
    }
}

// ═════ VOID AUTH MODAL (CART ITEM REMOVE) ═════
function weposCancelVoidAuth() {
    pendingVoid = null;
    pendingClearCart = false;
    document.getElementById('voidAuthModal').style.display = 'none';
}

async function weposSubmitVoidAuth() {
    const pin    = document.getElementById('voidAuthPin').value.trim();
    const errEl  = document.getElementById('voidAuthError');
    const btn    = document.getElementById('voidAuthBtn');

    errEl.style.display = 'none';

    if (!/^\d{7}$/.test(pin)) {
        errEl.textContent = 'Please enter a valid 7-digit Void PIN.';
        errEl.style.display = 'block';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authorizing...';

    try {
        const res = await fetch('../function/verify_void_pin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ void_pin: pin })
        });
        const result = await res.json();

        if (!result.success) {
            errEl.textContent = result.error || 'Invalid Void PIN.';
            errEl.style.display = 'block';
            return;
        }

        // Authorized — remove the item, decrement it, or clear cart
        if (pendingClearCart) {
            // Clear all cart items
            for (let key in weposCart) {
                delete weposCart[key];
            }
            // Reset verification state
            weposVerified = false;
            weposCustomerType = null;
            weposCustomerName = null;
            weposCustomerId = null;
            // Reset UI elements
            const discountSel = document.getElementById('weposDiscount');
            if (discountSel) discountSel.selectedIndex = 0;
            const customerInput = document.getElementById('weposCustomer');
            if (customerInput) customerInput.value = '';
            pendingClearCart = false;
        } else if (pendingVoid && weposCart[pendingVoid]) {
            const action = pendingVoidAction;
            const item = weposCart[pendingVoid];

            if (action === 'delete') {
                delete weposCart[pendingVoid];
            } else if (action === 'decrement') {
                weposCart[pendingVoid].qty = Math.max(weposCart[pendingVoid].qty - 1, 0);
                if (weposCart[pendingVoid].qty <= 0) {
                    delete weposCart[pendingVoid];
                }
            } else if (action === 'set' && Number.isInteger(pendingVoidTargetQty)) {
                if (pendingVoidTargetQty <= 0) {
                    delete weposCart[pendingVoid];
                } else {
                    item.qty = Math.min(pendingVoidTargetQty, item.stock);
                }
            }

            pendingVoid = null;
            pendingVoidAction = null;
            pendingVoidTargetQty = null;
        }
        
        document.getElementById('voidAuthModal').style.display = 'none';
        weposUpdateCart();

    } catch (e) {
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
    } finally {
        // Always reset button so it doesn't get stuck
        btn.disabled = false;
        const buttonText = pendingClearCart ? '<i class="fas fa-trash-alt"></i> Clear Cart' : '<i class="fas fa-trash"></i> Confirm Remove';
        btn.innerHTML = buttonText;
    }
}
