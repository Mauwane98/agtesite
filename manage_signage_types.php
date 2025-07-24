<?php
session_start();

// Security check: Redirect to login if not logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$signage_types_file = 'data/signage_types.json';

// Ensure the data directory exists and is writable
if (!is_dir('data/')) {
    if (!mkdir('data/', 0777, true)) {
        $_SESSION['error'] = "Error: Data directory 'data/' could not be created.";
        header('Location: admin.php#signage-types');
        exit;
    }
}

// Load existing signage types
$signage_types = [];
if (file_exists($signage_types_file)) {
    $json_content = file_get_contents($signage_types_file);
    $signage_types = json_decode($json_content, true);
    if ($signage_types === null && json_last_error() !== JSON_ERROR_NONE) {
        $_SESSION['error'] = "Error reading signage_types.json: " . json_last_error_msg();
        $signage_types = []; // Initialize as empty array to prevent further errors
    }
} else {
    // If signage_types.json doesn't exist, create it with an empty array
    if (!file_put_contents($signage_types_file, json_encode([]))) {
        $_SESSION['error'] = "Error: signage_types.json could not be created or written to.";
        header('Location: admin.php#signage-types');
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image_key = trim($_POST['image_key'] ?? '');

        if (!empty($name) && !empty($description) && !empty($image_key)) {
            $signage_types[] = [
                'name' => $name,
                'description' => $description,
                'image_key' => $image_key
            ];
            
            // Save updated signage types back to JSON
            if (file_put_contents($signage_types_file, json_encode($signage_types, JSON_PRETTY_PRINT))) {
                $_SESSION['message'] = "Signage type '{$name}' added successfully!";
            } else {
                $_SESSION['error'] = "Failed to save signage type data. Check data/signage_types.json permissions.";
                error_log("Failed to write to signage_types.json: " . $signage_types_file);
            }
        } else {
            $_SESSION['error'] = "All fields (Name, Description, Image Key) are required to add a signage type.";
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? null;

    if ($action === 'delete' && $id !== null) {
        if (isset($signage_types[$id])) {
            $deleted_name = $signage_types[$id]['name'];
            array_splice($signage_types, $id, 1); // Remove the signage type
            
            // Save updated signage types back to JSON
            if (file_put_contents($signage_types_file, json_encode($signage_types, JSON_PRETTY_PRINT))) {
                $_SESSION['message'] = "Signage type '{$deleted_name}' deleted successfully!";
            } else {
                $_SESSION['error'] = "Failed to save signage type data after deletion. Check data/signage_types.json permissions.";
                error_log("Failed to write to signage_types.json after delete: " . $signage_types_file);
            }
        } else {
            $_SESSION['error'] = "Signage type not found for deletion.";
        }
    }
}

// Redirect back to admin page, specifically to the signage types section
header('Location: admin.php#signage-types');
exit;
?>
