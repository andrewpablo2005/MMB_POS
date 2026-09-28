async function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const element = document.getElementById("reportContent");

    const canvas = await html2canvas(element, { scale: 2 });
    const imgData = canvas.toDataURL("image/png");

    const pdf = new jsPDF('p', 'mm', 'a4');

    const imgWidth = 210;
    const pageHeight = 297;
    const imgHeight = canvas.height * imgWidth / canvas.width;

    let heightLeft = imgHeight;
    let position = 0;

    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
    heightLeft -= pageHeight;

    while (heightLeft > 0) {
        position = heightLeft - imgHeight;
        pdf.addPage();
        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
    }

    pdf.save("MMB_Drugstore_Report.pdf");
}

async function openSalesReceipt(transactionId) {
    const modalElement = document.getElementById('salesReceiptModal');
    if (!modalElement || !window.bootstrap) return;

    const loading = document.getElementById('salesReceiptLoading');
    const error = document.getElementById('salesReceiptError');
    const content = document.getElementById('salesReceiptContent');
    loading.classList.remove('d-none');
    error.classList.add('d-none');
    content.classList.add('d-none');
    bootstrap.Modal.getOrCreateInstance(modalElement).show();

    try {
        const response = await fetch(`../function/get_transaction_details.php?id=${encodeURIComponent(transactionId)}`);
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.error || 'Could not load this receipt.');
        }

        const transaction = data.transaction;
        const setText = (id, value) => {
            document.getElementById(id).textContent = value;
        };
        const money = (value) => `₱${Number(value || 0).toFixed(2)}`;
        setText('receiptTransactionId', `#${String(transaction.id).padStart(6, '0')}`);
        setText('receiptTransactionDate', transaction.created_at || 'N/A');
        setText('receiptCustomer', transaction.customer_name || 'Walk-in');
        setText('receiptCashier', transaction.cashier_name || 'N/A');

        const itemsBody = document.getElementById('salesReceiptItems');
        itemsBody.replaceChildren();
        data.items.forEach((item) => {
            const row = document.createElement('tr');
            [
                item.product_name || `Product #${item.product_id}`,
                String(Number(item.quantity || 0)),
                money(item.price),
                money(item.subtotal),
            ].forEach((value, index) => {
                const cell = document.createElement('td');
                cell.textContent = value;
                if (index > 0) cell.className = 'text-end';
                row.appendChild(cell);
            });
            itemsBody.appendChild(row);
        });

        setText('receiptGross', money(transaction.gross_subtotal));
        setText('receiptOverrideDiscount', `-${money(transaction.override_discount_total)}`);
        setText('receiptDiscount', `-${money(transaction.discount_total)}`);
        setText('receiptVatExempt', `-${money(transaction.total_vat_exemption)}`);
        setText('receiptRefund', `-${money(transaction.refund_total)}`);
        setText('receiptTotal', money(transaction.total_amount));
        loading.classList.add('d-none');
        content.classList.remove('d-none');
    } catch (loadError) {
        loading.classList.add('d-none');
        error.textContent = loadError.message || 'Could not load this receipt.';
        error.classList.remove('d-none');
    }
}