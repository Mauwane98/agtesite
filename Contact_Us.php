<?php
// Load Contact Info from JSON
$contact_info = [];
if (file_exists('data/contact_info.json')) {
    $contact_info = json_decode(file_get_contents("data/contact_info.json"), true);
} else {
    // Provide a default empty structure if file doesn't exist
    $contact_info = [
        "susan" => ["cell_phone" => "", "telephone" => "", "email" => "", "fax" => "", "photo" => ""],
        "danny" => ["cell_phone" => "", "email" => "", "photo" => ""]
    ];
}

// Load Services from CSV for navigation dropdown
$services = [];
if (file_exists('data/services.csv') && ($handle = fopen("data/services.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $services[] = ['name' => $data[0], 'description' => $data[1], 'image' => $data[2]];
    }
    fclose($handle);
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
    <title>AGTE - Always Good Trading Enterprise - Contact Us</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/contact.css">
</head>
<body class="bg-white">
    <!-- Header -->
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
                            <a href="./Services.php" class="nav-link">Services</a>
                            <ul class="absolute dropdown-menu dropdown-hidden mt-2 w-48 overflow-hidden">
                                <?php
                                // Define the mapping for service names to their respective page files
                                $service_pages = [
                                    'construction' => './Construction.php',
                                    'signage' => './Signage.php',
                                    'professional services' => './Professional-Services.php',
                                    'personal protective equipment' => './PPE.php'
                                ];
                                foreach ($services as $service):
                                    $service_name_lower = strtolower($service['name']);
                                    $link_href = $service_pages[$service_name_lower] ?? 'service.php?name=' . urlencode($service_name_lower);
                                ?>
                                <li><a href="<?php echo htmlspecialchars($link_href); ?>" class="sub-nav-link"><?php echo htmlspecialchars($service['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li><a href="./Contact_Us.php" class="nav-link active">Contact Us</a></li>
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
            <li><a href="./Services.php" class="block py-2 px-4 rounded hover:bg-gray-700">Services</a></li>
            <?php
            // Define the mapping for service names to their respective page files
            $service_pages = [
                'construction' => './Construction.php',
                'signage' => './Signage.php',
                'professional services' => './Professional-Services.php',
                'personal protective equipment' => './PPE.php'
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
            <img src="images/<?php echo htmlspecialchars($site_images['hero_image'] ?? 'AGTE%20Head%20Img.jpg'); ?>" alt="AGTE Header Image" class="w-full h-64 md:h-80 object-cover" onerror="this.onerror=null;this.src='https://placehold.co/1070x357/cccccc/000000?text=Contact+Us';">
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <h1 class="text-white text-4xl md:text-6xl font-bold">Contact Us</h1>
            </div>
        </div>

        <div class="py-16 bg-gray-100">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-2 gap-12">
                    <!-- Contact People -->
                    <div class="contact-card">
                        <h2 class="contact-title">Key Contacts</h2>
                        <div class="grid sm:grid-cols-2 gap-8">
                            <div>
                                <img src="images/<?php echo htmlspecialchars($contact_info['susan']['photo'] ?? 'IMG_1205.JPG'); ?>" alt="Susan Mauwane" class="contact-image">
                                <h3 class="font-bold text-xl mt-4">Susan Mauwane</h3>
                                <p class="text-gray-600">CEO</p>
                                <?php if (!empty($contact_info['susan']['cell_phone'])): ?>
                                <p class="mt-2">C: <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $contact_info['susan']['cell_phone'])); ?>" class="contact-link"><?php echo htmlspecialchars($contact_info['susan']['cell_phone']); ?></a></p>
                                <?php endif; ?>
                                <?php if (!empty($contact_info['susan']['telephone'])): ?>
                                <p>T: <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $contact_info['susan']['telephone'])); ?>" class="contact-link"><?php echo htmlspecialchars($contact_info['susan']['telephone']); ?></a></p>
                                <?php endif; ?>
                                <?php if (!empty($contact_info['susan']['email'])): ?>
                                <p>E: <a href="mailto:<?php echo htmlspecialchars($contact_info['susan']['email']); ?>" class="contact-link"><?php echo htmlspecialchars($contact_info['susan']['email']); ?></a></p>
                                <?php endif; ?>
                                <?php if (!empty($contact_info['susan']['fax'])): ?>
                                <p>F: <?php echo htmlspecialchars($contact_info['susan']['fax']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <img src="images/<?php echo htmlspecialchars($contact_info['danny']['photo'] ?? 'IMG_1217.JPG'); ?>" alt="Danny Mauwane" class="contact-image">
                                <h3 class="font-bold text-xl mt-4">Danny Mauwane</h3>
                                <p class="text-gray-600">Operations Manager</p>
                                <?php if (!empty($contact_info['danny']['cell_phone'])): ?>
                                <p class="mt-2">C: <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $contact_info['danny']['cell_phone'])); ?>" class="contact-link"><?php echo htmlspecialchars($contact_info['danny']['cell_phone']); ?></a></p>
                                <?php endif; ?>
                                <?php if (!empty($contact_info['danny']['email'])): ?>
                                <p>E: <a href="mailto:<?php echo htmlspecialchars($contact_info['danny']['email']); ?>" class="contact-link"><?php echo htmlspecialchars($contact_info['danny']['email']); ?></a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- Office Locations -->
                    <div class="contact-card">
                        <h2 class="contact-title">Our Offices</h2>
                        <div class="space-y-8">
                            <div>
                                <h3 class="font-bold text-xl mb-2 text-yellow-500">Head Office</h3>
                                <p>141 Fountain View<br>14th Road<br>Noordwyk, Midrand<br>Gauteng</p>
                                <div class="map-container">
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3587.1543656878753!2d28.125764574645252!3d-25.96297727722592!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e956fccc23bb3cf%3A0xf58bcd7c5422647!2s14th%20Rd%2C%20Noordwyk%2C%20Midrand%2C%201687!5e0!3m2!1sen!2sza!4v1737369117380!5m2=1en!2sza" class="map-iframe" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl mb-2 text-yellow-500">Branch Office</h3>
                                <p>75 Block B<br>Dikebu, Moema Section<br>Moretele<br>North-West</p>
                                <div class="map-container">
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14438.962284231968!2d27.9507914630084!3d-25.21196994063542!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1ebfacff195a86cb%3A0x5db6cbf75e15d78b!2sMoema%2C%200504!5e0!3m2!1sen!2sza!4v1737369463044!5m2=1en!2sza" class="map-iframe" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer (same as before) -->
    <footer class="bg-gray-200 text-black">
        <div class="container mx-auto px-4 py-6 text-center">
            <p>&copy; Copyright AGTE Always Good Trading Enterprise 2025 (All Rights Reserved)</p>
            <img src="images/Copywrite%20design.png" alt="Design" class="mx-auto mt-2 h-6" onerror="this.onerror=null;this.src='https://placehold.co/174x25/cccccc/000000?text=Design';">
            <div class="mt-4">
                <a href="./admin-login.html" class="text-sm text-gray-500 hover:text-gray-700">Admin Login</a>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
