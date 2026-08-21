document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("togglePassword");
    const password = document.getElementById("password");

    if (toggle && password) {
        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            const type = password.type === "password" ? "text" : "password";
            password.type = type;

            // Change icon
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    }
});
