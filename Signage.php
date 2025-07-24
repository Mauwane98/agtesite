<?php
// Load Services from CSV for navigation dropdown
$services = [];
if (file_exists('data/services.csv') && ($handle = fopen("data/services.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $services[] = ['name' => $data[0], 'description' => $data[1], 'image' => $data[2]];
    }
    fclose($handle);
}
// Load general website images from JSON
$site_images = [];
if (file_exists('data/images.json')) {
    $site_images = json_decode(file_get_contents("data/images.json"), true);
}
// Load Signage Types from JSON (NEW DATA FILE)
$signage_types = [];
if (file_exists('data/signage_types.json')) {
    $signage_types = json_decode(file_get_contents("data/signage_types.json"), true);
    if ($signage_types === null) { // Handle JSON decode errors
        $signage_types = [];
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AGTE - Always Good Trading Enterprise - Signage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/signage.css">
</head>
<body class="bg-white">

    <!-- Header Section -->
    <header class="shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-3">
                <div>
                    <a href="./index.php">
                        <img src="images/<?php echo htmlspecialchars($site_images['logo'] ?? 'AGTE%20Logo.png'); ?>" alt="AGTE Logo" class="h-16 md:h-20" onerror="this.onerror=null;this.src='https://placehold.co/218x119/cccccc/000000?text=AGTE+Logo';">
                    </a>
                </div>
                <div class="hidden lg:block">
                     <img src="images/<?php echo htmlspecialchars($site_images['banner'] ?? 'AGTE%20Banner.png'); ?>" alt="AGTE Banner" class="h-16" onerror="this.onerror=null;this.src='https://placehold.co/694x106/cccccc/000000?text=AGTE+Banner';">
                </div>
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center">
                     <ul class="flex space-x-6 text-lg">
                        <li><a href="./index.php" class="nav-link">Home</a></li>
                        <li><a href="./About_Us.php" class="nav-link">About Us</a></li>
                        <li class="relative nav-item-services">
                            <a href="./Services.php" class="nav-link active">Services</a>
                            <ul class="absolute dropdown-menu dropdown-hidden mt-2 w-48 overflow-hidden">
                                <?php
                                // Define the mapping for service names to their respective page files
                                $service_pages = [
                                    'construction' => './Construction.php',
                                    'signage' => './Signage.php',
                                    'professional services' => './Professional-Services.php',
                                    'ppe' => './PPE.php'
                                ];
                                foreach ($services as $service):
                                    $service_name_lower = strtolower($service['name']);
                                    $link_href = $service_pages[$service_name_lower] ?? 'service.php?name=' . urlencode($service_name_lower);
                                ?>
                                <li><a href="<?php echo htmlspecialchars($link_href); ?>" class="sub-nav-link <?php echo (strtolower($service['name']) == 'signage') ? 'active' : ''; ?>"><?php echo htmlspecialchars($service['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li><a href="./Contact_Us.php" class="nav-link">Contact Us</a></li>
                    </ul>
                </nav>
                 <!-- Mobile Menu Toggle -->
                 <div class="md:hidden">
                    <button id="menu-toggle" class="text-white focus:outline-none flex items-center space-x-2 px-2">
                        <span class="font-bold text-lg">Menu</span>
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="text-white">
        <ul class="flex flex-col space-y-2 p-4 text-lg">
            <li><a href="./index.php" class="block py-2 px-4 rounded hover:bg-gray-700">Home</a></li>
            <li><a href="./About_Us.php" class="block py-2 px-4 rounded hover:bg-gray-700">About Us</a></li>
            <li><a href="./Services.php" class="block py-2 px-4 rounded hover:bg-gray-700 active">Services</a></li>
            <?php
            // Define the mapping for service names to their respective page files
            $service_pages = [
                'construction' => './Construction.php',
                'signage' => './Signage.php',
                'professional services' => './Professional-Services.php',
                'ppe' => './PPE.php'
            ];
            foreach ($services as $service):
                $service_name_lower = strtolower($service['name']);
                $link_href = $service_pages[$service_name_lower] ?? 'service.php?name=' . urlencode($service_name_lower);
            ?>
            <li><a href="<?php echo htmlspecialchars($link_href); ?>" class="block py-2 px-4 rounded hover:bg-gray-700 <?php echo (strtolower($service['name']) == 'signage') ? 'active' : ''; ?>"><?php echo htmlspecialchars($service['name']); ?></a></li>
            <?php endforeach; ?>
            <li><a href="./Contact_Us.php" class="block py-2 px-4 rounded hover:bg-gray-700">Contact Us</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <div class="relative text-center">
            <img src="images/<?php echo htmlspecialchars($site_images['hero_image'] ?? 'AGTE%20Head%20Img.jpg'); ?>" alt="AGTE Header Image" class="w-full h-64 md:h-80 object-cover" onerror="this.onerror=null;this.src='https://placehold.co/1070x357/cccccc/000000?text=Signage';">
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <h1 class="text-white text-4xl md:text-6xl font-bold">Signage Solutions</h1>
            </div>
        </div>

        <!-- Intro Section -->
        <div class="py-16 bg-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="page-section-title text-black">Comprehensive Signage Services</h2>
                <p class="max-w-3xl mx-auto text-lg text-gray-700">
                    At AGTE, we provide high-quality, durable, and effective signage solutions for a variety of needs. From construction sites to corporate branding, our signs are designed to be visible, professional, and long-lasting.
                </p>
            </div>
        </div>

        <!-- Types of Signage Section (Dynamically loaded) -->
        <div class="py-16 bg-gray-100">
            <div class="container mx-auto px-4">
                <h2 class="page-section-title text-black">Types of Signage We Offer</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php foreach ($signage_types as $type): ?>
                    <div class="signage-card">
                        <img src="images/<?php echo htmlspecialchars($site_images[$type['image_key']] ?? 'placehold.co/400x300/cccccc/000000?text=Image'); ?>" alt="<?php echo htmlspecialchars($type['name']); ?>" class="signage-image" onerror="this.onerror=null;this.src='https://placehold.co/400x300/cccccc/000000?text=<?php echo urlencode($type['name']); ?>';">
                        <div class="p-6">
                            <h3 class="signage-card-title"><?php echo htmlspecialchars($type['name']); ?></h3>
                            <p class="signage-card-text"><?php echo htmlspecialchars($type['description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Our Team in Action Section -->
        <div class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="page-section-title text-black">Our Team in Action</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="team-gallery-item">
                        <img src="images/<?php echo htmlspecialchars($site_images['signage_team_1'] ?? 'employee_sign_1.jpeg'); ?>" alt="Employee with a safety sign" class="team-image" onerror="this.onerror=null;this.src='https://placehold.co/400x400/cccccc/000000?text=Team+Member+1';">
                    </div>
                    <div class="team-gallery-item">
                        <img src="images/<?php echo htmlspecialchars($site_images['signage_team_2'] ?? 'employee_sign_2.jpeg'); ?>" alt="Employee installing a large banner" class="team-image" onerror="this.onerror=null;this.src='https://placehold.co/400x400/cccccc/000000?text=Team+Member+2';">
                    </div>
                    <div class="team-gallery-item">
                        <img src="images/<?php echo htmlspecialchars($site_images['signage_team_3'] ?? 'employee_sign_3.jpeg'); ?>" alt="Two employees with construction site signage" class="team-image" onerror="this.onerror=null;this.src='https://placehold.co/400x400/cccccc/000000?text=Team+Member+3';">
                    </div>
                    <div class="team-gallery-item">
                        <img src="images/<?php echo htmlspecialchars($site_images['signage_team_4'] ?? 'employee_sign_4.jpeg'); ?>" alt="Employee applying a decal to a window" class="team-image" onerror="this.onerror=null;this.src='https://placehold.co/400x400/cccccc/000000?text=Team+Member+4';">
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-200 text-black">
        <div class="container mx-auto px-4 py-6 text-center">
            <p>&copy; Copyright AGTE Always Good Trading Enterprise 2025 (All Rights Reserved)</p>
            <div class="mt-4">
                <a href="./admin-login.html" class="text-sm text-gray-500 hover:text-gray-700">Admin Login</a>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
