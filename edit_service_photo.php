<?php
session_start();

// Security check: ensure the user is logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$service_id = $_GET['id'];
$services = [];
if (($handle = fopen("data/services.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $services[] = $data;
    }
    fclose($handle);
}

// Get the specific service to edit
$service_to_edit = $services[$service_id];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Update Service Photo - AGTE Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="bg-gray-100 font-sans p-8">
    <div class="max-w-lg mx-auto bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Update Photo for "<?php echo htmlspecialchars($service_to_edit[0]); ?>"</h1>
        
        <div class="mb-4">
            <label class="form-label">Current Photo</label>
            <img src="images/<?php echo htmlspecialchars($service_to_edit[2]); ?>" alt="Current service photo" class="w-48 h-48 object-cover rounded-md border">
        </div>

        <form action="update_photo.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
            
            <div class="mb-6">
                <label for="new_service_image" class="form-label">Upload New Photo</label>
                <input type="file" name="new_service_image" class="form-input-file" required>
            </div>
            
            <div>
                <button type="submit" class="action-button">Update Photo</button>
                <a href="admin.php#services" class="ml-4 text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
