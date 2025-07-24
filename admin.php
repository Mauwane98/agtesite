<?php
// Start the session.
session_start();

// Security check: Redirect to login if not logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

// --- Load existing data ---

// Load Services from CSV
$services = [];
if (file_exists('data/services.csv') && ($handle = fopen("data/services.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $services[] = ['name' => $data[0], 'description' => $data[1], 'image' => $data[2]];
    }
    fclose($handle);
}

// Load Contact Info from JSON
$contact_info = [];
if (file_exists('data/contact_info.json')) {
    $contact_info = json_decode(file_get_contents("data/contact_info.json"), true);
}

// Load general website images from JSON
$site_images = [];
if (file_exists('data/images.json')) {
    $site_images = json_decode(file_get_contents("data/images.json"), true);
}

// Load Projects from CSV
$projects = [];
if (file_exists('data/projects.csv') && ($handle = fopen("data/projects.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $projects[] = ['name' => $data[0], 'client' => $data[1], 'status' => $data[2]];
    }
    fclose($handle);
}

// Load Clients from CSV
$clients = [];
if (file_exists('data/clients.csv') && ($handle = fopen("data/clients.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $clients[] = ['name' => $data[0], 'logo' => $data[1]];
    }
    fclose($handle);
}

// Load Signage Types from JSON (NEW DATA FILE)
$signage_types = [];
if (file_exists('data/signage_types.json')) {
    $signage_types = json_decode(file_get_contents("data/signage_types.json"), true);
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AGTE - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <style>
        /* Custom CSS for responsive sidebar and main content */
        @media (min-width: 1024px) { /* Equivalent to Tailwind's lg breakpoint */
            #sidebar {
                transform: translateX(0) !important; /* Ensure sidebar is always visible on large screens */
                position: static !important; /* Remove fixed positioning on large screens */
                display: flex !important; /* Ensure it's flex on large screens */
            }
            #sidebar-overlay {
                display: none !important; /* Hide overlay on large screens */
            }
            /* Adjust main content to make space for the sidebar on large screens */
            .main-content-area {
                margin-left: 16rem; /* w-64 = 16rem */
            }
        }
        /* Ensure images within the admin forms are contained and have consistent sizing */
        .form-image-preview {
            max-width: 100%;
            height: 120px; /* Fixed height for better alignment in forms */
            object-fit: contain; /* Use 'contain' to ensure full image is visible, 'cover' might crop */
            border-radius: 0.375rem; /* rounded-md */
            margin-bottom: 0.5rem; /* mb-2 */
            background-color: #e2e8f0; /* Light background for transparent images */
            padding: 5px; /* Small padding for visual separation */
        }
        /* Adjust alignment for file inputs and labels */
        .form-group-image {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Align items to the start */
        }
        /* Ensure file input takes full width */
        .form-input-file {
            width: 100%; /* Ensure file input takes full width of its container */
            padding: 0.75rem 1rem;
            border-radius: 6px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            color: #2d3748;
            transition: border-color 0.3s ease;
        }
        /* Message box styling */
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
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex flex-col lg:flex-row h-screen">
        <!-- Sidebar Overlay (for mobile) -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-gray-800 text-white flex-shrink-0 flex-col hidden lg:flex fixed lg:static inset-y-0 left-0 z-50 transform -translate-x-full transition-transform duration-200 ease-in-out shadow-lg lg:shadow-none">
            <div class="p-4 text-center">
                <h1 class="text-2xl font-bold text-yellow-400">AGTE Admin</h1>
            </div>
            <nav class="flex-1 overflow-y-auto">
                <ul>
                    <li><a href="admin.php" class="admin-nav-link" data-target-section="dashboard">Dashboard</a></li>
                    <li><a href="#projects" class="admin-nav-link" data-target-section="projects">Manage Projects</a></li>
                    <li><a href="#clients" class="admin-nav-link" data-target-section="clients">Manage Clients</a></li>
                    <li><a href="#services" class="admin-nav-link" data-target-section="services">Manage Services</a></li>
                    <li><a href="#signage-types" class="admin-nav-link" data-target-section="signage-types">Manage Signage Types</a></li> <!-- NEW LINK -->
                    <li><a href="#contact" class="admin-nav-link" data-target-section="contact">Manage Contact Us</a></li>
                    <li><a href="#images" class="admin-nav-link" data-target-section="images">Manage Website Images</a></li>
                </ul>
            </nav>
            <div class="p-4">
                <a href="./index.php" class="admin-nav-link border-t border-gray-700 block text-center">Back to Website</a>
                <a href="logout.php" class="admin-nav-link border-t border-gray-700 block text-center">Logout</a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-64">
            <!-- Header -->
            <header class="bg-white shadow-md p-4 flex justify-between items-center lg:pl-8">
                <h2 class="text-2xl font-semibold text-gray-800">Dashboard Overview</h2>
                <div class="flex items-center">
                    <span class="text-gray-600 mr-4 hidden md:block">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <!-- Mobile Menu Toggle Button -->
                    <button id="sidebar-toggle" class="lg:hidden text-gray-800 focus:outline-none p-2 rounded-md hover:bg-gray-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
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

                <!-- Manage Projects Section -->
                <div id="projects" class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-700">Manage Projects</h3>
                    </div>
                    <div class="p-4 border-t">
                        <form action="manage_projects.php#projects" method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label for="project-name" class="form-label">Project Name</label>
                                    <input type="text" name="project_name" class="form-input" required>
                                </div>
                                <div>
                                    <label for="project-client" class="form-label">Client</label>
                                    <input type="text" name="project_client" class="form-input" required>
                                </div>
                                <div>
                                    <label for="project-status" class="form-label">Status</label>
                                    <select name="project_status" class="form-input">
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Pending">Pending</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="action-button">Add Project</button>
                            </div>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="table-header">Project Name</th>
                                    <th class="table-header">Client</th>
                                    <th class="table-header">Status</th>
                                    <th class="table-header">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <?php foreach ($projects as $index => $project): ?>
                                <tr>
                                    <td class="table-cell"><?php echo htmlspecialchars($project['name']); ?></td>
                                    <td class="table-cell"><?php echo htmlspecialchars($project['client']); ?></td>
                                    <td class="table-cell"><span class="status-chip <?php echo strtolower(str_replace(' ', '-', $project['status'])); ?>"><?php echo htmlspecialchars($project['status']); ?></span></td>
                                    <td class="table-cell">
                                        <a href="manage_projects.php?action=delete&id=<?php echo $index; ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Manage Clients Section -->
                <div id="clients" class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-700">Manage Clients</h3>
                    </div>
                    <div class="p-4 border-t">
                        <form action="manage_clients.php#clients" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="client-name" class="form-label">Client Name</label>
                                    <input type="text" name="client_name" class="form-input" required>
                                </div>
                                <div>
                                    <label for="client-logo" class="form-label">Client Logo</label>
                                    <input type="file" name="client_logo" class="form-input-file" required>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="action-button">Add Client</button>
                            </div>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="table-header">Client Name</th>
                                    <th class="table-header">Logo</th>
                                    <th class="table-header">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <?php foreach ($clients as $index => $client): ?>
                                <tr>
                                    <td class="table-cell"><?php echo htmlspecialchars($client['name']); ?></td>
                                    <td class="table-cell"><img src="images/<?php echo htmlspecialchars($client['logo']); ?>" class="h-12"></td>
                                    <td class="table-cell">
                                        <a href="manage_clients.php?action=delete&id=<?php echo $index; ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Manage Services Section -->
                <div id="services" class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-700">Manage Services</h3>
                    </div>
                    <div class="p-4 border-t">
                        <form action="manage_services.php#services" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="service-name" class="form-label">Service Name</label>
                                    <input type="text" name="service_name" class="form-input" placeholder="e.g., Construction" required>
                                </div>
                                <div>
                                    <label for="service-description" class="form-label">Description</label>
                                    <input type="text" name="service_description" class="form-input" placeholder="Brief description" required>
                                </div>
                                <div>
                                    <label for="service-image" class="form-label">Service Image</label>
                                    <input type="file" name="service_image" class="form-input-file" required>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="action-button">Add Service</button>
                            </div>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="table-header">Service Name</th>
                                    <th class="table-header">Description</th>
                                    <th class="table-header">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <?php foreach ($services as $index => $service): ?>
                                <tr>
                                    <td class="table-cell"><?php echo htmlspecialchars($service['name']); ?></td>
                                    <td class="table-cell"><?php echo htmlspecialchars($service['description']); ?></td>
                                    <td class="table-cell">
                                        <a href="edit_service.php?id=<?php echo $index; ?>" class="text-blue-600 hover:underline">Edit</a> |
                                        <a href="manage_services.php?action=delete&id=<?php echo $index; ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this service?');">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Manage Signage Types Section (NEW SECTION) -->
                <div id="signage-types" class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-700">Manage Signage Types</h3>
                    </div>
                    <div class="p-4 border-t">
                        <h4 class="font-semibold text-lg text-gray-700 mb-4">Add New Signage Type</h4>
                        <form action="manage_signage_types.php#signage-types" method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="signage-type-name" class="form-label">Signage Type Name</label>
                                    <input type="text" name="name" id="signage-type-name" class="form-input" required>
                                </div>
                                <div>
                                    <label for="signage-type-description" class="form-label">Description</label>
                                    <input type="text" name="description" id="signage-type-description" class="form-input" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="signage-type-image-key" class="form-label">Associated Image Key (from Manage Website Images)</label>
                                    <input type="text" name="image_key" id="signage-type-image-key" class="form-input" placeholder="e.g., signage_gov" required>
                                    <p class="text-sm text-gray-500 mt-1">This key must match an image key in the 'Manage Website Images' section above.</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="action-button">Add Signage Type</button>
                            </div>
                        </form>

                        <h4 class="font-semibold text-lg text-gray-700 mt-8 mb-4 border-t pt-4">Existing Signage Types</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="table-header">Name</th>
                                        <th class="table-header">Description</th>
                                        <th class="table-header">Image Key</th>
                                        <th class="table-header">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <?php foreach ($signage_types as $index => $type): ?>
                                    <tr>
                                        <td class="table-cell"><?php echo htmlspecialchars($type['name']); ?></td>
                                        <td class="table-cell"><?php echo htmlspecialchars($type['description']); ?></td>
                                        <td class="table-cell"><?php echo htmlspecialchars($type['image_key']); ?></td>
                                        <td class="table-cell">
                                            <a href="edit_signage_type.php?id=<?php echo $index; ?>" class="text-blue-600 hover:underline">Edit</a> |
                                            <a href="manage_signage_types.php?action=delete&id=<?php echo $index; ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this signage type?');">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Manage Contact Info Section -->
                <div id="contact" class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-700">Manage Contact Information</h3>
                    </div>
                    <div class="p-4 border-t">
                        <form action="manage_contact.php#contact" method="POST" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group-image">
                                    <h4 class="font-semibold text-lg mb-2">Susan Mauwane</h4>
                                    <label class="form-label">Current Photo</label>
                                    <img id="susan-photo-preview" src="images/<?php echo htmlspecialchars($contact_info['susan']['photo'] ?? ''); ?>" class="form-image-preview w-32 h-32">
                                    <label for="susan-photo" class="form-label">Upload New Photo</label>
                                    <input type="file" name="susan_photo" id="susan-photo" class="form-input-file mb-2">
                                    
                                    <label for="susan-cell-phone" class="form-label">Cell Phone</label>
                                    <input type="text" name="susan_cell_phone" class="form-input mb-2" value="<?php echo htmlspecialchars($contact_info['susan']['cell_phone'] ?? ''); ?>">
                                    
                                    <label for="susan-telephone" class="form-label">Telephone</label>
                                    <input type="text" name="susan_telephone" class="form-input mb-2" value="<?php echo htmlspecialchars($contact_info['susan']['telephone'] ?? ''); ?>">
                                    
                                    <label for="susan-email" class="form-label">Email</label>
                                    <input type="email" name="susan_email" class="form-input mb-2" value="<?php echo htmlspecialchars($contact_info['susan']['email'] ?? ''); ?>">

                                    <label for="susan-fax" class="form-label">Fax</label>
                                    <input type="text" name="susan_fax" class="form-input" value="<?php echo htmlspecialchars($contact_info['susan']['fax'] ?? ''); ?>">
                                </div>
                                <div class="form-group-image">
                                    <h4 class="font-semibold text-lg mb-2">Danny Mauwane</h4>
                                    <label class="form-label">Current Photo</label>
                                    <img id="danny-photo-preview" src="images/<?php echo htmlspecialchars($contact_info['danny']['photo'] ?? ''); ?>" class="form-image-preview w-32 h-32">
                                    <label for="danny-photo" class="form-label">New Photo</label>
                                    <input type="file" name="danny_photo" id="danny-photo" class="form-input-file mb-2">
                                    
                                    <label for="danny-cell-phone" class="form-label">Cell Phone</label>
                                    <input type="text" name="danny_cell_phone" class="form-input mb-2" value="<?php echo htmlspecialchars($contact_info['danny']['cell_phone'] ?? ''); ?>">
                                    
                                    <label for="danny-email" class="form-label">Email</label>
                                    <input type="email" name="danny_email" class="form-input" value="<?php echo htmlspecialchars($contact_info['danny']['email'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit" class="action-button">Update Contact Info</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Manage Website Images Section -->
                <div id="images" class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-700">Manage Website Images</h3>
                    </div>
                    <div class="p-4 border-t">
                        <!-- Global Images Forms -->
                        <h4 class="col-span-full font-semibold text-lg text-gray-700 mb-4 border-b pb-2">Global Website Images (Header/Hero)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-center mb-6">
                            <!-- Logo Form -->
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="logo">
                                <label class="form-label">Current Logo</label>
                                <img id="logo-preview" src="images/<?php echo htmlspecialchars($site_images['logo'] ?? ''); ?>" class="form-image-preview w-32">
                                <label for="logo" class="form-label">Upload New Logo</label>
                                <input type="file" name="image_file" id="logo" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Logo</button>
                                </div>
                            </form>
                            <!-- Banner Form -->
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="banner">
                                <label class="form-label">Current Banner</label>
                                <img id="banner-preview" src="images/<?php echo htmlspecialchars($site_images['banner'] ?? ''); ?>" class="form-image-preview w-full">
                                <label for="banner" class="form-label">Upload New Banner</label>
                                <input type="file" name="image_file" id="banner" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Banner</button>
                                </div>
                            </form>
                            <!-- Hero Image Form -->
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image md:col-span-2 lg:col-span-1 border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="hero_image">
                                <label class="form-label">Current Hero Image</label>
                                <img id="hero_image-preview" src="images/<?php echo htmlspecialchars($site_images['hero_image'] ?? ''); ?>" class="form-image-preview w-full h-48">
                                <label for="hero_image" class="form-label">Upload New Hero Image</label>
                                <input type="file" name="image_file" id="hero_image" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Hero Image</button>
                                </div>
                            </form>
                        </div>

                        <!-- PPE Page Images Forms -->
                        <h4 class="col-span-full font-semibold text-lg text-gray-700 mb-4 border-b pb-2">PPE Page Images</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center mb-6">
                            <!-- PPE Product 1 Form -->
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="ppe_product_showcase_1">
                                <label class="form-label">Current PPE Products Image 1</label>
                                <img id="ppe_product_showcase_1-preview" src="images/<?php echo htmlspecialchars($site_images['ppe_product_showcase_1'] ?? ''); ?>" class="form-image-preview w-full">
                                <label for="ppe_product_showcase_1" class="form-label">Upload New PPE Products Image 1</label>
                                <input type="file" name="image_file" id="ppe_product_showcase_1" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Image 1</button>
                                </div>
                            </form>
                            <!-- PPE Product 2 Form -->
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="ppe_product_showcase_2">
                                <label class="form-label">Current PPE Products Image 2 (Collage)</label>
                                <img id="ppe_product_showcase_2-preview" src="images/<?php echo htmlspecialchars($site_images['ppe_product_showcase_2'] ?? ''); ?>" class="form-image-preview w-full">
                                <label for="ppe_product_showcase_2" class="form-label">Upload New PPE Products Image 2</label>
                                <input type="file" name="image_file" id="ppe_product_showcase_2" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Image 2</button>
                                </div>
                            </form>
                        </div>

                        <!-- Professional Services Page Images Forms -->
                        <h4 class="col-span-full font-semibold text-lg text-gray-700 mb-4 border-b pb-2">Professional Services Page Images</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center mb-6">
                            <!-- Safety Picture Form -->
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="professional_services_safety_pic">
                                <label class="form-label">Current Safety Picture</label>
                                <img id="professional_services_safety_pic-preview" src="images/<?php echo htmlspecialchars($site_images['professional_services_safety_pic'] ?? ''); ?>" class="form-image-preview w-full">
                                <label for="professional_services_safety_pic" class="form-label">Upload New Safety Picture</label>
                                <input type="file" name="image_file" id="professional_services_safety_pic" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Safety Picture</button>
                                </div>
                            </form>
                            <!-- Crossfire Logo Form -->
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="professional_services_crossfire_logo">
                                <label class="form-label">Current Crossfire Logo</label>
                                <img id="professional_services_crossfire_logo-preview" src="images/<?php echo htmlspecialchars($site_images['professional_services_crossfire_logo'] ?? ''); ?>" class="form-image-preview w-full">
                                <label for="professional_services_crossfire_logo" class="form-label">Upload New Crossfire Logo</label>
                                <input type="file" name="image_file" id="professional_services_crossfire_logo" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Crossfire Logo</button>
                                </div>
                            </form>
                        </div>

                        <!-- Construction Project Gallery Images Forms -->
                        <h4 class="col-span-full font-semibold text-lg text-gray-700 mb-4 border-b pb-2">Construction Project Gallery Images</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-start mb-6">
                            <?php for ($i = 1; $i <= 6; $i++): ?>
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="construction_gallery_<?php echo $i; ?>">
                                <label class="form-label">Current Image <?php echo $i; ?></label>
                                <img id="construction_gallery_<?php echo $i; ?>-preview" src="images/<?php echo htmlspecialchars($site_images['construction_gallery_' . $i] ?? ''); ?>" class="form-image-preview w-full h-32">
                                <label for="construction_gallery_<?php echo $i; ?>" class="form-label">Upload New Image <?php echo $i; ?></label>
                                <input type="file" name="image_file" id="construction_gallery_<?php echo $i; ?>" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Image <?php echo $i; ?></button>
                                </div>
                            </form>
                            <?php endfor; ?>
                        </div>

                        <!-- Signage Types Images Forms (Moved and Renamed for clarity) -->
                        <h4 class="col-span-full font-semibold text-lg text-gray-700 mb-4 border-b pb-2">Signage Type Showcase Images</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-start mb-6">
                            <!-- Government & Corporate Signage Form -->
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="signage_gov">
                                <label class="form-label">Current Government Signage</label>
                                <img id="signage_gov-preview" src="images/<?php echo htmlspecialchars($site_images['signage_gov'] ?? ''); ?>" class="form-image-preview w-full h-32">
                                <label for="signage_gov" class="form-label">Upload New Gov. Signage</label>
                                <input type="file" name="image_file" id="signage_gov" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Gov. Signage</button>
                                </div>
                            </form>
                            <!-- Construction Signage Form -->
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="signage_construction">
                                <label class="form-label">Current Construction Signage</label>
                                <img id="signage_construction-preview" src="images/<?php echo htmlspecialchars($site_images['signage_construction'] ?? ''); ?>" class="form-image-preview w-full h-32">
                                <label for="signage_construction" class="form-label">Upload New Const. Signage</label>
                                <input type="file" name="image_file" id="signage_construction" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Const. Signage</button>
                                </div>
                            </form>
                            <!-- Safety Signage Form -->
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="signage_safety">
                                <label class="form-label">Current Safety Signage</label>
                                <img id="signage_safety-preview" src="images/<?php echo htmlspecialchars($site_images['signage_safety'] ?? ''); ?>" class="form-image-preview w-full h-32">
                                <label for="signage_safety" class="form-label">Upload New Safety Signage</label>
                                <input type="file" name="image_file" id="signage_safety" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Safety Signage</button>
                                </div>
                            </form>
                            <!-- Business Signage Form -->
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="signage_business">
                                <label class="form-label">Current Business Signage</label>
                                <img id="signage_business-preview" src="images/<?php echo htmlspecialchars($site_images['signage_business'] ?? ''); ?>" class="form-image-preview w-full h-32">
                                <label for="signage_business" class="form-label">Upload New Business Signage</label>
                                <input type="file" name="image_file" id="signage_business" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Business Signage</button>
                                </div>
                            </form>
                        </div>

                        <!-- Signage Team Images Forms -->
                        <h4 class="col-span-full font-semibold text-lg text-gray-700 mb-4 border-b pb-2">Signage Team Images</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-start mb-6 pb-6">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                            <form action="manage_single_image.php#images" method="POST" enctype="multipart/form-data" class="form-group-image border p-4 rounded-md shadow-sm">
                                <input type="hidden" name="image_key" value="signage_team_<?php echo $i; ?>">
                                <label class="form-label">Current Team Image <?php echo $i; ?></label>
                                <img id="signage_team_<?php echo $i; ?>-preview" src="images/<?php echo htmlspecialchars($site_images['signage_team_' . $i] ?? ''); ?>" class="form-image-preview w-full h-48">
                                <label for="signage_team_<?php echo $i; ?>" class="form-label">Upload New Team Image <?php echo $i; ?></label>
                                <input type="file" name="image_file" id="signage_team_<?php echo $i; ?>" class="form-input-file">
                                <div class="mt-4 text-right w-full">
                                    <button type="submit" class="action-button">Update Team Image <?php echo $i; ?></button>
                                </div>
                            </form>
                            <?php endfor; ?>
                        </div>

                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        // JavaScript for mobile sidebar toggle, message handling, live image preview, and active nav links
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const messageBox = document.getElementById('message-box');
            const navLinks = document.querySelectorAll('.admin-nav-link');
            const sections = document.querySelectorAll('main > div[id]'); // All main content sections

            // Function to open sidebar
            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('flex'); // Ensure it's flex when open
                sidebar.classList.remove('hidden'); // Ensure it's not hidden
                sidebarOverlay.classList.remove('hidden');
            }

            // Function to close sidebar
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('flex'); // Remove flex when closed
                sidebar.classList.add('hidden'); // Hide it
                sidebarOverlay.classList.add('hidden');
            }

            // Toggle sidebar on button click
            sidebarToggle.addEventListener('click', function() {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });

            // Close sidebar when clicking on overlay
            sidebarOverlay.addEventListener('click', function() {
                closeSidebar();
            });

            // Close sidebar on resize if it's open and screen becomes large
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) { // lg breakpoint
                    closeSidebar(); // Ensure it closes on mobile view before becoming static
                }
            });

            // Initial state check for desktop: ensure sidebar is visible on large screens
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('hidden');
                sidebar.classList.add('flex');
                sidebar.classList.remove('-translate-x-full'); // Ensure it's not translated
                sidebar.style.position = 'static'; // Ensure static positioning
            }

            // Auto-hide message box after a few seconds
            if (messageBox) {
                setTimeout(() => {
                    messageBox.style.display = 'none';
                }, 5000); // Hide after 5 seconds
            }

            // Live Image Preview Functionality
            function setupImagePreview(inputId, previewId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);

                if (input && preview) {
                    input.addEventListener('change', function() {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                            };
                            reader.readAsDataURL(this.files[0]);
                        } else {
                            // If no file selected, revert to current image (if available)
                            // This requires the PHP to render the current image path in the src attribute initially
                            // which is already done. So, if input is cleared, it will show the original on next load.
                            // For immediate visual revert, you'd need to store original src.
                            // For now, we'll just let the original src attribute handle it.
                        }
                    });
                }
            }

            // Setup image previews for all relevant inputs
            setupImagePreview('susan-photo', 'susan-photo-preview');
            setupImagePreview('danny-photo', 'danny-photo-preview');
            setupImagePreview('logo', 'logo-preview');
            setupImagePreview('banner', 'banner-preview');
            setupImagePreview('hero_image', 'hero_image-preview');
            setupImagePreview('ppe_product_showcase_1', 'ppe_product_showcase_1-preview');
            setupImagePreview('ppe_product_showcase_2', 'ppe_product_showcase_2-preview');
            setupImagePreview('professional_services_safety_pic', 'professional_services_safety_pic-preview');
            setupImagePreview('professional_services_crossfire_logo', 'professional_services_crossfire_logo-preview');
            
            // Loop for gallery and team images
            for (let i = 1; i <= 6; i++) {
                setupImagePreview(`construction_gallery_${i}`, `construction_gallery_${i}-preview`);
            }
            for (let i = 1; i <= 4; i++) {
                setupImagePreview(`signage_team_${i}`, `signage_team_${i}-preview`);
            }
            // Loop for Signage Types Images
            setupImagePreview('signage_gov', 'signage_gov-preview');
            setupImagePreview('signage_construction', 'signage_construction-preview');
            setupImagePreview('signage_safety', 'signage_safety-preview');
            setupImagePreview('signage_business', 'signage_business-preview');


            // Active Sidebar Link and Scroll to Section Logic
            function setActiveLink(hash) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                });

                if (hash) {
                    const activeLink = document.querySelector(`.admin-nav-link[href="${hash}"]`);
                    if (activeLink) {
                        activeLink.classList.add('active');
                    }
                } else {
                    // If no hash, default to Dashboard
                    document.querySelector('.admin-nav-link[href="admin.php"]').classList.add('active');
                }
            }

            // Scroll to section after form submission if hash is present
            if (window.location.hash) {
                const targetElement = document.querySelector(window.location.hash);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                    setActiveLink(window.location.hash);
                }
            } else {
                setActiveLink(null); // Set Dashboard as active on initial load if no hash
            }

            // Update active link on scroll
            window.addEventListener('scroll', () => {
                let currentActiveSection = '';
                sections.forEach(section => {
                    // Get the top position of the section relative to the viewport
                    const rect = section.getBoundingClientRect();
                    // Check if the section is currently in the viewport (or close to the top)
                    if (rect.top <= window.innerHeight / 2 && rect.bottom >= window.innerHeight / 2) {
                        currentActiveSection = '#' + section.id;
                    }
                });
                // Only update if a new active section is found, to prevent constant re-setting
                if (currentActiveSection && document.querySelector(`.admin-nav-link[href="${currentActiveSection}"]`) && !document.querySelector(`.admin-nav-link[href="${currentActiveSection}"]`).classList.contains('active')) {
                    setActiveLink(currentActiveSection);
                } else if (!currentActiveSection && !document.querySelector('.admin-nav-link[href="admin.php"]').classList.contains('active')) {
                    // If no section is active (e.g., scrolled to top), activate Dashboard
                    setActiveLink(null);
                }
            });

            // Update active link on hash change (e.g., direct link or back/forward buttons)
            window.addEventListener('hashchange', () => {
                setActiveLink(window.location.hash);
            });
        });
    </script>
</body>
</html>
