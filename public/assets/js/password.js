const passwordInput = document.getElementById("password");
const toggleButton = document.querySelector(".password-toggle");
const toggleIcon = document.getElementById("togglePassword");

if (toggleButton && passwordInput && toggleIcon) {
  toggleButton.addEventListener("click", function () {
    // Toggle password visibility
    const type =
      passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);

    // Toggle icon
    toggleIcon.classList.toggle("fa-eye");
    toggleIcon.classList.toggle("fa-eye-slash");
  });
}
