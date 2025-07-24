<?php
session_start();

// Security check: Redirect to login if not logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$contact_info_file = 'data/contact_info.json';
$upload_directory = 'images/';

// Ensure the upload directory exists and is writable
if (!is_dir($upload_directory)) {
    if (!mkdir($upload_directory, 0777, true)) {
        $_SESSION['error'] = "Error: Upload directory '{$upload_directory}' could not be created. Check parent directory permissions.";
        header('Location: admin.php#contact');
        exit;
    }
} elseif (!is_writable($upload_directory)) {
    $_SESSION['error'] = "Error: Upload directory '{$upload_directory}' is not writable. Check permissions (e.g., chmod 777).";
    header('Location: admin.php#contact');
    exit;
}

// Ensure the data directory exists and is writable
if (!is_dir('data/')) {
    if (!mkdir('data/', 0777, true)) {
        $_SESSION['error'] = "Error: Data directory 'data/' could not be created. Check parent directory permissions.";
        header('Location: admin.php#contact');
        exit;
    }
}


// Load existing contact info
$contact_info = [];
if (file_exists($contact_info_file)) {
    $json_content = file_get_contents($contact_info_file);
    $contact_info = json_decode($json_content, true);
    if ($contact_info === null && json_last_error() !== JSON_ERROR_NONE) {
        $_SESSION['error'] = "Error reading contact_info.json: " . json_last_error_msg();
        $contact_info = []; // Initialize as empty array to prevent further errors
    }
} else {
    // If contact_info.json doesn't exist, create it with an empty structure
    $initial_contact_data = [
        "susan" => ["cell_phone" => "", "telephone" => "", "email" => "", "fax" => "", "photo" => ""],
        "danny" => ["cell_phone" => "", "email" => "", "photo" => ""]
    ];
    if (!file_put_contents($contact_info_file, json_encode($initial_contact_data, JSON_PRETTY_PRINT))) {
        $_SESSION['error'] = "Error: contact_info.json could not be created or written to. Check data/ directory permissions.";
        header('Location: admin.php#contact');
        exit;
    }
    $contact_info = $initial_contact_data;
}


// Function to handle file upload
function handleFileUpload($file_input_name, $current_image_path, $upload_dir) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES[$file_input_name]['tmp_name'];
        $file_name = basename($_FILES[$file_input_name]['name']);
        $target_file = $upload_dir . $file_name;

        // Check if the target file already exists and is the same as the current image
        if ($current_image_path == $file_name && file_exists($target_file)) {
            return $file_name; // No need to re-upload or delete
        }

        // Attempt to move the uploaded file
        if (move_uploaded_file($file_tmp_name, $target_file)) {
            // If there was an old image, delete it to prevent accumulation
            if ($current_image_path && file_exists($upload_dir . $current_image_path) && $current_image_path != $file_name) {
                unlink($upload_dir . $current_image_path);
                error_log("Old image '{$current_image_path}' deleted for {$file_input_name}.");
            }
            return $file_name; // Return new file name
        } else {
            // Log specific move_uploaded_file errors
            error_log("Failed to move uploaded file for {$file_input_name}. Error: " . $_FILES[$file_input_name]['error'] . ". From: {$file_tmp_name} To: {$target_file}");
            return $current_image_path; // Keep old path on error
        }
    } elseif (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_NO_FILE) {
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
        $_SESSION['error'] = "Upload error for {$file_input_name}: " . ($phpFileUploadErrors[$_FILES[$file_input_name]['error']] ?? 'Unknown error');
        error_log("Upload error for {$file_input_name}: " . $_FILES[$file_input_name]['error']);
    }
    return $current_image_path; // No new file uploaded or error, keep old path
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $has_error = false; // Flag to track if any error occurred during processing

    // Update Susan's info
    if (!isset($contact_info['susan'])) {
        $contact_info['susan'] = []; // Initialize if not set
    }
    $contact_info['susan']['cell_phone'] = trim($_POST['susan_cell_phone'] ?? ($contact_info['susan']['cell_phone'] ?? ''));
    $contact_info['susan']['telephone'] = trim($_POST['susan_telephone'] ?? ($contact_info['susan']['telephone'] ?? ''));
    $contact_info['susan']['email'] = trim($_POST['susan_email'] ?? ($contact_info['susan']['email'] ?? ''));
    $contact_info['susan']['fax'] = trim($_POST['susan_fax'] ?? ($contact_info['susan']['fax'] ?? ''));
    
    $susan_photo_result = handleFileUpload('susan_photo', $contact_info['susan']['photo'] ?? '', $upload_directory);
    if ($susan_photo_result === false) { // handleFileUpload returns false on critical move error
        $has_error = true;
    } else {
        $contact_info['susan']['photo'] = $susan_photo_result;
    }

    // Update Danny's info
    if (!isset($contact_info['danny'])) {
        $contact_info['danny'] = []; // Initialize if not set
    }
    $contact_info['danny']['cell_phone'] = trim($_POST['danny_cell_phone'] ?? ($contact_info['danny']['cell_phone'] ?? ''));
    $contact_info['danny']['email'] = trim($_POST['danny_email'] ?? ($contact_info['danny']['email'] ?? ''));

    $danny_photo_result = handleFileUpload('danny_photo', $contact_info['danny']['photo'] ?? '', $upload_directory);
    if ($danny_photo_result === false) { // handleFileUpload returns false on critical move error
        $has_error = true;
    } else {
        $contact_info['danny']['photo'] = $danny_photo_result;
    }

    // Save updated contact info back to JSON file
    if (file_put_contents($contact_info_file, json_encode($contact_info, JSON_PRETTY_PRINT))) {
        if (!$has_error && !isset($_SESSION['error'])) { // Only set success message if no prior errors
            $_SESSION['message'] = "Contact information updated successfully!";
        } elseif ($has_error && !isset($_SESSION['error'])) {
             $_SESSION['error'] = "Contact information updated, but some images failed to upload.";
        }
    } else {
        $_SESSION['error'] = "Failed to save contact information to JSON file. Check data/contact_info.json permissions.";
        error_log("Failed to write to contact_info.json: " . $contact_info_file);
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
