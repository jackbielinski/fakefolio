document
  .getElementById("edit-settings-form")
  .addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const response = await fetch("../api/edit_account_settings.php", {
      method: "POST",
      body: formData,
    });
      
      const result = await response.json();

      if (result.success) {
          // OK
        const messageDiv = document.getElementById("message");
          messageDiv.textContent = "Settings updated successfully.";
          messageDiv.classList.add("text-green-600");
          messageDiv.classList.remove("text-red-600");
          messageDiv.style.display = "block";
      } else {
          messageDiv.textContent =
            result.error || "Settings update failed. Please try again.";
          messageDiv.classList.add("text-red-600");
            messageDiv.classList.remove("text-green-600");
          messageDiv.style.display = "block";
      }
  });
