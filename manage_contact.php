<?php
session_start();

// Security check: Redirect to login if not logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$contact_info_file = 'data/contact_info.json';
$upload_directory = 'images/';

// Ensure the upload directory exists
if (!is_dir($upload_directory)) {
    mkdir($upload_directory, 0777, true); // Create directory with write permissions
}

// Load existing contact info
$contact_info = [];
if (file_exists($contact_info_file)) {
    $contact_info = json_decode(file_get_contents($contact_info_file), true);
}

// Function to handle file upload
function handleFileUpload($file_input_name, $current_image_path, $upload_dir) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES[$file_input_name]['tmp_name'];
        $file_name = basename($_FILES[$file_input_name]['name']);
        $target_file = $upload_dir . $file_name;

        // Move the uploaded file
        if (move_uploaded_file($file_tmp_name, $target_file)) {
            // If there was an old image, delete it to prevent accumulation
            if ($current_image_path && file_exists($upload_dir . $current_image_path) && $current_image_path != $file_name) {
                unlink($upload_dir . $current_image_path);
            }
            return $file_name; // Return new file name
        } else {
            // Handle upload error
            error_log("Failed to move uploaded file: " . $_FILES[$file_input_name]['error']);
            return $current_image_path; // Keep old path on error
        }
    }
    return $current_image_path; // No new file uploaded or error, keep old path
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update Susan's info
    if (!isset($contact_info['susan'])) {
        $contact_info['susan'] = []; // Initialize if not set
    }
    $contact_info['susan']['cell_phone'] = $_POST['susan_cell_phone'] ?? ($contact_info['susan']['cell_phone'] ?? '');
    $contact_info['susan']['telephone'] = $_POST['susan_telephone'] ?? ($contact_info['susan']['telephone'] ?? '');
    $contact_info['susan']['email'] = $_POST['susan_email'] ?? ($contact_info['susan']['email'] ?? '');
    $contact_info['susan']['fax'] = $_POST['susan_fax'] ?? ($contact_info['susan']['fax'] ?? '');
    $contact_info['susan']['photo'] = handleFileUpload('susan_photo', $contact_info['susan']['photo'] ?? '', $upload_directory);

    // Update Danny's info
    if (!isset($contact_info['danny'])) {
        $contact_info['danny'] = []; // Initialize if not set
    }
    $contact_info['danny']['cell_phone'] = $_POST['danny_cell_phone'] ?? ($contact_info['danny']['cell_phone'] ?? '');
    $contact_info['danny']['email'] = $_POST['danny_email'] ?? ($contact_info['danny']['email'] ?? '');
    $contact_info['danny']['photo'] = handleFileUpload('danny_photo', $contact_info['danny']['photo'] ?? '', $upload_directory);

    // Save updated contact info back to JSON file
    if (file_put_contents($contact_info_file, json_encode($contact_info, JSON_PRETTY_PRINT))) {
        $_SESSION['message'] = "Contact information updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update contact information.";
    }

    // Redirect back to admin page
    header('Location: admin.php#contact');
    exit;
} else {
    // If accessed directly without POST, redirect to admin page
    header('Location: admin.php');
    exit;
}
?>
