document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("togglePassword");
    const password = document.getElementById("password");

    if (toggle && password) {
        const icon = toggle.querySelector("i");

        const updateToggle = function () {
            const isPasswordVisible = password.type === "text";

            if (icon) {
                icon.classList.toggle("fa-eye", !isPasswordVisible);
                icon.classList.toggle("fa-eye-slash", isPasswordVisible);
            }

            toggle.setAttribute("aria-label", isPasswordVisible ? "Hide password" : "Show password");
            toggle.setAttribute("title", isPasswordVisible ? "Hide password" : "Show password");
        };

        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            password.type = password.type === "password" ? "text" : "password";
            updateToggle();
        });

        updateToggle();
    }
});
