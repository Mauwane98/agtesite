<?php
session_start();

// Security check: Redirect to login if not logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$services_file = 'data/services.csv';
$upload_directory = 'images/';
$service_id = $_GET['id'] ?? null; // Get the service ID from the URL

$services = [];
// Load existing services from CSV
if (file_exists($services_file) && ($handle = fopen($services_file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $services[] = ['name' => $data[0], 'description' => $data[1], 'image' => $data[2]];
    }
    fclose($handle);
}

$current_service = null;
if ($service_id !== null && isset($services[$service_id])) {
    $current_service = $services[$service_id];
} else {
    $_SESSION['error'] = "Service not found.";
    header('Location: admin.php#services');
    exit;
}

// Handle form submission for updating the service
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['service_name'] ?? '';
    $new_description = $_POST['service_description'] ?? '';
    $new_image = $current_service['image']; // Default to current image

    // Handle image upload if a new file is provided
    if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['service_image']['tmp_name'];
        $file_name = basename($_FILES['service_image']['name']);
        $target_file = $upload_directory . $file_name;

        // Ensure upload directory exists
        if (!is_dir($upload_directory)) {
            mkdir($upload_directory, 0777, true);
        }

        if (move_uploaded_file($file_tmp_name, $target_file)) {
            // Delete old image if it exists and is different from the new one
            if ($current_service['image'] && file_exists($upload_directory . $current_service['image']) && $current_service['image'] != $file_name) {
                unlink($upload_directory . $current_service['image']);
            }
            $new_image = $file_name;
        } else {
            $_SESSION['error'] = "Failed to upload new image. Error: " . $_FILES['service_image']['error'];
            header('Location: edit_service.php?id=' . $service_id); // Stay on edit page with error
            exit;
        }
    }

    // Update the service in the array
    $services[$service_id] = [
        'name' => $new_name,
        'description' => $new_description,
        'image' => $new_image
    ];

    // Write the updated services back to CSV
    if (($handle = fopen($services_file, "w")) !== FALSE) {
        foreach ($services as $service) {
            fputcsv($handle, $service);
        }
        fclose($handle);
        $_SESSION['message'] = "Service updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to open services file for writing.";
    }

    // Redirect back to admin page, specifically to the services section
    header('Location: admin.php#services'); 
    exit; // Ensure no further code is executed after redirection
}

// Load general website images from JSON for header
$site_images = [];
if (file_exists('data/images.json')) {
    $site_images = json_decode(file_get_contents("data/images.json"), true);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AGTE - Edit Service</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        /* Re-using some admin styles for consistency */
        .form-image-preview {
            max-width: 100%;
            height: 120px;
            object-fit: contain;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
            background-color: #e2e8f0;
            padding: 5px;
        }
        .form-group-image {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .form-input-file {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            color: #2d3748;
            transition: border-color 0.3s ease;
        }
        .message-box {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
        }
        .message-box.success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .message-box.error {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .message-box button {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            color: inherit;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex flex-col h-screen">
        <!-- Header -->
        <header class="bg-white shadow-md p-4 flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-gray-800">Edit Service</h2>
            <div>
                <a href="admin.php#services" class="action-button">Back to Admin</a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 md:p-8">
            <?php
            // Display session messages
            if (isset($_SESSION['message'])): ?>
                <div id="message-box" class="message-box success">
                    <span><?php echo htmlspecialchars($_SESSION['message']); ?></span>
                    <button onclick="this.parentElement.style.display='none';">&times;</button>
                </div>
                <?php unset($_SESSION['message']);
            elseif (isset($_SESSION['error'])): ?>
                <div id="message-box" class="message-box error">
                    <span><?php echo htmlspecialchars($_SESSION['error']); ?></span>
                    <button onclick="this.parentElement.style.display='none';">&times;</button>
                </div>
                <?php unset($_SESSION['error']);
            endif;
            ?>

            <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
                <form action="edit_service.php?id=<?php echo htmlspecialchars($service_id); ?>" method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="service-name" class="form-label">Service Name</label>
                            <input type="text" name="service_name" id="service-name" class="form-input" value="<?php echo htmlspecialchars($current_service['name'] ?? ''); ?>" required>
                        </div>
                        <div>
                            <label for="service-description" class="form-label">Description</label>
                            <textarea name="service_description" id="service-description" class="form-input h-32" required><?php echo htmlspecialchars($current_service['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group-image md:col-span-2">
                            <label class="form-label">Current Service Image</label>
                            <img id="service-image-preview" src="images/<?php echo htmlspecialchars($current_service['image'] ?? ''); ?>" alt="Current Service Image" class="form-image-preview w-full h-48">
                            <label for="service-image" class="form-label mt-4">Upload New Service Image</label>
                            <input type="file" name="service_image" id="service-image" class="form-input-file">
                        </div>
                    </div>
                    <div class="mt-6 text-right">
                        <button type="submit" class="action-button">Update Service</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageBox = document.getElementById('message-box');
            const serviceImageInput = document.getElementById('service-image');
            const serviceImagePreview = document.getElementById('service-image-preview');

            // Auto-hide message box after a few seconds
            if (messageBox) {
                setTimeout(() => {
                    messageBox.style.display = 'none';
                }, 5000); // Hide after 5 seconds
            }

            // Live Image Preview for Service Image
            if (serviceImageInput && serviceImagePreview) {
                serviceImageInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            serviceImagePreview.src = e.target.result;
                        };
                        reader.readAsDataURL(this.files[0]);
                    } else {
                        // Revert to original image if no new file is selected
                        // This requires storing the original image path,
                        // for now, we'll just clear it or let the PHP default handle it on reload.
                        // A more robust solution would involve a hidden input with the original path.
                        serviceImagePreview.src = 'images/<?php echo htmlspecialchars($current_service['image'] ?? ''); ?>';
                    }
                });
            }
        });
    </script>
</body>
</html>
