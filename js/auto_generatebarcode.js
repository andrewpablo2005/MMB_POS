function generateBarcode() {
    // Generate a 12-digit numeric barcode (like UPC)
    let barcode = '';
    for (let i = 0; i < 12; i++) {
        barcode += Math.floor(Math.random() * 10);
    }

    document.getElementById('barcode').value = barcode;
}
