const messageDiv = document.getElementById("message");

async function addFriend(formData) {
    const response = await fetch("../api/add_friend.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
        // OK
      const messageDiv = document.getElementById("message");
        messageDiv.textContent = `Successfully sent a friend request to ${result.user_added}.`;
        messageDiv.classList.add("text-green-600");
        messageDiv.classList.remove("text-red-600");
        messageDiv.style.display = "block";
    } else {
        messageDiv.textContent =
          result.error || "User could not be added. Please try again.";
        messageDiv.classList.add("text-red-600");
          messageDiv.classList.remove("text-green-600");
        messageDiv.style.display = "block";
    }
}

async function cancelPendingRequest(formData) {
    const response = await fetch("../api/cancel_friend_request.php", {
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
    } else {
        messageDiv.textContent =
          result.error || "User could not be added. Please try again.";
        messageDiv.classList.add("text-red-600");
        messageDiv.classList.remove("text-green-600");
        messageDiv.style.display = "block";
    }
}

document
  .getElementById("add-friend-form")
  .addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);

    await addFriend(formData);

    messageDiv.style.display = "block";
  });

// if get param u is present, prefill the username field
window.addEventListener("DOMContentLoaded", async () => {
  // Submit form with username from URL parameter
  const urlParams = new URLSearchParams(window.location.search);
  const usernameParam = urlParams.get("u");
    if (usernameParam) {
        // put it in the username field and submit the form
        document.getElementById("username").value = usernameParam;
        const formData = new FormData();
        formData.append("username", usernameParam);
        await addFriend(formData);

        messageDiv.style.display = "block";
  }
});

// if get param cancel is present, send cancel friend request
window.addEventListener("DOMContentLoaded", async () => {
// Submit form with username from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const cancelParam = urlParams.get("cancel");
    if (cancelParam) {
        const formData = new FormData();
        formData.append("username", cancelParam);
        await cancelPendingRequest(formData);
        messageDiv.style.display = "block";
    }
});