<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>


<?php

include('includes/connect.php');
var_dump($connect);
include('includes/config.php');
include('includes/functions.php');

define('PAGE_TITLE', '');

include('includes/header.php');

?>

<h1>LEGO&reg; Parts Directory</h1>

<main style="flex-wrap: wrap; gap: 16px; align-items: stretch;">

 <div class="w3-card w3-padding w3-margin">
        <h2 class="w3-green w3-padding">Database Connection Test</h2>
        <?php
        if ($connect) {
            echo '<p class="w3-text-green">✅ Database connection successful!</p>';
            echo '<p><strong>Connected to:</strong> ' . DB_DATABASE . '</p>';
            
            // testing if tables exist
            $tables_check = [];
            $table_names = ['assets', 'stats', 'users'];
            
            foreach ($table_names as $table) {
                $result = mysqli_query($connect, "SHOW TABLES LIKE '$table'");
                if (mysqli_num_rows($result) > 0) {
                    $tables_check[$table] = 'Exists';
                } else {
                    $tables_check[$table] = 'Missing';
                }
            }
            
            echo '<h3>Database Tables Status:</h3>';
            echo '<ul>';
            foreach ($tables_check as $table => $status) {
                echo '<li><strong>' . $table . ':</strong> ' . $status . '</li>';
            }
            echo '</ul>';
            
        } else {
            echo '<p class="w3-text-red">Database connection failed!</p>';
            echo '<p>Error: ' . mysqli_connect_error() . '</p>';
        }
        ?>
    </div>

    <div class="w3-card w3-padding w3-margin">
        <h2 class="w3-blue w3-padding">Project Information</h2>
        <p><strong>Developer:</strong> Tarun Shokeen</p>
        <p><strong>Project:</strong> BrickMMO Analytics Application</p>
        <p><strong>Course:</strong> HTTP 5310 Capstone</p>
        <p><strong>Professor:</strong> Adam Thomas (codeadamca)</p>
    </div>

    <div class="w3-card w3-padding w3-margin">
        <h2 class="w3-indigo w3-padding">Features to Build</h2>
        <ul>
            <li>JavaScript tracking script for page views</li>
            <li>Admin panel for asset management</li>
            <li>Statistics dashboard with charts</li>
            <li>Export functionality (CSV)</li>
            <li>User authentication system</li>
        </ul>
    </div>

    <div class="w3-card w3-padding w3-margin">
        <h2 class="w3-purple w3-padding">Quick Links</h2>
        <p><a href="admin/" class="w3-button w3-green">Admin Panel</a> (Coming Soon)</p>
        <p><a href="tracker.js" class="w3-button w3-blue" target="_blank">View Tracking Script</a></p>
    </div>

    
</main>

<?php include('includes/footer.php'); ?>