document.addEventListener("DOMContentLoaded", () => {
    // Limpia errores al enfocar los campos
    document.querySelectorAll(".input-field input").forEach(input => {
        input.addEventListener("focus", () => {
            // Quita la clase 'wrong-data' del contenedor .input-field
            input.closest(".input-field").classList.remove("wrong-data");
        });
    });

    // Mostrar/ocultar contraseña
    document.querySelector("#togglePassword").addEventListener("click", function () {
        const passwordInput = document.querySelector("#password input");
        if (passwordInput) {
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        }
    });
});
