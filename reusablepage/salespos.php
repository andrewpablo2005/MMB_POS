<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../conn/database.php";
require_once __DIR__ . "/../function/workingpos.php";

$product = new Product($db);
$products = $product->getProducts();
$categories = $product->getCategories();
$discounts = $product->getDiscounts();

$userRole = strtolower($_SESSION['position'] ?? 'staff');
$isManager = in_array($userRole, ['owner', 'admin']);

$cashierName = $_SESSION['username'] ?? 'Unknown';
if (!empty($_SESSION['user_id'])) {
    $stmt = $db->prepare(
        "SELECT COALESCE(NULLIF(CONCAT_WS(' ', ui.firstname, ui.lastname), ''), u.username) AS cashier_name
         FROM users u
         LEFT JOIN users_info ui ON u.id = ui.user_id
         WHERE u.id = ?"
    );
    $stmt->execute([(int)$_SESSION['user_id']]);
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($userRow && !empty($userRow['cashier_name'])) {
        $cashierName = $userRow['cashier_name'];
    }
}
?>

<!-- wePOS Inspired CSS -->
<link rel="stylesheet" href="../css/pos_wepos.css?v=1.3">

<div class="wepos-wrapper" id="weposApp">
    
    <!-- ═══════════════ LEFT PANEL: PRODUCT GRID ═══════════════ -->
    <div class="wepos-left">
        
        <!-- Header & Search -->
        <div class="wepos-header">
            <div class="wepos-search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="weposSearch" placeholder="Scan barcode or search products..." autocomplete="off">
                <kbd>F2</kbd>
            </div>
            <button type="button" class="wepos-btn wepos-btn-outline text-danger" style="border-color: #e74c3c; color: #c0392b;" onclick="openReturnModal()" title="Process Return / Refund (F9)">
                    <i class="fas fa-undo-alt me-1"></i> Process Return <kbd style="font-size: 10px; background: #fee2e2; color: #c0392b; border: none; margin-left: 2px;">F9</kbd>
                </button>
            <button class="wepos-btn wepos-btn-outline" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button class="wepos-btn wepos-btn-outline text-success" onclick="weposOpenClosingModal()" title="Close cashier register">
                <i class="fas fa-cash-register"></i> Close Register
            </button>
        </div>

        <!-- Categories -->
        <div class="wepos-categories">
            <button class="wepos-cat-btn active" onclick="weposFilterCat('All')">All</button>
            <?php foreach ($categories as $cat): ?>
                <button class="wepos-cat-btn" onclick="weposFilterCat('<?= htmlspecialchars($cat['category_name']) ?>')">
                    <?= htmlspecialchars($cat['category_name']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Products -->
        <div class="wepos-products-area">
            <div class="wepos-products-grid" id="weposGrid">
                <?php foreach ($products as $row): ?>
                    <?php 
                        $stock = (int)($row['stock'] ?? 0);
                        $isOut = $stock <= 0;
                        $expiryDate = $row['earliest_expiry_date'] ?? null;
                        $isExpired = ($expiryDate && strtotime($expiryDate) < strtotime('today')) ? true : false;
                        $image = !empty($row['imageproduct']) ? "../img/" . $row['imageproduct'] : "";
                        $salePrice = (float)($row['total_price'] ?? $row['sale_price'] ?? 0);
                        $costPrice = (float)($row['net_price'] ?? $row['purchase_cost'] ?? $salePrice);
                    ?>
                    <div class="wepos-product-card <?= $isOut ? 'out-of-stock' : '' ?><?= $isExpired ? ' expired' : '' ?>"
                         data-id="<?= $row['id'] ?>"
                         data-name="<?= htmlspecialchars($row['branded_name']) ?> <?= htmlspecialchars($row['generic_name']) ?> <?= htmlspecialchars($row['strength'] ?? '') ?> <?= htmlspecialchars($row['measurement_name'] ?? '') ?><?= !empty(trim($row['dosage_form'] ?? '')) ? ' ' . htmlspecialchars(trim($row['dosage_form'])) : '' ?><?= isset($row['strength_per_quantity']) && $row['strength_per_quantity'] > 0 ? ' (' . htmlspecialchars((string)$row['strength_per_quantity']) . ' ' . htmlspecialchars(trim($row['strength_per_quantity_unit'] ?? '')) . ' per unit)' : '' ?>"
                         data-price="<?= $salePrice ?>"
                         data-net="<?= $costPrice ?>"
                         data-barcode="<?= htmlspecialchars($row['barcode'] ?? '') ?>"
                         data-category="<?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?>"
                         data-has-vat="<?= (int)($row['has_vat'] ?? 0) ?>"
                         data-senior="<?= (int)($row['senior_discount'] ?? 0) ?>"
                         data-pwd="<?= (int)($row['pwd_discount'] ?? 0) ?>"
                         data-stock="<?= $stock ?>"
                         data-pcs="<?= (int)($row['pcs'] ?? 1) ?>"
                         data-expired="<?= $isExpired ? '1' : '0' ?>"
                         onclick="weposAddToCart(this)">
                        
                        <div class="wepos-card-img">
                            <?php if ($image): ?>
                                <img src="<?= $image ?>" alt="">
                            <?php else: ?>
                                <i class="fas fa-box" style="font-size: 2rem; color: #cbd5e1;"></i>
                            <?php endif; ?>
                            
                            <!-- Expired Indicator -->
                            <?php if ($isExpired): ?>
                                <span class="wepos-stock-badge expired">EXPIRED</span>
                            <!-- Stock Indicator -->
                            <?php elseif ($isOut): ?>
                                <span class="wepos-stock-badge empty">Out of Stock</span>
                            <?php else: ?>
                                <span class="wepos-stock-badge <?= $stock <= 10 ? 'low' : '' ?>"><?= $stock ?> in stock</span>
                            <?php endif; ?>
                            
                            <!-- Hover Add Overlay -->
                            <div class="wepos-card-add-overlay">
                                <span class="wepos-add-btn-fake"><i class="fas fa-plus"></i> Add to Cart</span>
                            </div>
                        </div>
                        
                        <div class="wepos-card-info">
                            <div class="wepos-card-price">₱<?= number_format($salePrice, 2) ?></div>
                            <div class="wepos-card-name" title="<?= htmlspecialchars($row['branded_name']) ?> <?= htmlspecialchars($row['generic_name']) ?> <?= htmlspecialchars($row['strength'] ?? '') ?> <?= htmlspecialchars($row['measurement_name'] ?? '') ?><?= !empty(trim($row['dosage_form'] ?? '')) ? ' ' . htmlspecialchars(trim($row['dosage_form'])) : '' ?><?= isset($row['strength_per_quantity']) && $row['strength_per_quantity'] > 0 ? ' (' . htmlspecialchars((string)$row['strength_per_quantity']) . ' ' . htmlspecialchars(trim($row['strength_per_quantity_unit'] ?? '')) . ' per unit)' : '' ?>">
                                <?= htmlspecialchars($row['branded_name']) ?> <?= htmlspecialchars($row['generic_name']) ?> <?= htmlspecialchars($row['strength'] ?? '') ?> <?= htmlspecialchars($row['measurement_name'] ?? '') ?><?= !empty(trim($row['dosage_form'] ?? '')) ? ' ' . htmlspecialchars(trim($row['dosage_form'])) : '' ?><?= isset($row['strength_per_quantity']) && $row['strength_per_quantity'] > 0 ? ' (' . htmlspecialchars((string)$row['strength_per_quantity']) . ' ' . htmlspecialchars(trim($row['strength_per_quantity_unit'] ?? '')) . ' per unit)' : '' ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- ═══════════════ RIGHT PANEL: CART & CHECKOUT ═══════════════ -->
    <div class="wepos-right">
        
        <!-- Cart Header -->
        <div class="wepos-cart-header">
            <span style="font-size:13px; font-weight:600; color:#50575e;"><i class="fas fa-shopping-cart me-1"></i> Current Order</span>
            <div class="wepos-cart-actions">
                <button class="wepos-btn wepos-btn-icon text-danger" title="Clear Cart (F8)" onclick="weposClearCart()">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="wepos-cart-items">
            <table class="wepos-cart-table">
                <thead>
                    <tr>
                        <th class="wepos-col-name">Product</th>
                        <th class="wepos-col-price">Price</th>
                        <th class="wepos-col-qty">Qty</th>
                        <th class="wepos-col-total">Total</th>
                        <th class="wepos-col-action"></th>
                    </tr>
                </thead>
                <tbody id="weposCartBody">
                    <tr>
                        <td colspan="5" class="wepos-empty-cart">
                            <i class="fas fa-shopping-cart fa-3x mb-3 text-muted" style="opacity: 0.3;"></i>
                            <p>Cart is empty</p>
                            <small>Scan barcode or click products to add</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Calculations & Checkout -->
        <div class="wepos-checkout-area">
            
            <!-- Discount Selection -->
            <div class="wepos-discount-row">
                <div class="wepos-discount-label"><i class="fas fa-tags text-primary"></i> Apply Discount</div>
                <select id="weposDiscount" class="wepos-select" onchange="weposOnDiscountChange(this)">
                    <?php foreach ($discounts as $d): ?>
                        <?php
                            $discountName = trim((string)($d['discount_name'] ?? ''));
                            if (stripos($discountName, 'bnpc') !== false) {
                                continue; // Do not expose BNPC as a manual discount option
                            }
                            $discountRate = (float)($d['discount_rate'] ?? 0);
                            $isStatutoryDiscount = stripos($discountName, 'senior') !== false || stripos($discountName, 'pwd') !== false;
                            $displayName = $discountName;
                            $displayRate = number_format($discountRate > 1 ? $discountRate / 100 : $discountRate, 2, '.', '');
                            $displayRule = $isStatutoryDiscount ? 'statutory' : 'regular';
                            $displayExempt = $isStatutoryDiscount ? 1 : (int)($d['is_vat_exempt'] ?? 0);
                        ?>
                        <option value="<?= (int)$d['id'] ?>"
                                data-rate="<?= $displayRate ?>"
                                data-rule="<?= $displayRule ?>"
                                data-exempt="<?= $displayExempt ?>"
                                <?= ($discountRate == 0) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Math Breakdown -->
            <div class="wepos-calc-box">
                <div class="wepos-calc-row">
                    <span>Subtotal</span>
                    <span id="calcSub">₱0.00</span>
                </div>
                <div class="wepos-calc-row text-danger" id="rowDiscount" style="display:none;">
                    <span>Discount (<span id="calcDiscountLabel">0%</span>)</span>
                    <span id="calcDiscount">-₱0.00</span>
                </div>
                <div class="wepos-calc-row text-muted" id="rowVat" style="display:none;">
                    <span>VAT (12%)</span>
                    <span id="calcVat">+₱0.00</span>
                </div>
                <div class="wepos-calc-row text-muted" id="rowVatExempt" style="display:none;">
                    <span>VAT Exemption</span>
                    <span id="calcVatExempt">-₱0.00</span>
                </div>

                <div class="wepos-calc-row total-row">
                    <span>Total Due</span>
                    <span class="wepos-grand-total" id="calcTotal">₱0.00</span>
                </div>
            </div>

            <!-- Pay Button -->
            <button class="wepos-pay-btn" id="weposPayBtn" onclick="weposOpenPayModal()" disabled>
                <span>Pay Now</span>
                <span class="wepos-pay-amount" id="btnTotalAmount">₱0.00</span>
            </button>
        </div>

    </div>

</div>

<!-- REGISTER CLOSING MODAL -->
<div class="wepos-modal-overlay" id="registerClosingModal" style="display:none;" onclick="weposCloseClosingModal(event)">
    <div class="wepos-modal" onclick="event.stopPropagation()" style="max-width:520px;">
        <div class="wepos-modal-head" style="background:#ecfdf5; border-bottom:1px solid #bbf7d0;">
            <h5 style="color:#166534;"><i class="fas fa-cash-register me-2"></i>Close Cashier Register</h5>
            <button type="button" onclick="weposCloseClosingModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="wepos-modal-body">
            <p class="text-muted" style="font-size:0.9rem;">Count the cash in your drawer and compare it with the system amount before closing.</p>
            <div style="margin-bottom:12px;">
                <label for="closingBusinessDate" style="font-weight:600; display:block; margin-bottom:4px;">Business date</label>
                <input type="date" id="closingBusinessDate" class="wepos-input-lg" style="font-size:1rem; padding:8px;" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" readonly>
            </div>
            <div id="closingSummary" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px; margin-bottom:12px;">
                <div style="display:flex; justify-content:space-between;"><span>Transactions</span><strong id="closingTransactions">—</strong></div>
                <div style="display:flex; justify-content:space-between;"><span>Sales collected</span><strong id="closingSales">₱0.00</strong></div>
                <div style="display:flex; justify-content:space-between;"><span>Cash refunds</span><strong id="closingRefunds" class="text-danger">-₱0.00</strong></div>
                <div style="display:flex; justify-content:space-between; border-top:1px solid #cbd5e1; margin-top:8px; padding-top:8px; font-size:1.05rem;"><span>System cash</span><strong id="closingSystemCash" class="text-primary">₱0.00</strong></div>
            </div>
            <div style="margin-bottom:12px;">
                <label for="closingCountedCash" style="font-weight:600; display:block; margin-bottom:4px;">Counted cash</label>
                <input type="number" id="closingCountedCash" class="wepos-input-lg" min="0" step="0.01" placeholder="0.00" oninput="weposUpdateClosingVariance()">
            </div>
            <div id="closingVariance" style="display:none; padding:10px; border-radius:5px; margin-bottom:12px; font-weight:600;"></div>
            <div>
                <label for="closingNotes" style="font-weight:600; display:block; margin-bottom:4px;">Notes (optional)</label>
                <textarea id="closingNotes" rows="2" class="form-control" placeholder="Explain any shortage or overage"></textarea>
            </div>
            <div id="closingError" class="text-danger" style="display:none; margin-top:10px;"></div>
        </div>
        <div class="wepos-modal-foot">
            <button type="button" class="wepos-btn wepos-btn-outline" onclick="weposCloseClosingModal()">Cancel</button>
            <button type="button" class="wepos-btn wepos-btn-primary" id="confirmClosingBtn" onclick="weposConfirmRegisterClosing()" disabled>Confirm Closing</button>
        </div>
    </div>
</div>

<!-- REGISTER CLOSING CONFIRMATION MODAL -->
<div class="wepos-modal-overlay" id="registerClosingConfirmModal" style="display:none;" onclick="weposCloseClosingConfirmModal(event)">
    <div class="wepos-modal" onclick="event.stopPropagation()" style="max-width:440px;">
        <div class="wepos-modal-head" style="background:#fff7ed; border-bottom:1px solid #fed7aa;">
            <h5 style="color:#9a3412;"><i class="fas fa-circle-question me-2"></i>Confirm Register Closing</h5>
            <button type="button" onclick="weposCloseClosingConfirmModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="wepos-modal-body">
            <p>Are you sure you want to close the register? This action cannot be repeated for the same business date.</p>
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px;">
                <div style="display:flex; justify-content:space-between;"><span>Business date</span><strong id="closingConfirmDate"></strong></div>
                <div style="display:flex; justify-content:space-between;"><span>System cash</span><strong id="closingConfirmSystemCash"></strong></div>
                <div style="display:flex; justify-content:space-between;"><span>Counted cash</span><strong id="closingConfirmCountedCash"></strong></div>
                <div style="display:flex; justify-content:space-between; border-top:1px solid #cbd5e1; margin-top:8px; padding-top:8px;"><span>Variance</span><strong id="closingConfirmVariance"></strong></div>
            </div>
        </div>
        <div class="wepos-modal-foot">
            <button type="button" class="wepos-btn wepos-btn-outline" onclick="weposCloseClosingConfirmModal()">Go Back</button>
            <button type="button" class="wepos-btn wepos-btn-primary" onclick="weposSubmitRegisterClosing()"><i class="fas fa-check me-1"></i>Close Register</button>
        </div>
    </div>
</div>

<!-- ═══════════════ PAYMENT MODAL ═══════════════ -->
<div class="wepos-modal-overlay" id="weposPayModal" style="display:none;" onclick="weposClosePayModal(event)">
    <div class="wepos-modal" onclick="event.stopPropagation()">
        <div class="wepos-modal-head">
            <h5>Complete Payment</h5>
            <button onclick="weposClosePayModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="wepos-modal-body">
            
            <div class="wepos-modal-totals">
                <div class="wepos-modal-amt">Amount Due: <strong class="text-primary" id="modalAmountDue">₱0.00</strong></div>
            </div>

            <!-- Checkout Items with Override -->
            <div id="weposCheckoutItems" style="max-height: 200px; overflow-y: auto; margin-bottom: 15px; border: 1px solid #e0e0e0; border-radius: 4px; display: none;"></div>



            <div class="wepos-tendered-box">
                <label>Amount Tendered (₱)</label>
                <input type="number" id="weposTendered" class="wepos-input-lg" placeholder="" oninput="weposCalcChange()" autofocus>
                
                <div class="wepos-quick-cash" id="weposQuickCash"></div>
            </div>

            <div class="wepos-change-box" id="weposChangeBox" style="display:none;">
                <span>Change:</span>
                <strong id="modalChange">₱0.00</strong>
            </div>

        </div>
        <div class="wepos-modal-foot">
            <button class="wepos-btn wepos-btn-outline" onclick="weposClosePayModal()">Cancel</button>
            <button class="wepos-btn wepos-btn-primary" id="modalConfirmBtn" onclick="weposOpenConfirmModal()" disabled>Confirm Payment</button>
        </div>
    </div>
</div>

<!-- ═══════════════ PAYMENT CONFIRMATION MODAL ═══════════════ -->
<div class="wepos-modal-overlay" id="weposConfirmModal" style="display:none;" onclick="weposCloseConfirmModal(event)">
    <div class="wepos-modal" onclick="event.stopPropagation()">
        <div class="wepos-modal-head">
            <h5>Confirm Payment</h5>
            <button onclick="weposCloseConfirmModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="wepos-modal-body">
            <div style="margin-bottom: 1rem; font-size: 1rem;">
                Please verify the payment details before proceeding.
            </div>
            <div style="margin-bottom: 0.75rem; display:flex; justify-content:space-between;">
                <span>Total Due</span>
                <strong id="confirmAmount">₱0.00</strong>
            </div>
            <div style="margin-bottom: 0.75rem; display:flex; justify-content:space-between;">
                <span>Payment Method</span>
                <strong id="confirmMethod">—</strong>
            </div>
            <div style="margin-bottom: 0.75rem; display:flex; justify-content:space-between;">
                <span>Amount Tendered</span>
                <strong id="confirmTendered">₱0.00</strong>
            </div>
            <div style="margin-bottom: 0.75rem; display:flex; justify-content:space-between;">
                <span>Change</span>
                <strong id="confirmChange">₱0.00</strong>
            </div>
        </div>
        <div class="wepos-modal-foot">
            <button class="wepos-btn wepos-btn-outline" onclick="weposCloseConfirmModal()">Cancel</button>
            <button class="wepos-btn wepos-btn-primary" id="confirmPayBtn" onclick="weposSubmitTransaction()">Pay Now</button>
        </div>
    </div>
</div>

<!-- ═══════════════ OVERRIDE PIN MODAL ═══════════════ -->
<div class="wepos-modal-overlay" id="overridePinModal" style="display:none;" onclick="event.stopPropagation()">
    <div class="wepos-modal" onclick="event.stopPropagation()">
        <div class="wepos-modal-head" style="background: #fff3cd;">
            <h5 style="color: #856404;"><i class="fas fa-shield-alt"></i> Manager Approval Required</h5>
            <button onclick="weposCancelOverride()"><i class="fas fa-times"></i></button>
        </div>
        <div class="wepos-modal-body">
            <p class="text-muted" style="font-size:0.9rem; margin-bottom:1rem;">
                Discount override requires manager authorization.<br>
                A log of this action will be recorded.
            </p>
            <div id="overrideItemPreview" style="background:#f8f9fa; border:1px solid #dcdcde; padding:10px 14px; border-radius:4px; font-size:13px; margin-bottom:14px;"></div>
            <div style="margin-bottom: 0.75rem;">
                <label style="font-size:0.85rem; font-weight:600; margin-bottom:0.25rem; display:block;">Reason for Discount</label>
                <textarea id="overrideReason" rows="2" placeholder="e.g. Regular customer, damaged packaging..." style="width:100%; border-radius:4px; border:1px solid #8c8f94; padding:0.5rem; font-size:0.9rem; resize:none;"></textarea>
            </div>
            <div style="margin-bottom:0.75rem;">
                <label style="font-size:0.85rem; font-weight:600; margin-bottom:0.25rem; display:block;">Override Discount %</label>
                <input type="number" id="overridePercent" min="1" max="100" value="12" step="1" style="width:100%; border-radius:4px; border:1px solid #8c8f94; padding:0.5rem; font-size:1rem;">
            </div>
            <hr>
            <p style="font-size:0.85rem; font-weight:600; margin-bottom:0.5rem;">Manager Login</p>
            <input type="text" id="overrideUsername" placeholder="Manager username" autocomplete="off" style="width:100%; border-radius:4px; border:1px solid #8c8f94; padding:0.5rem; font-size:0.9rem; margin-bottom:0.5rem;">
            <input type="password" id="overridePassword" placeholder="Manager password" style="width:100%; border-radius:4px; border:1px solid #8c8f94; padding:0.5rem; font-size:0.9rem; margin-bottom:1rem;">
            <div id="overridePinError" class="text-danger" style="font-size:0.85rem; display:none; margin-bottom:0.5rem;"></div>
        </div>
        <div class="wepos-modal-foot">
            <button class="wepos-btn wepos-btn-outline" onclick="weposCancelOverride()">Cancel</button>
            <button class="wepos-btn wepos-btn-primary" onclick="weposSubmitOverride()"><i class="fas fa-unlock"></i> Authorize Override</button>
        </div>
    </div>
</div>





<!-- ═══════════════ VOID AUTH MODAL ═══════════════ -->
<div class="wepos-modal-overlay" id="voidAuthModal" style="display:none;" onclick="event.stopPropagation()">
    <div class="wepos-modal" onclick="event.stopPropagation()">
        <div class="wepos-modal-head" style="background: #fee2e2; border-bottom: 1px solid #fecaca;">
            <h5 style="color: #991b1b;"><i class="fas fa-trash-alt"></i> Manager Void Required</h5>
            <button onclick="weposCancelVoidAuth()"><i class="fas fa-times"></i></button>
        </div>
        <div class="wepos-modal-body">
            <div id="voidItemPreview" style="background:#f8f9fa; border:1px solid #dcdcde; padding:10px 14px; border-radius:4px; font-size:13px; margin-bottom:14px;"></div>
            <p class="text-muted" style="font-size:0.9rem; margin-bottom:1rem;">
                Please enter the 7-digit Manager Void PIN to remove this item from the cart.
            </p>
            <div style="margin-bottom:0.75rem;">
                <input type="password" id="voidAuthPin" placeholder="7-Digit Void PIN" style="width:100%; border-radius:4px; border:1px solid #8c8f94; padding:0.5rem; font-size:1.2rem; text-align:center; letter-spacing:4px;" autocomplete="off" maxlength="7">
            </div>
            <div id="voidAuthError" class="text-danger" style="font-size:0.85rem; display:none; margin-bottom:0.5rem; text-align:center;"></div>
        </div>
        <div class="wepos-modal-foot">
            <button class="wepos-btn wepos-btn-outline" onclick="weposCancelVoidAuth()">Cancel</button>
            <button class="wepos-btn wepos-btn-primary" style="background-color: #dc2626; border-color: #dc2626;" id="voidAuthBtn" onclick="weposSubmitVoidAuth()"><i class="fas fa-trash"></i> Confirm Remove</button>
        </div>
    </div>
</div>

<!-- ═══════════════ RECEIPT MODAL ═══════════════ -->
<div class="wepos-modal-overlay" id="weposReceiptModal" style="display:none;" onclick="event.stopPropagation()">
    <div class="wepos-modal" onclick="event.stopPropagation()" style="max-width:400px; border-radius:8px;">
        <div class="wepos-modal-head" style="background:#f0fdf4; border-bottom:1px solid #bbf7d0;">
            <h5 style="color:#15803d;"><i class="fas fa-receipt"></i> Payment Successful</h5>
            <button onclick="weposCloseReceipt()"><i class="fas fa-times"></i></button>
        </div>
        <div class="wepos-modal-body" id="weposReceiptBody" style="padding:0;">

            <!-- Printable Receipt -->
            <div id="weposReceiptPrint" style="padding:20px; font-family:'Courier New',monospace; font-size:13px;">
                <div style="text-align:center; margin-bottom:12px;">
                    <div style="font-size:16px; font-weight:700;">MMB'SS DRUGSTORE</div>
                    <div style="font-size:11px; color:#64748b;">8VFW+7CP, Provincial Road, Jaen, Nueva Ecija</div>
                    <div style="font-size:11px; color:#64748b;">0965-845-2485</div>
                    <div style="font-size:11px; color:#64748b;">Official Receipt</div>
                    <div style="font-size:11px; color:#64748b;" id="receiptDateTime"></div>

                </div>
                <div style="border-top:1px dashed #cbd5e1; border-bottom:1px dashed #cbd5e1; padding:6px 0; margin-bottom:10px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span style="color:#64748b;">Ref #:</span> <strong id="receiptRefNo"></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:#64748b;">Cashier:</span> <strong id="receiptCashier"></strong>
                    </div>
                </div>
                <div id="receiptItems" style="margin-bottom:10px;"></div>
                <div style="border-top:1px dashed #cbd5e1; padding-top:8px; font-size:12px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span style="color:#64748b;">Customer</span><strong id="receiptCustomer">Walk-in</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span style="color:#64748b;">ID</span><strong id="receiptCustomerId">—</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <span style="color:#64748b;">Rule</span><strong id="receiptRule">Regular</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:3px;">
                        <span>Subtotal</span><span id="receiptSubtotal"></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:3px; color:#dc2626;" id="receiptDiscountRow">
                        <span>Discount (<span id="receiptDiscLabel"></span>)</span><span id="receiptDiscount"></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:3px; color:#64748b;" id="receiptVatExRow">
                        <span>VAT Exempt</span><span id="receiptVatEx"></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:3px; color:#64748b;">
                        <span>VAT (12%)</span><span id="receiptVat"></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:15px; font-weight:700; border-top:1px solid #e2e8f0; padding-top:6px; margin-top:4px;">
                        <span>TOTAL</span><span id="receiptTotal" style="color:#15803d;"></span>
                    </div>
                </div>
                <div style="border-top:1px dashed #cbd5e1; margin-top:10px; padding-top:8px; font-size:12px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:3px;">
                        <span>Payment Method</span><span id="receiptMethod"></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:3px;" id="receiptTenderedRow">
                        <span>Cash Tendered</span><span id="receiptTendered"></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700;" id="receiptChangeRow">
                        <span>Change</span><span id="receiptChange"></span>
                    </div>
                </div>
                <div style="text-align:center; margin-top:14px; font-size:11px; color:#94a3b8;">
                    Thank you for your purchase!<br>
                    Please come again.
                </div>
            </div>

        </div>
        <div class="wepos-modal-foot" style="gap:10px;">
            <button class="wepos-btn wepos-btn-outline" onclick="weposPrintReceipt()">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <button class="wepos-btn wepos-btn-primary" onclick="weposCloseReceipt()">
                Done
            </button>
        </div>
    </div>
</div>

<script>
    const WEPOS_ROLE = '<?= $userRole ?>';
    const WEPOS_IS_MANAGER = <?= $isManager ? 'true' : 'false' ?>;
    const WEPOS_CASHIER = <?= json_encode($cashierName) ?>;

    let weposClosingSystemCash = 0;

    function weposFormatClosingCurrency(value) {
        return '₱' + Number(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    async function weposOpenClosingModal() {
        const modal = document.getElementById('registerClosingModal');
        const error = document.getElementById('closingError');
        const date = document.getElementById('closingBusinessDate').value;
        error.style.display = 'none';
        document.getElementById('confirmClosingBtn').disabled = true;
        modal.style.display = 'flex';

        try {
            const response = await fetch('../function/close_register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'preview', business_date: date })
            });
            const result = await response.json();
            if (!result.success) throw new Error(result.error);

            weposClosingSystemCash = Number(result.system_cash || 0);
            document.getElementById('closingTransactions').textContent = result.transaction_count;
            document.getElementById('closingSales').textContent = weposFormatClosingCurrency(result.sales_total);
            document.getElementById('closingRefunds').textContent = '-' + weposFormatClosingCurrency(result.refund_total);
            document.getElementById('closingSystemCash').textContent = weposFormatClosingCurrency(weposClosingSystemCash);
            document.getElementById('confirmClosingBtn').disabled = result.already_closed || weposClosingSystemCash <= 0;
            if (result.already_closed) {
                error.textContent = 'This register is already closed for the selected date.';
                error.style.display = 'block';
            }
            weposUpdateClosingVariance();
        } catch (requestError) {
            error.textContent = requestError.message || 'Unable to load register totals.';
            error.style.display = 'block';
        }
    }

    function weposCloseClosingModal(event) {
        if (!event || event.target === event.currentTarget) {
            document.getElementById('registerClosingModal').style.display = 'none';
        }
    }

    function weposUpdateClosingVariance() {
        const counted = Number(document.getElementById('closingCountedCash').value);
        const varianceBox = document.getElementById('closingVariance');
        const confirmButton = document.getElementById('confirmClosingBtn');
        if (!Number.isFinite(counted) || counted < 0) {
            varianceBox.style.display = 'none';
            confirmButton.disabled = true;
            return;
        }

        const variance = Math.round((counted - weposClosingSystemCash) * 100) / 100;
        varianceBox.textContent = variance === 0
            ? '✓ Counted cash matches the system amount.'
            : (variance > 0 ? 'Overage: ' : 'Shortage: ') + weposFormatClosingCurrency(Math.abs(variance));
        varianceBox.style.display = 'block';
        varianceBox.style.background = variance === 0 ? '#dcfce7' : '#fef3c7';
        varianceBox.style.color = variance === 0 ? '#166534' : '#92400e';
        confirmButton.disabled = false;
    }

    function weposConfirmRegisterClosing() {
        const counted = Number(document.getElementById('closingCountedCash').value);
        if (!Number.isFinite(counted) || counted < 0) {
            return;
        }

        const variance = Math.round((counted - weposClosingSystemCash) * 100) / 100;
        document.getElementById('closingConfirmDate').textContent = document.getElementById('closingBusinessDate').value;
        document.getElementById('closingConfirmSystemCash').textContent = weposFormatClosingCurrency(weposClosingSystemCash);
        document.getElementById('closingConfirmCountedCash').textContent = weposFormatClosingCurrency(counted);
        document.getElementById('closingConfirmVariance').textContent = (variance < 0 ? '-' : '') + weposFormatClosingCurrency(Math.abs(variance));
        document.getElementById('registerClosingConfirmModal').style.display = 'flex';
    }

    function weposCloseClosingConfirmModal(event) {
        if (!event || event.target === event.currentTarget) {
            document.getElementById('registerClosingConfirmModal').style.display = 'none';
        }
    }

    async function weposSubmitRegisterClosing() {
        const button = document.getElementById('confirmClosingBtn');
        const error = document.getElementById('closingError');
        button.disabled = true;
        error.style.display = 'none';
        document.getElementById('registerClosingConfirmModal').style.display = 'none';
        try {
            const response = await fetch('../function/close_register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'close',
                    business_date: document.getElementById('closingBusinessDate').value,
                    counted_cash: document.getElementById('closingCountedCash').value,
                    notes: document.getElementById('closingNotes').value
                })
            });
            const result = await response.json();
            if (!result.success) throw new Error(result.error);
            alert('Register closed successfully. Variance: ' + weposFormatClosingCurrency(result.variance));
            window.location.href = '../login_logout_page/logout.php';
        } catch (requestError) {
            error.textContent = requestError.message || 'Unable to close register.';
            error.style.display = 'block';
            button.disabled = false;
        }
    }
