<?php
session_start();

// Security check: ensure the user is logged in before processing the upload.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Access denied. Please log in.");
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- Define the upload directory ---
    $upload_dir = "images/";
    
    // --- Get form data ---
    $service_name = $_POST['service_name'];
    $service_description = $_POST['service_description'];
    
    // --- Handle the file upload ---
    if (isset($_FILES["service_image"]) && $_FILES["service_image"]["error"] == 0) {
        $allowed_types = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png", "gif" => "image/gif"];
        $filename = $_FILES["service_image"]["name"];
        $filetype = $_FILES["service_image"]["type"];
        $filesize = $_FILES["service_image"]["size"];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed_types)) {
            die("Error: Please select a valid file format (JPG, PNG, GIF).");
        }

        // Verify file size (e.g., 5MB maximum)
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            die("Error: File size is larger than the allowed limit of 5MB.");
        }

        // Verify MIME type of the file
        if (in_array($filetype, $allowed_types)) {
            // Create a new, unique filename to avoid overwriting existing files
            $new_filename = uniqid() . "." . $ext;
            $target_filepath = $upload_dir . $new_filename;

            // Move the file from the temporary directory to the final destination
            if (move_uploaded_file($_FILES["service_image"]["tmp_name"], $target_filepath)) {
                
                // --- SUCCESS: File is uploaded ---
                // Now, you would save the service details to your database or CSV file.
                // For example, you would store:
                // $service_name, $service_description, and $new_filename
                
                echo "The service '" . htmlspecialchars($service_name) . "' was added successfully.";
                echo "<br>File uploaded as: " . htmlspecialchars($new_filename);
                // Redirect back to the admin panel after a short delay
                header("refresh:3;url=admin.php");

            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Error: There was a problem with your file upload.";
        }
    } else {
        echo "Error: " . $_FILES["service_image"]["error"];
    }
}
?>
