document.getElementById("register-form").addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new FormData(e.target);
  const response = await fetch("./api/register.php", {
    method: "POST",
    body: formData,
  });

  const result = await response.json();

  if (result.success) {
    const loginData = new FormData();
    loginData.append("username_email", formData.get("username"));
    loginData.append("password", formData.get("password"));
        
    const loginResponse = await fetch("./api/login.php", {
      method: "POST",
      body: loginData,
    });
        
    const loginResult = await loginResponse.json();
    const messageDiv = document.getElementById("message");

    if (loginResult.success) {
      messageDiv.textContent = "Registration successful! Logging you in...";
      messageDiv.classList.add("text-green-600");
      messageDiv.classList.remove("text-red-600");
      messageDiv.classList.remove("hidden");
      window.location.href = "./home";
    } else {
      messageDiv.textContent = loginResult.error || "Login after registration failed. Please try logging in manually.";
      messageDiv.classList.add("text-red-600");
      messageDiv.classList.remove("text-green-600");
      messageDiv.classList.remove("hidden");
    }
  } else {
    messageDiv.textContent = result.error || "Registration failed. Please try again.";
    messageDiv.classList.add("text-red-600");
    messageDiv.classList.remove("text-green-600");
    messageDiv.classList.remove("hidden");
  }
});
