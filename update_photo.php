<?php
session_start();

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Access denied.");
}

$services_file = 'data/services.csv';
$upload_dir = 'images/';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['service_id'])) {
    $service_id = (int)$_POST['service_id'];
    
    // Read all services from the CSV file
    $services = [];
    if (($handle = fopen($services_file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $services[] = $data;
        }
        fclose($handle);
    }

    // Check if the service exists
    if (isset($services[$service_id])) {
        // Check if a new image was uploaded
        if (isset($_FILES["new_service_image"]) && $_FILES["new_service_image"]["error"] == 0) {
            
            // --- Delete the old image ---
            $old_image = $services[$service_id][2];
            if (file_exists($upload_dir . $old_image)) {
                unlink($upload_dir . $old_image);
            }

            // --- Upload the new image ---
            $allowed_types = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png"];
            $filename = $_FILES["new_service_image"]["name"];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (array_key_exists($ext, $allowed_types)) {
                $new_filename = uniqid() . "." . $ext;
                if (move_uploaded_file($_FILES["new_service_image"]["tmp_name"], $upload_dir . $new_filename)) {
                    // Update the image filename in the services array
                    $services[$service_id][2] = $new_filename;
                }
            }
        }
        
        // --- Write the updated services array back to the CSV file ---
        $handle = fopen($services_file, 'w');
        foreach ($services as $service) {
            fputcsv($handle, $service);
        }
        fclose($handle);
    }
}

// Redirect back to the admin panel
header("Location: admin.php#services");
exit;
?>
