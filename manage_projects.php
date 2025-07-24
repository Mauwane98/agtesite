<?php
session_start(); // Start the session at the very beginning

// Security check: Redirect to login if not logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$projects_file = 'data/projects.csv';

// Load existing projects
$projects = [];
if (file_exists($projects_file) && ($handle = fopen($projects_file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $projects[] = ['name' => $data[0], 'client' => $data[1], 'status' => $data[2]];
    }
    fclose($handle);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $project_name = $_POST['project_name'] ?? '';
        $project_client = $_POST['project_client'] ?? '';
        $project_status = $_POST['project_status'] ?? '';

        if (!empty($project_name) && !empty($project_client) && !empty($project_status)) {
            $projects[] = ['name' => $project_name, 'client' => $project_client, 'status' => $project_status];
            
            // Write updated projects back to CSV
            if (($handle = fopen($projects_file, "w")) !== FALSE) {
                foreach ($projects as $project) {
                    fputcsv($handle, $project);
                }
                fclose($handle);
                $_SESSION['message'] = "Project added successfully!";
            } else {
                $_SESSION['error'] = "Failed to open projects file for writing.";
            }
        } else {
            $_SESSION['error'] = "All project fields are required.";
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? null;

    if ($action === 'delete' && $id !== null) {
        if (isset($projects[$id])) {
            array_splice($projects, $id, 1); // Remove the project
            
            // Write updated projects back to CSV
            if (($handle = fopen($projects_file, "w")) !== FALSE) {
                foreach ($projects as $project) {
                    fputcsv($handle, $project);
                }
                fclose($handle);
                $_SESSION['message'] = "Project deleted successfully!";
            } else {
                $_SESSION['error'] = "Failed to open projects file for writing.";
            }
        } else {
            $_SESSION['error'] = "Project not found.";
        }
    }
}

// Redirect back to admin page, specifically to the projects section
header('Location: admin.php#projects');
exit;
?>
