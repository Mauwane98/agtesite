<?php
session_start();

// Security check: Redirect to login if not logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$images_json_file = 'data/images.json';
$upload_directory = 'images/';

// Ensure the upload directory exists and is writable
if (!is_dir($upload_directory)) {
    if (!mkdir($upload_directory, 0777, true)) {
        $_SESSION['error'] = "Error: Upload directory '{$upload_directory}' could not be created.";
        header('Location: admin.php#images');
        exit;
    }
} elseif (!is_writable($upload_directory)) {
    $_SESSION['error'] = "Error: Upload directory '{$upload_directory}' is not writable. Check permissions (e.g., chmod 777).";
    header('Location: admin.php#images');
    exit;
}

// Load existing site images data
$site_images = [];
if (file_exists($images_json_file)) {
    $json_content = file_get_contents($images_json_file);
    $site_images = json_decode($json_content, true);
    if ($site_images === null && json_last_error() !== JSON_ERROR_NONE) {
        $_SESSION['error'] = "Error reading images.json: " . json_last_error_msg();
        $site_images = []; // Initialize as empty array to prevent further errors
    }
} else {
    // If images.json doesn't exist, create it with an empty array
    if (!file_put_contents($images_json_file, json_encode([]))) {
        $_SESSION['error'] = "Error: images.json could not be created or written to.";
        header('Location: admin.php#images');
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_key'])) {
    $image_key = $_POST['image_key'];
    $redirect_hash = '#images'; // Default redirect to images section

    // Check if a file was uploaded for this specific key
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['image_file']['tmp_name'];
        $file_name = basename($_FILES['image_file']['name']);
        $target_file = $upload_directory . $file_name;

        // Get the current image path for the given key, if it exists
        $current_image_path = $site_images[$image_key] ?? '';

        // Attempt to move the uploaded file
        if (move_uploaded_file($file_tmp_name, $target_file)) {
            // If a new image was successfully uploaded and there was an old image, delete the old one
            if ($current_image_path && file_exists($upload_directory . $current_image_path) && $current_image_path != $file_name) {
                unlink($upload_directory . $current_image_path);
                error_log("Old image '{$current_image_path}' deleted for key '{$image_key}'.");
            }
            // Update the image path in the array
            $site_images[$image_key] = $file_name;
            $_SESSION['message'] = "Image for '{$image_key}' updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to upload image for '{$image_key}'. Move error: " . $_FILES['image_file']['error'];
            error_log("File move error for {$image_key}: " . $_FILES['image_file']['error'] . " from {$file_tmp_name} to {$target_file}");
        }
    } elseif (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle specific upload errors other than no file being selected
        $phpFileUploadErrors = array(
            UPLOAD_ERR_OK => 'There is no error, the file uploaded with success.',
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
        );
        $_SESSION['error'] = "Upload error for '{$image_key}': " . ($phpFileUploadErrors[$_FILES['image_file']['error']] ?? 'Unknown error');
        error_log("Upload error for {$image_key}: " . $_FILES['image_file']['error']);
    } else {
        // No file was uploaded, but this might be an update without changing the image
        // We don't set an error here, as it's a valid scenario.
        $_SESSION['message'] = "No new file selected for '{$image_key}'. Image path remains unchanged.";
    }

    // Save updated site images data back to JSON file
    if (file_put_contents($images_json_file, json_encode($site_images, JSON_PRETTY_PRINT))) {
        // Message already set above if upload was successful or no file selected
    } else {
        $_SESSION['error'] = "Failed to save image data to JSON file for '{$image_key}'. Check data/images.json permissions.";
        error_log("Failed to write to images.json: " . $images_json_file);
    }

    // Redirect back to admin page, maintaining scroll position
    header('Location: admin.php' . $redirect_hash);
    exit;
} else {
    // If accessed directly without POST or image_key, redirect to admin page
    header('Location: admin.php');
    exit;
}
?>
