let cart = {};
let barcodeBuffer = '';
let barcodeTimer = null;

// Audio context for beep
const beepSound = document.getElementById('scannerBeep');

document.addEventListener('DOMContentLoaded', () => {

    // --- BARCODE SCANNER INTERCEPTION ---
    // Scanners act like keyboards typing very fast, usually ending with 'Enter'
    document.addEventListener('keypress', function (e) {
        // If we are typing in the search box, let it be handled by input event
        if (document.activeElement.id === 'barcodeScanner' && e.key !== 'Enter') {
            return;
        }

        if (e.key === 'Enter') {
            if (barcodeBuffer.length > 2) {
                // We have a scanned barcode!
                handleScannedBarcode(barcodeBuffer);
            }
            barcodeBuffer = ''; // Reset

            // If focused on search box, also trigger search
            if (document.activeElement.id === 'barcodeScanner') {
                const val = document.getElementById('barcodeScanner').value.trim();
                if (val) handleScannedBarcode(val);
                document.getElementById('barcodeScanner').value = '';
            }
            e.preventDefault();
            return;
        }

        // Add character to buffer
        barcodeBuffer += e.key;

        // Clear buffer if typing is too slow (manual typing instead of scanner)
        clearTimeout(barcodeTimer);
        barcodeTimer = setTimeout(() => {
            barcodeBuffer = '';
        }, 100); // Scanners usually input characters < 50ms apart
    });

    // --- MANUAL SEARCH ---
    document.getElementById('barcodeScanner').addEventListener('input', function (e) {
        const query = e.target.value.toLowerCase();
        const products = document.querySelectorAll('.product-card-col');

        products.forEach(p => {
            const name = p.querySelector('.prod-name').value.toLowerCase();
            const barcode = p.querySelector('.prod-barcode').value.toLowerCase();

            if (name.includes(query) || barcode.includes(query)) {
                p.style.display = 'block';
            } else {
                p.style.display = 'none';
            }
        });
    });
});

// --- CORE LOGIC ---

function handleScannedBarcode(scannedCode) {
    const products = document.querySelectorAll('.product-card');
    let found = false;

    products.forEach(card => {
        const barcode = card.querySelector('.prod-barcode').value;
        if (barcode === scannedCode) {
            // Simulate click
            card.click();
            found = true;
        }
    });

    if (found) {
        // Play beep
        if (beepSound) {
            beepSound.currentTime = 0;
            beepSound.play().catch(e => console.log('Audio play prevented by browser'));
        }
    } else {
        alert('Barcode not found in database: ' + scannedCode);
    }
}

function filterCategory(category) {
    // Update button styles
    const btns = document.querySelectorAll('.catalog-categories .btn');
    btns.forEach(b => {
        b.classList.remove('btn-success');
        b.classList.add('btn-outline-secondary', 'bg-white');
        if (b.innerText === category || (category === 'All' && b.innerText === 'All Items')) {
            b.classList.add('btn-success');
            b.classList.remove('btn-outline-secondary', 'bg-white');
        }
    });

    // Filter products
    const products = document.querySelectorAll('.product-card-col');
    products.forEach(p => {
        if (category === 'All' || p.dataset.category === category) {
            p.style.display = 'block';
        } else {
            p.style.display = 'none';
        }
    });
}

function addToCart(id, name, price, barcode) {
    if (cart[id]) {
        cart[id].qty++;
    } else {
        cart[id] = { name, price, qty: 1, barcode };
    }
    updateCartUI();
}

function updateQty(id, delta) {
    if (cart[id]) {
        cart[id].qty += delta;
        if (cart[id].qty <= 0) {
            delete cart[id];
        }
        updateCartUI();
    }
}

function removeItem(id) {
    delete cart[id];
    updateCartUI();
}

function clearCart() {
    if (Object.keys(cart).length === 0) return;
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = {};
        updateCartUI();
    }
}

function updateCartUI() {
    const container = document.getElementById('cartItemsContainer');
    let subtotal = 0;

    if (Object.keys(cart).length === 0) {
        container.innerHTML = `
            <div class="empty-cart-state text-center text-muted mt-5">
                <i class="fas fa-shopping-cart fa-3x mb-3 opacity-25"></i>
                <h6>Cart is empty</h6>
                <p class="small">Scan a barcode or click an item to start.</p>
            </div>
        `;
        document.getElementById('cartSubtotal').innerText = '₱0.00';
        document.getElementById('cartTotal').innerText = '₱0.00';
        document.getElementById('btnTotal').innerText = '0.00';
        return;
    }

    let html = '';
    for (const [id, item] of Object.entries(cart)) {
        const itemTotal = item.price * item.qty;
        subtotal += itemTotal;

        html += `
            <div class="cart-item shadow-sm">
                <div class="flex-grow-1">
                    <h6 class="mb-0 text-truncate" style="max-width: 150px; font-size: 0.9rem;">${item.name}</h6>
                    <small class="text-muted">₱${item.price.toFixed(2)}</small>
                </div>
                
                <div class="qty-control mx-2">
                    <button class="qty-btn" onclick="updateQty(${id}, -1)"><i class="fas fa-minus small"></i></button>
                    <div class="qty-display">${item.qty}</div>
                    <button class="qty-btn" onclick="updateQty(${id}, 1)"><i class="fas fa-plus small"></i></button>
                </div>
                
                <div class="fw-bold me-2" style="min-width: 60px; text-align: right;">
                    ₱${itemTotal.toFixed(2)}
                </div>
                
                <i class="fas fa-times remove-btn" onclick="removeItem(${id})" title="Remove"></i>
            </div>
        `;
    }

    container.innerHTML = html;

    // Smooth scroll to bottom to see new items
    container.scrollTop = container.scrollHeight;

    // Update Totals
    document.getElementById('cartSubtotal').innerText = '₱' + subtotal.toFixed(2);
    // Assuming no global discount logic implemented yet, total = subtotal
    document.getElementById('cartTotal').innerText = '₱' + subtotal.toFixed(2);
    document.getElementById('btnTotal').innerText = subtotal.toFixed(2);
}

function processPayment() {
    if (Object.keys(cart).length === 0) {
        alert('Cart is empty!');
        return;
    }
    // This is where you would send the cart to the backend via AJAX to save into `transactions` and `transaction_items`
    alert('Payment Processing... (Backend Integration Pending)');
}
