<?php
session_start();

// Security check: Redirect to login if not logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$signage_types_file = 'data/signage_types.json';
$signage_type_id = $_GET['id'] ?? null; // Get the signage type ID from the URL

$signage_types = [];
// Load existing signage types
if (file_exists($signage_types_file)) {
    $json_content = file_get_contents($signage_types_file);
    $signage_types = json_decode($json_content, true);
    if ($signage_types === null && json_last_error() !== JSON_ERROR_NONE) {
        $_SESSION['error'] = "Error reading signage_types.json: " . json_last_error_msg();
        $signage_types = [];
    }
} else {
    $_SESSION['error'] = "Signage types data file not found.";
    header('Location: admin.php#signage-types');
    exit;
}

$current_signage_type = null;
if ($signage_type_id !== null && isset($signage_types[$signage_type_id])) {
    $current_signage_type = $signage_types[$signage_type_id];
} else {
    $_SESSION['error'] = "Signage type not found for editing.";
    header('Location: admin.php#signage-types');
    exit;
}

// Handle form submission for updating the signage type
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['name'] ?? '');
    $new_description = trim($_POST['description'] ?? '');
    $new_image_key = trim($_POST['image_key'] ?? '');

    if (!empty($new_name) && !empty($new_description) && !empty($new_image_key)) {
        // Update the signage type in the array
        $signage_types[$signage_type_id] = [
            'name' => $new_name,
            'description' => $new_description,
            'image_key' => $new_image_key
        ];

        // Write the updated signage types back to JSON
        if (file_put_contents($signage_types_file, json_encode($signage_types, JSON_PRETTY_PRINT))) {
            $_SESSION['message'] = "Signage type '{$new_name}' updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to save signage type data. Check data/signage_types.json permissions.";
            error_log("Failed to write to signage_types.json during edit: " . $signage_types_file);
        }
    } else {
        $_SESSION['error'] = "All fields (Name, Description, Image Key) are required to update a signage type.";
    }

    // Redirect back to admin page, specifically to the signage types section
    header('Location: admin.php#signage-types');
    exit;
}

// Load general website images from JSON for header (needed for consistent header/footer)
$site_images = [];
if (file_exists('data/images.json')) {
    $site_images = json_decode(file_get_contents("data/images.json"), true);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AGTE - Edit Signage Type</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        /* Re-using some admin styles for consistency */
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            color: #2d3748;
            transition: border-color 0.3s ease;
        }
        .form-label {
            display: block;
            color: #4a5568;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .action-button {
            background-color: #DBFB00;
            color: #2d3748;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .action-button:hover {
            background-color: #c2e000;
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
            <h2 class="text-2xl font-semibold text-gray-800">Edit Signage Type</h2>
            <div>
                <a href="admin.php#signage-types" class="action-button">Back to Admin</a>
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
                <form action="edit_signage_type.php?id=<?php echo htmlspecialchars($signage_type_id); ?>" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="form-label">Signage Type Name</label>
                            <input type="text" name="name" id="name" class="form-input" value="<?php echo htmlspecialchars($current_signage_type['name'] ?? ''); ?>" required>
                        </div>
                        <div>
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-input h-32" required><?php echo htmlspecialchars($current_signage_type['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label for="image-key" class="form-label">Associated Image Key (from Manage Website Images)</label>
                            <input type="text" name="image_key" id="image-key" class="form-input" value="<?php echo htmlspecialchars($current_signage_type['image_key'] ?? ''); ?>" required>
                            <p class="text-sm text-gray-500 mt-1">This key must match an image key in the 'Manage Website Images' section of the admin dashboard.</p>
                        </div>
                    </div>
                    <div class="mt-6 text-right">
                        <button type="submit" class="action-button">Update Signage Type</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageBox = document.getElementById('message-box');
            if (messageBox) {
                setTimeout(() => {
                    messageBox.style.display = 'none';
                }, 5000); // Hide after 5 seconds
            }
        });
    </script>
</body>
</html>
