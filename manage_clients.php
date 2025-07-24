<?php
session_start(); // Start the session at the very beginning

// Security check: Redirect to login if not logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$clients_file = 'data/clients.csv';
$upload_directory = 'images/';

// Ensure the upload directory exists
if (!is_dir($upload_directory)) {
    mkdir($upload_directory, 0777, true);
}

// Load existing clients
$clients = [];
if (file_exists($clients_file) && ($handle = fopen($clients_file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $clients[] = ['name' => $data[0], 'logo' => $data[1]];
    }
    fclose($handle);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $client_name = $_POST['client_name'] ?? '';
        $client_logo = ''; // Default to empty

        // Handle logo upload
        if (isset($_FILES['client_logo']) && $_FILES['client_logo']['error'] === UPLOAD_ERR_OK) {
            $file_tmp_name = $_FILES['client_logo']['tmp_name'];
            $file_name = basename($_FILES['client_logo']['name']);
            $target_file = $upload_directory . $file_name;

            if (move_uploaded_file($file_tmp_name, $target_file)) {
                $client_logo = $file_name;
            } else {
                $_SESSION['error'] = "Failed to upload client logo. Error: " . $_FILES['client_logo']['error'];
                header('Location: admin.php#clients'); // Redirect with error
                exit;
            }
        } else {
            $_SESSION['error'] = "Client logo file is required.";
            header('Location: admin.php#clients'); // Redirect with error
            exit;
        }

        if (!empty($client_name) && !empty($client_logo)) {
            $clients[] = ['name' => $client_name, 'logo' => $client_logo];
            
            // Write updated clients back to CSV
            if (($handle = fopen($clients_file, "w")) !== FALSE) {
                foreach ($clients as $client) {
                    fputcsv($handle, $client);
                }
                fclose($handle);
                $_SESSION['message'] = "Client added successfully!";
            } else {
                $_SESSION['error'] = "Failed to open clients file for writing.";
            }
        } else {
            $_SESSION['error'] = "Client name and logo are required.";
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? null;

    if ($action === 'delete' && $id !== null) {
        if (isset($clients[$id])) {
            $logo_to_delete = $clients[$id]['logo'];
            array_splice($clients, $id, 1); // Remove the client
            
            // Write updated clients back to CSV
            if (($handle = fopen($clients_file, "w")) !== FALSE) {
                foreach ($clients as $client) {
                    fputcsv($handle, $client);
                }
                fclose($handle);
                $_SESSION['message'] = "Client deleted successfully!";

                // Delete the associated logo file
                if ($logo_to_delete && file_exists($upload_directory . $logo_to_delete)) {
                    unlink($upload_directory . $logo_to_delete);
                }
            } else {
                $_SESSION['error'] = "Failed to open clients file for writing.";
            }
        } else {
            $_SESSION['error'] = "Client not found.";
        }
    }
}

// Redirect back to admin page, specifically to the clients section
header('Location: admin.php#clients');
exit;
?>
