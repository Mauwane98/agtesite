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
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AGTE - Always Good Trading Enterprise - PPE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/ppe.css">
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
                                    'personal protective equipment' => './PPE.php' // Corrected key to match full lowercase name
                                ];
                                foreach ($services as $service):
                                    $service_name_lower = strtolower($service['name']);
                                    $link_href = $service_pages[$service_name_lower] ?? 'service.php?name=' . urlencode($service_name_lower);
                                ?>
                                <li><a href="<?php echo htmlspecialchars($link_href); ?>" class="sub-nav-link <?php echo (strtolower($service['name']) == 'personal protective equipment') ? 'active' : ''; ?>"><?php echo htmlspecialchars($service['name']); ?></a></li>
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
                'personal protective equipment' => './PPE.php' // Corrected key to match full lowercase name
            ];
            foreach ($services as $service):
                $service_name_lower = strtolower($service['name']);
                $link_href = $service_pages[$service_name_lower] ?? 'service.php?name=' . urlencode($service_name_lower);
            ?>
            <li><a href="<?php echo htmlspecialchars($link_href); ?>" class="block py-2 px-4 rounded hover:bg-gray-700 <?php echo (strtolower($service['name']) == 'personal protective equipment') ? 'active' : ''; ?>"><?php echo htmlspecialchars($service['name']); ?></a></li>
            <?php endforeach; ?>
            <li><a href="./Contact_Us.php" class="block py-2 px-4 rounded hover:bg-gray-700">Contact Us</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <div class="relative text-center">
            <img src="images/<?php echo htmlspecialchars($site_images['hero_image'] ?? 'AGTE%20Head%20Img.jpg'); ?>" alt="AGTE Header Image" class="w-full h-64 md:h-80 object-cover" onerror="this.onerror=null;this.src='https://placehold.co/1070x357/cccccc/000000?text=PPE';">
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <h1 class="text-white text-4xl md:text-6xl font-bold">Personal Protective Equipment</h1>
            </div>
        </div>

        <!-- PPE Content Section -->
        <div class="py-16 bg-gray-800 text-white">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="ppe-title">Quality PPE Supplier</h2>
                        <p class="mb-6">AGTE is a trusted supplier of a wide range of quality Personal Protective Equipment. We are committed to ensuring the safety of your workforce with reliable and durable products.</p>
                        <ul class="ppe-list">
                            <li>Conti Suits</li>
                            <li>Boiler Suits</li>
                            <li>Safety Shoes</li>
                            <li>Head Protection</li>
                            <li>Eye Protection</li>
                            <li>Ear Protection</li>
                            <li>Respiratory and Welding Masks</li>
                            <li>Reflectors</li>
                        </ul>
                    </div>
                    <div>
                        <img src="images/<?php echo htmlspecialchars($site_images['ppe_product_showcase_1'] ?? 'ppe%20products2.jpg'); ?>" alt="PPE Products" class="rounded-lg shadow-lg w-full" onerror="this.onerror=null;this.src='https://placehold.co/690x425/cccccc/000000?text=PPE+Products';">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Our Products Gallery -->
        <div class="py-16 bg-gray-100">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-black mb-8">Our Products</h2>
                <img src="images/<?php echo htmlspecialchars($site_images['ppe_product_showcase_2'] ?? 'PPE%20Products.jpg'); ?>" alt="A collage of PPE products" class="w-full rounded-lg shadow-lg" onerror="this.onerror=null;this.src='https://placehold.co/1060x297/cccccc/000000?text=Our+Products';">
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
