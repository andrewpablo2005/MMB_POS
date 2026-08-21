let cart = [];

document.querySelectorAll(".product").forEach(p => {
    p.addEventListener("click", () => {
        let name = p.dataset.name;
        let price = parseFloat(p.dataset.price);

        cart.push({name, price, qty:1});
        renderCart();
    });
});

function renderCart() {
    let container = document.getElementById("cartItems");
    container.innerHTML = "";

    let total = 0;

    cart.forEach((item, i) => {
        total += item.price * item.qty;

        container.innerHTML += `
        <div class="cart-item">
            <div>${item.name}<br>₱${item.price}</div>
            <div class="qty">
                <button onclick="updateQty(${i}, -1)">-</button>
                ${item.qty}
                <button onclick="updateQty(${i}, 1)">+</button>
            </div>
        </div>`;
    });

    updateTotal(total);
}

function updateQty(i, change) {
    cart[i].qty += change;
    if (cart[i].qty <= 0) cart.splice(i,1);
    renderCart();
}

function updateTotal(amount) {
    let el = document.getElementById("totalDisplay");
    el.innerText = "TOTAL: ₱" + amount.toFixed(2);

    el.classList.add("flash");
    setTimeout(()=>el.classList.remove("flash"),300);
}

function clearCart(){
    cart = [];
    renderCart();
}

/* DISCOUNT SHOW */
document.getElementById("discountType").addEventListener("change", function(){
    document.getElementById("customerInfo").style.display =
        (this.value === "senior" || this.value === "pwd") ? "block":"none";
});

/* ENTER KEY ADD */
document.getElementById("searchBox").addEventListener("keypress", function(e){
    if(e.key==="Enter"){
        let first = document.querySelector(".product");
        if(first) first.click();
    }
});