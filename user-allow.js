
    const registerButton = document.getElementById("register-button");
    const registerForm = document.getElementById("register-form");
    registerButton.addEventListener("click", function(event) {
        const passwordStrength = document.getElementById("password-strength").textContent.trim();
        if (passwordStrength !== "Strong") {
            event.preventDefault();
            document.getElementById("password-missing").textContent = "Please enter a strong password.";
        } else {
            registerForm.submit();
        }
    });

