const messageDiv = document.getElementById("message");

async function cancelPendingRequest(username) {
  const formData = new FormData();
  formData.append("username", username);

  const response = await fetch("./api/cancel_friend_request.php", {
    method: "POST",
    body: formData,
  });

  const result = await response.json();

  if (result.success) {
    // OK
    const messageDiv = document.getElementById("message");
    messageDiv.textContent = `Successfully canceled the friend request going to ${result.user_unadded}.`;
    messageDiv.classList.add("text-green-600");
    messageDiv.classList.remove("text-red-600");
      messageDiv.style.display = "block";
      
    return window.location.reload();
  } else {
    messageDiv.textContent =
      result.error || "User could not be unadded. Please try again.";
    messageDiv.classList.add("text-red-600");
    messageDiv.classList.remove("text-green-600");
    messageDiv.style.display = "block";
  }
}

document.addEventListener("DOMContentLoaded", async function () {
  document
    .querySelectorAll('button[id^="cancel-request?"]')
    .forEach((button) => {
      button.addEventListener("click", async function () {
        const receiverUsername = this.id.split("?")[1];
        cancelPendingRequest(receiverUsername);
      });
    });
});
