const errorDiv = document.getElementById("error-message");

document.getElementById("edit-profile-form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const response = await fetch("../api/edit_profile.php", {
        method: "POST",
        body: formData,
    });

    const result = await response.json();
    console.log(result);

    if (result.success) {
        window.location.href = "../@" + formData.get("username");
    } else {
        errorDiv.textContent = result.error || "Profile update failed. Please try again.";
        errorDiv.style.display = "block";
    }
});

// make avatar preview work
document.getElementById("avatar_upload").addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            // update avatarPreview, show size compared to max size and warn if not valid
            const avatarPreview = document.getElementById("avatarPreview");
            avatarPreview.src = event.target.result;

            document.getElementById("imageMetadata").classList.remove("hidden");

            const maxSizeKB = 100;
            const fileSizeKB = (file.size / 1024).toFixed(2);
            const sizeInfo = document.getElementById("imageSize");
            sizeInfo.textContent = `file size: ${fileSizeKB} KB (max ${maxSizeKB} KB)`;

            if (fileSizeKB > maxSizeKB) {
                sizeInfo.classList.add("font-bold");
                sizeInfo.classList.add("text-red-600");
                sizeInfo.classList.remove("text-green-600");
            } else {
                sizeInfo.append(" (OK)");
                sizeInfo.classList.remove("font-bold");
                sizeInfo.classList.remove("text-red-600");
                sizeInfo.classList.add("text-green-600");
            }

            const allowedTypes = ["image/png", "image/jpeg"];
            const typeInfo = document.getElementById("imageType");
            typeInfo.textContent = `file type: ${file.type}`;

            if (!allowedTypes.includes(file.type)) {
                typeInfo.classList.add("font-bold");
                typeInfo.classList.add("text-red-600");
                typeInfo.classList.remove("text-green-600");
            } else {
                typeInfo.append(" (OK)");
                typeInfo.classList.remove("font-bold");
                typeInfo.classList.remove("text-red-600");
                typeInfo.classList.add("text-green-600");
            }

            // if both size and type are valid, show a green border around the preview
            if (fileSizeKB <= maxSizeKB && allowedTypes.includes(file.type)) {
                avatarPreview.classList.add("border-green-600");
                avatarPreview.classList.add("border-4");
                avatarPreview.classList.remove("border-red-600");
            } else {
                avatarPreview.classList.remove("border-green-600");
                avatarPreview.classList.remove("border-4");
                avatarPreview.classList.add("border-red-600");
            }
        };
        reader.readAsDataURL(file);
    }
});

// make banner preview work
document.getElementById("banner_upload").addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            // update bannerPreview, show size compared to max size and warn if not valid
            const bannerPreview = document.getElementById("bannerPreview");

            bannerPreview.src = event.target.result;

            document
              .getElementById("bannerImageMetadata")
              .classList.remove("hidden");

            const maxSizeMB = 2;
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            const sizeInfo = document.getElementById("bannerImageSize");
            sizeInfo.textContent = `file size: ${fileSizeMB} MB (max ${maxSizeMB} MB)`;

            if (fileSizeMB > maxSizeMB) {
                sizeInfo.classList.add("font-bold");
                sizeInfo.classList.add("text-red-600");
                sizeInfo.classList.remove("text-green-600");
            } else {
                sizeInfo.append(" (OK)");
                sizeInfo.classList.remove("font-bold");
                sizeInfo.classList.remove("text-red-600");
                sizeInfo.classList.add("text-green-600");
            }

            const allowedTypes = ["image/png", "image/jpeg"];
            const typeInfo = document.getElementById("bannerImageType");
            typeInfo.textContent = `file type: ${file.type}`;

            if (!allowedTypes.includes(file.type)) {
                typeInfo.classList.add("font-bold");
                typeInfo.classList.add("text-red-600");
                typeInfo.classList.remove("text-green-600");
            } else {
                typeInfo.append(" (OK)");
                typeInfo.classList.remove("font-bold");
                typeInfo.classList.remove("text-red-600");
                typeInfo.classList.add("text-green-600");
            }

            // if not in a ratio of 807 x 100, show a warning and resize preview
            const img = new Image();
            img.onload = function () {
              // Resize the image to 807 x 100
              const canvas = document.createElement("canvas");
              canvas.width = 807;
              canvas.height = 100;
              const ctx = canvas.getContext("2d");
              ctx.drawImage(img, 0, 0, 807, 100);
                bannerPreview.src = canvas.toDataURL();
                
              const width = img.width;
              const height = img.height;
              const dimensionWarning = document.getElementById(
                "bannerImageDimensionWarning"
              );
              if (width / height !== 8.07) {
                dimensionWarning.textContent =
                  "warning: recommended banner dimensions are 807 x 100 pixels. banner may appear distorted.";
              } else {
                dimensionWarning.textContent = "";
              }
            };
            img.src = event.target.result;

            // if both size and type are valid, show a green border around the preview
            if (fileSizeMB <= maxSizeMB && allowedTypes.includes(file.type)) {
                bannerPreview.classList.add("border-green-600");
                bannerPreview.classList.add("border-4");
                bannerPreview.classList.remove("border-red-600");
            } else {
                bannerPreview.classList.remove("border-green-600");
                bannerPreview.classList.remove("border-4");
                bannerPreview.classList.add("border-red-600");
            }
        };
        reader.readAsDataURL(file);
    }
});