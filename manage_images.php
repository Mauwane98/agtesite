<?php
session_start();

// Security check: Redirect to login if not logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$images_json_file = 'data/images.json';
$upload_directory = 'images/';

// Ensure the upload directory exists
if (!is_dir($upload_directory)) {
    mkdir($upload_directory, 0777, true); // Create directory with write permissions
}

// Load existing site images data
$site_images = [];
if (file_exists($images_json_file)) {
    $site_images = json_decode(file_get_contents($images_json_file), true);
}

// Function to handle file upload and update $site_images array
function handleImageUpload($file_input_name, $image_key, &$site_images, $upload_dir) {
    global $upload_directory; // Use global for upload_directory

    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES[$file_input_name]['tmp_name'];
        $file_name = basename($_FILES[$file_input_name]['name']);
        $target_file = $upload_dir . $file_name;

        $current_image_path = $site_images[$image_key] ?? '';

        if (move_uploaded_file($file_tmp_name, $target_file)) {
            if ($current_image_path && file_exists($upload_dir . $current_image_path) && $current_image_path != $file_name) {
                unlink($upload_dir . $current_image_path);
            }
            $site_images[$image_key] = $file_name;
            return true; // Indicate success
        } else {
            error_log("Failed to move uploaded file for {$image_key}: " . $_FILES[$file_input_name]['error']);
            return false; // Indicate failure
        }
    }
    return true; // No file uploaded for this key, consider it not an error for this function
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success_count = 0;
    $error_messages = [];

    // Process Global Images
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        if (handleImageUpload('logo', 'logo', $site_images, $upload_directory)) $success_count++; else $error_messages[] = "Failed to upload logo.";
    }
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
        if (handleImageUpload('banner', 'banner', $site_images, $upload_directory)) $success_count++; else $error_messages[] = "Failed to upload banner.";
    }
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
        if (handleImageUpload('hero_image', 'hero_image', $site_images, $upload_directory)) $success_count++; else $error_messages[] = "Failed to upload hero image.";
    }

    // Process PPE Page Images
    if (isset($_FILES['ppe_product_showcase_1']) && $_FILES['ppe_product_showcase_1']['error'] === UPLOAD_ERR_OK) {
        if (handleImageUpload('ppe_product_showcase_1', 'ppe_product_showcase_1', $site_images, $upload_directory)) $success_count++; else $error_messages[] = "Failed to upload PPE Product Image 1.";
    }
    if (isset($_FILES['ppe_product_showcase_2']) && $_FILES['ppe_product_showcase_2']['error'] === UPLOAD_ERR_OK) {
        if (handleImageUpload('ppe_product_showcase_2', 'ppe_product_showcase_2', $site_images, $upload_directory)) $success_count++; else $error_messages[] = "Failed to upload PPE Product Image 2.";
    }

    // Process Professional Services Page Images
    if (isset($_FILES['professional_services_safety_pic']) && $_FILES['professional_services_safety_pic']['error'] === UPLOAD_ERR_OK) {
        if (handleImageUpload('professional_services_safety_pic', 'professional_services_safety_pic', $site_images, $upload_directory)) $success_count++; else $error_messages[] = "Failed to upload Safety Picture.";
    }
    if (isset($_FILES['professional_services_crossfire_logo']) && $_FILES['professional_services_crossfire_logo']['error'] === UPLOAD_ERR_OK) {
        if (handleImageUpload('professional_services_crossfire_logo', 'professional_services_crossfire_logo', $site_images, $upload_directory)) $success_count++; else $error_messages[] = "Failed to upload Crossfire Logo.";
    }

    // Process Construction Project Gallery Images
    for ($i = 1; $i <= 6; $i++) {
        $input_name = 'construction_gallery_' . $i;
        $image_key = 'construction_gallery_' . $i;
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
            if (handleImageUpload($input_name, $image_key, $site_images, $upload_directory)) $success_count++; else $error_messages[] = "Failed to upload Construction Gallery Image {$i}.";
        }
    }

    // Process Signage Team Images
    for ($i = 1; $i <= 4; $i++) {
        $input_name = 'signage_team_' . $i;
        $image_key = 'signage_team_' . $i;
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
            if (handleImageUpload($input_name, $image_key, $site_images, $upload_directory)) $success_count++; else $error_messages[] = "Failed to upload Signage Team Image {$i}.";
        }
    }

    // Save updated site images data back to JSON file
    if (file_put_contents($images_json_file, json_encode($site_images, JSON_PRETTY_PRINT))) {
        if (empty($error_messages)) {
            $_SESSION['message'] = "All selected images updated successfully!";
        } else {
            $_SESSION['message'] = "Images updated with some issues: " . implode(", ", $error_messages);
            $_SESSION['error'] = true; // Mark as error for styling
        }
    } else {
        $_SESSION['error'] = "Failed to save image data to JSON file.";
    }

    // Redirect back to admin page, specifically to the images section
    header('Location: admin.php#images');
    exit;

} else {
    // If accessed directly without POST, redirect to admin page
    header('Location: admin.php');
    exit;
}
?>
