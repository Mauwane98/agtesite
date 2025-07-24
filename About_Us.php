<?php
// Load Services from CSV to display on the home page
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
    <title>AGTE - Always Good Trading Enterprise</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/about.css"> <!-- Added link to about.css -->
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
                        <li><a href="./About_Us.php" class="nav-link active">About Us</a></li>
                        <li class="relative nav-item-services">
                            <a href="./Services.php" class="nav-link">Services</a>
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
                                <li><a href="<?php echo htmlspecialchars($link_href); ?>" class="sub-nav-link"><?php echo htmlspecialchars($service['name']); ?></a></li>
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
            <li><a href="./About_Us.php" class="block py-2 px-4 rounded hover:bg-gray-700 active">About Us</a></li>
            <li><a href="./Services.php" class="block py-2 px-4 rounded hover:bg-gray-700">Services</a></li>
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
            <li><a href="<?php echo htmlspecialchars($link_href); ?>" class="block py-2 px-4 rounded hover:bg-gray-700"><?php echo htmlspecialchars($service['name']); ?></a></li>
            <?php endforeach; ?>
            <li><a href="./Contact_Us.php" class="block py-2 px-4 rounded hover:bg-gray-700">Contact Us</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <div class="relative text-center">
            <img src="images/<?php echo htmlspecialchars($site_images['hero_image'] ?? 'AGTE%20Head%20Img.jpg'); ?>" alt="Header Background" class="w-full h-64 md:h-80 object-cover" onerror="this.onerror=null;this.src='https://placehold.co/1070x222/cccccc/000000?text=Header+Image';">
            <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center flex-col p-4">
                 <h1 class="text-white text-3xl md:text-5xl font-bold drop-shadow-lg">About Us</h1>
            </div>
        </div>
        
        <div class="bg-white">
            <div class="container mx-auto px-4 py-16">
                <!-- Our Story Section -->
                <div class="text-center mb-16">
                    <div class="relative inline-block mb-4 rounded-lg shadow-md" style="background-color: #DBFB00;">
                         <h2 class="text-black text-2xl md:text-3xl font-bold px-6 py-3">Our Story</h2>
                    </div>
                    <p class="max-w-4xl mx-auto text-lg text-black leading-relaxed">
                        Always Good Trading Enterprise (AGTE) is a dynamic company specializing in a diverse range of services including General Building, Plumbing Services, Signage, Occupational Health and Safety, Supply of Personal Protective Equipment, and Civil Works. Established with a commitment to excellence, AGTE prides itself on delivering high-quality and affordable solutions to both the public and private sectors. Our dedication to client satisfaction and adherence to industry standards set us apart.
                    </p>
                </div>

                <!-- Mission & Vision Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                    <div class="bg-gray-100 p-8 rounded-lg shadow-md">
                        <h3 class="text-2xl font-bold text-yellow-500 mb-4">Our Mission</h3>
                        <p class="text-gray-700 leading-relaxed">
                            To be the leading provider of comprehensive construction and safety solutions, consistently exceeding client expectations through innovation, quality, and unwavering commitment to safety and efficiency. We aim to build lasting relationships based on trust and exceptional service delivery.
                        </p>
                    </div>
                    <div class="bg-gray-100 p-8 rounded-lg shadow-md">
                        <h3 class="text-2xl font-bold text-yellow-500 mb-4">Our Vision</h3>
                        <p class="text-gray-700 leading-relaxed">
                            To create sustainable value for our clients, employees, and communities by setting industry benchmarks in service quality, safety, and operational excellence. We envision a future where AGTE is synonymous with reliability and integrity in every project we undertake.
                        </p>
                    </div>
                </div>

                <!-- Values Section -->
                <div class="text-center">
                    <div class="relative inline-block mb-4 rounded-lg shadow-md" style="background-color: #DBFB00;">
                         <h2 class="text-black text-2xl md:text-3xl font-bold px-6 py-3">Our Values</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8">
                        <div class="value-card">
                            <h3 class="value-title">Quality</h3>
                            <p class="value-text">We are committed to delivering services and products of the highest standard, ensuring durability and reliability.</p>
                        </div>
                        <div class="value-card">
                            <h3 class="value-title">Integrity</h3>
                            <p class="value-text">We operate with honesty, transparency, and strong moral principles in all our business dealings.</p>
                        </div>
                        <div class="value-card">
                            <h3 class="value-title">Safety</h3>
                            <p class="value-text">The well-being of our team and clients is paramount. We adhere to the strictest safety protocols.</p>
                        </div>
                        <div class="value-card">
                            <h3 class="value-title">Client Satisfaction</h3>
                            <p class="value-text">Our success is measured by our clients' satisfaction. We strive to understand and exceed their expectations.</p>
                        </div>
                        <div class="value-card">
                            <h3 class="value-title">Innovation</h3>
                            <p class="value-text">We embrace new technologies and methods to provide efficient and effective solutions.</p>
                        </div>
                        <div class="value-card">
                            <h3 class="value-title">Teamwork</h3>
                            <p class="value-text">We foster a collaborative environment where every team member contributes to our collective success.</p>
                        </div>
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
