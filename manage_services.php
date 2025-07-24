<?php
session_start(); // Start the session at the very beginning

// Security check: Redirect to login if not logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$services_file = 'data/services.csv';
$upload_directory = 'images/';

// Ensure the upload directory exists
if (!is_dir($upload_directory)) {
    mkdir($upload_directory, 0777, true);
}

// Load existing services
$services = [];
if (file_exists($services_file) && ($handle = fopen($services_file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $services[] = ['name' => $data[0], 'description' => $data[1], 'image' => $data[2]];
    }
    fclose($handle);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $service_name = $_POST['service_name'] ?? '';
        $service_description = $_POST['service_description'] ?? '';
        $service_image = ''; // Default to empty

        // Handle image upload
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp_name = $_FILES['service_image']['tmp_name'];
            $file_name = basename($_FILES['service_image']['name']);
            $target_file = $upload_directory . $file_name;

            if (move_uploaded_file($file_tmp_name, $target_file)) {
                $service_image = $file_name;
            } else {
                $_SESSION['error'] = "Failed to upload service image. Error: " . $_FILES['service_image']['error'];
                header('Location: admin.php#services'); // Redirect with error
                exit;
            }
        } else {
            $_SESSION['error'] = "Service image file is required.";
            header('Location: admin.php#services'); // Redirect with error
            exit;
        }

        if (!empty($service_name) && !empty($service_description) && !empty($service_image)) {
            $services[] = ['name' => $service_name, 'description' => $service_description, 'image' => $service_image];
            
            // Write updated services back to CSV
            if (($handle = fopen($services_file, "w")) !== FALSE) {
                foreach ($services as $service) {
                    fputcsv($handle, $service);
                }
                fclose($handle);
                $_SESSION['message'] = "Service added successfully!";
            } else {
                $_SESSION['error'] = "Failed to open services file for writing.";
            }
        } else {
            $_SESSION['error'] = "All service fields are required.";
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? null;

    if ($action === 'delete' && $id !== null) {
        if (isset($services[$id])) {
            $image_to_delete = $services[$id]['image'];
            array_splice($services, $id, 1); // Remove the service
            
            // Write updated services back to CSV
            if (($handle = fopen($services_file, "w")) !== FALSE) {
                foreach ($services as $service) {
                    fputcsv($handle, $service);
                }
                fclose($handle);
                $_SESSION['message'] = "Service deleted successfully!";

                // Delete the associated image file
                if ($image_to_delete && file_exists($upload_directory . $image_to_delete)) {
                    unlink($upload_directory . $image_to_delete);
                }
            } else {
                $_SESSION['error'] = "Failed to open services file for writing.";
            }
        } else {
            $_SESSION['error'] = "Service not found.";
        }
    }
}

// Redirect back to admin page, specifically to the services section
header('Location: admin.php#services');
exit;
?>