</script>

<!-- ═══════════════ SENIOR/PWD VERIFY MODAL ═══════════════ -->
<div class="wepos-modal-overlay" id="verifyIdModal" style="display:none;" onclick="event.stopPropagation()">
    <div class="wepos-modal" onclick="event.stopPropagation()">
        <div class="wepos-modal-head" style="background: #eef2ff; border-bottom: 1px solid #c7d2fe;">
            <h5 style="color: #3730a3;" id="verifyIdTitle"><i class="fas fa-id-card"></i> ID Verification</h5>
            <button onclick="weposCancelVerifyId()"><i class="fas fa-times"></i></button>
        </div>
        <div class="wepos-modal-body">
            <p class="text-muted" style="font-size:0.9rem; margin-bottom:1rem;">
                Please enter the customer's details to verify their ID.
            </p>
            
            <div style="margin-bottom: 0.75rem;">
                <label style="font-size:0.85rem; font-weight:600; margin-bottom:0.25rem; display:block;">Full Name</label>
                <input type="text" id="verifyIdName" placeholder="Juan Dela Cruz" style="width:100%; border-radius:4px; border:1px solid #8c8f94; padding:0.5rem; font-size:1rem;" autocomplete="off">
            </div>
            
            <div style="margin-bottom:0.75rem;">
                <label style="font-size:0.85rem; font-weight:600; margin-bottom:0.25rem; display:block;">ID Number</label>
                <input type="text" id="verifyIdNumber" placeholder="XXXX-XXXX-XXXX" style="width:100%; border-radius:4px; border:1px solid #8c8f94; padding:0.5rem; font-size:1rem;" autocomplete="off">
            </div>
            
            <div id="verifyIdError" class="text-danger" style="font-size:0.85rem; display:none; margin-bottom:0.5rem;"></div>
            
            <div id="verifyIdNewMsg" class="text-warning" style="font-size:0.85rem; display:none; margin-bottom:0.5rem; color: #d97706; background: #fef3c7; padding: 8px; border-radius: 4px; border-left: 3px solid #f59e0b;">
                <i class="fas fa-exclamation-triangle"></i> This ID has not been verified yet for a discount. Please check the website.
            </div>
        </div>
        <div class="wepos-modal-foot" id="verifyIdFootInitial">
            <button class="wepos-btn wepos-btn-outline" onclick="weposCancelVerifyId()">Cancel</button>
            <button class="wepos-btn wepos-btn-primary" id="verifyIdBtn" onclick="weposSubmitVerifyId()">Verify</button>
        </div>
        <div class="wepos-modal-foot" id="verifyIdFootManual" style="display:none; justify-content: space-between;">
            <button class="wepos-btn wepos-btn-outline" style="color: #dc2626; border-color: #dc2626;" onclick="weposDeclineVerify()">Decline</button>
            <div style="display:flex; gap: 8px;">
                <button class="wepos-btn wepos-btn-primary" style="background-color: #3b82f6; border-color: #3b82f6;" onclick="weposOpenVerificationSite()"><i class="fas fa-external-link-alt"></i> Check Website</button>
                <button class="wepos-btn wepos-btn-primary" style="background-color: #16a34a; border-color: #16a34a;" onclick="weposApproveVerify()"><i class="fas fa-check"></i> Accept</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/returnmodal.php'; ?>
<script src="../js/pos_wepos.js?v=1.4"></script>
