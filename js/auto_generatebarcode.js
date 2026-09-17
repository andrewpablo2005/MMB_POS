function generateBarcode() {
    // Generate a 12-digit numeric product code (like UPC)
    let barcode = '';
    for (let i = 0; i < 12; i++) {
        barcode += Math.floor(Math.random() * 10);
    }

    const input = document.getElementById('barcode');
    input.value = barcode;

    // Notify the live Code 128 preview (programmatic value changes
    // do not fire 'input' on their own)
    input.dispatchEvent(new Event('input', { bubbles: true }));
}
