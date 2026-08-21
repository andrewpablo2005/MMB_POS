//show modal
document.addEventListener("DOMContentLoaded", function () {

    window.openStockModal = function(prod) {
        document.getElementById("stockModal").style.display = "block";

        document.getElementById("stock_id").value = prod.id;
        document.getElementById("stock_quantity").value = prod.current_quantity;
        document.getElementById("stock_expiry").value = prod.expiry_date;
    };

    window.closeStockModal = function() {
        document.getElementById("stockModal").style.display = "none";
    };

});

//Alert in the middle of the screen if low
function closeAlert() {
    document.getElementById("lowStockAlert").style.display = "none";
}
