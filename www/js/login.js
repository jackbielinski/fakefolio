document.getElementById("login-form").addEventListener("submit", async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const response = await fetch("./api/login.php", {
        method: "POST",
        body: formData
    });

    const result = await response.json();

    if (result.success) {
        window.location.href = "./home";
    } else {
        const errorDiv = document.getElementById("error-message");
        errorDiv.textContent = result.error || "Login failed. Please try again.";
        errorDiv.style.display = "block";
    }
});
