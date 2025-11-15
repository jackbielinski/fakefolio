<?php
include "../include/backend/main.php";
include "../include/page_elements/errors.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Grab user data and see what changed. Do not count null fields. If null, keep existing value
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "error" => find_error_key("required")]);
        exit();
    } else {
        $userId = $_SESSION['user_id'];
    }

    $stmt = $pdo->prepare("SELECT display_name, username, avatar, banner_img FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // keep existing avatar and banner by default; only replace when a new upload is provided
    $avatar = $user['avatar'];
    $banner = $user['banner_img'];

    $displayName = trim($_POST['display_name']) ?: $user['display_name'];
    $username = $user['username'];

    // Handle avatar upload. Must be image file (png or jpg) and the max size is 100kb
    if (!empty($_FILES['avatar_upload']) && $_FILES['avatar_upload']['error'] === UPLOAD_ERR_OK) {
        // Rename file to userID_timestamp.extension to avoid conflicts
        $fileTmpPath = $_FILES['avatar_upload']['tmp_name'];
        $fileNameCmps = explode(".", $_FILES['avatar_upload']['name']);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = $userId . "_" . time() . "." . $fileExtension;
        $allowedfileExtensions = ['jpg', 'jpeg', 'png'];

        // Validate extension first
        if (!in_array($fileExtension, $allowedfileExtensions)) {
            header("Location: ../profile/edit?error=invalid_avatar_type");
            exit();
        }

        // enforce max size 100KB
        if ($_FILES['avatar_upload']['size'] > 102400) {
            header("Location: ../profile/edit?error=avatar_too_large");
            exit();
        }

        $imgInfo = getimagesize($fileTmpPath);
        if ($imgInfo === false) {
            header("Location: ../profile/edit?error=invalid_avatar_type");
            exit();
        }

        $mime = $imgInfo['mime'];
        switch ($mime) {
            case 'image/png':
                $source = imagecreatefrompng($fileTmpPath);
                $isPng = true;
                break;
            case 'image/jpeg':
            case 'image/jpg':
                $source = imagecreatefromjpeg($fileTmpPath);
                $isPng = false;
                break;
            default:
                header("Location: ../profile/edit?error=invalid_avatar_type");
                exit();
        }

        // target size
        $newWidth = 128;
        $newHeight = 128;

        $width  = $imgInfo[0];
        $height = $imgInfo[1];

        // create destination image
        $thumb = imagecreatetruecolor($newWidth, $newHeight);

        // preserve transparency for PNGs
        if ($isPng) {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            $transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
            imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $transparent);
        } else {
            // white background for JPEG
            $white = imagecolorallocate($thumb, 255, 255, 255);
            imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $white);
        }

        // center-crop to maintain aspect ratio
        $src_x = $src_y = 0;
        $src_w = $width;
        $src_h = $height;

        $src_aspect = $width / $height;
        $dst_aspect = $newWidth / $newHeight;

        if ($src_aspect > $dst_aspect) {
            // source is wider -> crop sides
            $src_w = (int)($height * $dst_aspect);
            $src_x = (int)(($width - $src_w) / 2);
        } else {
            // source is taller -> crop top/bottom
            $src_h = (int)($width / $dst_aspect);
            $src_y = (int)(($height - $src_h) / 2);
        }

        imagecopyresampled($thumb, $source, 0, 0, $src_x, $src_y, $newWidth, $newHeight, $src_w, $src_h);

        $destDir = ROOT_PATH . "/_static/avatar/";
        if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
            header("Location: ../profile/edit?error=mkdir_failed");
            exit();
        }

        $filePath = $destDir . $newFileName;

        // save thumbnail (do not move the original tmp file) using ternary
        $saved = $isPng ? imagepng($thumb, $filePath) : imagejpeg($thumb, $filePath, 85);

        imagedestroy($source);
        imagedestroy($thumb);

        if ($saved) {
            $avatar = "avatar/" . $newFileName;
        } else {
            echo json_encode(["success" => false, "error" => find_error_key("save_failed")]);
            exit();
        }
    } elseif (!empty($_FILES['avatar_upload']) && $_FILES['avatar_upload']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo json_encode(["success" => false, "error" => find_error_key("upload_error")]);
        exit();
    }

    // Handle banner upload. Must be image file (png or jpg) and the max size is 2MB
    if (!empty($_FILES['banner_upload']) && $_FILES['banner_upload']['error'] === UPLOAD_ERR_OK) {
        // Rename file to userID_timestamp.extension to avoid conflicts
        $fileTmpPath = $_FILES['banner_upload']['tmp_name'];
        $fileNameCmps = explode(".", $_FILES['banner_upload']['name']);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = $userId . "_banner_" . time() . "." . $fileExtension;
        $allowedfileExtensions = ['jpg', 'jpeg', 'png'];

        // Validate extension first
        if (!in_array($fileExtension, $allowedfileExtensions)) {
            header("Location: ../profile/edit?error=invalid_banner_type");
            exit();
        }

        // enforce max size 2MB
        if ($_FILES['banner_upload']['size'] > 2097152) {
            header("Location: ../profile/edit?error=banner_too_large");
            exit();
        }

        // If image is not 8.07:1, resize
        $imgInfo = getimagesize($fileTmpPath);
        $resized = null;
        if ($imgInfo === false || abs($imgInfo[0] / $imgInfo[1] - 8.07) > 0.1) {
            // Resize logic here
            $thumb = imagecreatetruecolor(807, 100);
            $source = imagecreatefromstring(file_get_contents($fileTmpPath));
            // preserve transparency if PNG
            if (isset($source) && ($fileExtension === 'png')) {
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
                $transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
                imagefilledrectangle($thumb, 0, 0, 807, 100, $transparent);
            }
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, 807, 100, $imgInfo[0], $imgInfo[1]);
            imagedestroy($source);
            $resized = $thumb;
        }

        $destDir = ROOT_PATH . "/_static/banner/uploads/";
        if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
            header("Location: ../profile/edit?error=mkdir_failed");
            exit();
        }

        $filePath = $destDir . $newFileName;

        if ($resized && is_resource($resized)) {
            // Save resized resource directly
            $saved = ($fileExtension === 'png') ? imagepng($resized, $filePath) : imagejpeg($resized, $filePath, 85);
            imagedestroy($resized);
            if ($saved) {
                $banner = "banner/uploads/" . $newFileName;
            } else {
                echo json_encode(["success" => false, "error" => find_error_key("save_failed")]);
                exit();
            }
        } else {
            // No resizing done, move uploaded file
            if (move_uploaded_file($fileTmpPath, $filePath)) {
                $banner = "banner/uploads/" . $newFileName;
            } else {
                echo json_encode(["success" => false, "error" => find_error_key("upload_error")]);
                exit();
            }
        }
    } elseif (!empty($_FILES['banner_upload']) && $_FILES['banner_upload']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo json_encode(["success" => false, "error" => find_error_key("upload_error")]);
        exit();
    }

    // Validate and update user information
    $stmt = $pdo->prepare("UPDATE users SET display_name = :display_name, avatar = :avatar, banner_img = :banner WHERE id = :id");
    $stmt->execute([
        'display_name' => $displayName,
        'avatar' => $avatar,
        'banner' => $banner,
        'id' => $userId
    ]);

    echo json_encode(["success" => true]);
    exit();
}