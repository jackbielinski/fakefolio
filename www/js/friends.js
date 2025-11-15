async function addFriend(username) {
    const formData = new FormData();
    formData.append("username", username);

    const response = await fetch("./api/add_friend.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
        return window.location.reload();
    }
}

async function cancelPendingRequest(username) {
    const formData = new FormData();
    formData.append("username", username);

    const response = await fetch("./api/cancel_friend_request.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
        return window.location.reload();
    }
}