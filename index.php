<?php

header('Location: admin/login.php');
exit;

?>

<h1>BrickMMO Stats Dashboard</h1>

<?php
if ($connect) {
    $total_assets = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM assets"))['count'] ?? 0;
    $total_views = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM stats"))['count'] ?? 0;
    $today_views = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM stats WHERE DATE(viewed_at) = CURDATE()"))['count'] ?? 0;
    $total_users = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM users"))['count'] ?? 0;
    $week_views = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM stats WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['count'] ?? 0;
    
    $top_assets_query = "
        SELECT a.name, COUNT(s.id) as view_count
        FROM assets a
        LEFT JOIN stats s ON a.id = s.asset_id
        GROUP BY a.id, a.name
        ORDER BY view_count DESC
        LIMIT 5
    ";
    $top_assets_result = mysqli_query($connect, $top_assets_query);
?>

<h2 class="orange-text">Overview Statistics</h2>
<div class="w3-row-padding w3-margin-bottom">
    <div class="w3-col l3 m6 s12">
        <div class="w3-card w3-container w3-center w3-padding">
            <h3 class="orange-text" style="font-size: 36px;"><?=$total_views?></h3>
            <p><strong>Total Page Views</strong></p>
            <p class="w3-text-grey">All-time tracking data</p>
        </div>
    </div>
    
    <div class="w3-col l3 m6 s12">
        <div class="w3-card w3-container w3-center w3-padding">
            <h3 class="orange-text" style="font-size: 36px;"><?=$total_assets?></h3>
            <p><strong>Active Assets</strong></p>
            <p class="w3-text-grey">Tracking endpoints</p>
        </div>
    </div>
    
    <div class="w3-col l3 m6 s12">
        <div class="w3-card w3-container w3-center w3-padding">
            <h3 class="orange-text" style="font-size: 36px;"><?=$today_views?></h3>
            <p><strong>Today's Views</strong></p>
            <p class="w3-text-grey">Views in last 24 hours</p>
        </div>
    </div>
    
    <div class="w3-col l3 m6 s12">
        <div class="w3-card w3-container w3-center w3-padding">
            <h3 class="orange-text" style="font-size: 36px;"><?=$week_views?></h3>
            <p><strong>This Week</strong></p>
            <p class="w3-text-grey">Views in last 7 days</p>
        </div>
    </div>
</div>

<div class="w3-row-padding">
    <div class="w3-col l6">
        <div class="w3-card">
            <header class="w3-container orange-theme">
                <h3>System Status</h3>
            </header>
            <div class="w3-container w3-padding">
                <div class="w3-row w3-margin-bottom">
                    <div class="w3-col s8">Database Connection</div>
                    <div class="w3-col s4 orange-text"><strong>✓ Active</strong></div>
                </div>
                <div class="w3-row w3-margin-bottom">
                    <div class="w3-col s8">Total Users</div>
                    <div class="w3-col s4"><strong><?=$total_users?></strong></div>
                </div>
                <div class="w3-row">
                    <div class="w3-col s8">Tracking Status</div>
                    <div class="w3-col s4 orange-text"><strong>✓ Online</strong></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="w3-col l6">
        <div class="w3-card">
            <header class="w3-container orange-theme">
                <h3>Top Assets</h3>
            </header>
            <div class="w3-container w3-padding">
                <?php while($asset = mysqli_fetch_assoc($top_assets_result)): ?>
                <div class="w3-row w3-margin-bottom">
                    <div class="w3-col s8"><?=$asset['name']?></div>
                    <div class="w3-col s4"><strong><?=$asset['view_count']?> views</strong></div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<div class="w3-margin-top">
    <h2 class="orange-text">Demo Websites</h2>
    <p>Explore our demo websites to see BrickMMO Stats tracking in action:</p>
    <div class="w3-row-padding w3-margin-bottom">
        <div class="w3-col l6 m12">
            <div class="w3-card">
                <header class="w3-container orange-theme">
                    <h3>Demo 1 - Basic Tracking</h3>
                </header>
                <div class="w3-container w3-padding">
                    <p>Simple website demonstrating basic page view tracking with clean design.</p>
                    <a href="demo/" class="w3-button orange-theme w3-round" target="_blank">Visit Demo 1</a>
                </div>
            </div>
        </div>
        
        <div class="w3-col l6 m12">
            <div class="w3-card">
                <header class="w3-container orange-theme">
                    <h3>Demo 2 - E-commerce</h3>
                </header>
                <div class="w3-container w3-padding">
                    <p>Advanced tracking demo with multiple pages and e-commerce features.</p>
                    <a href="demo2/" class="w3-button orange-theme w3-round" target="_blank">Visit Demo 2</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="w3-margin-top">
    <h2 class="orange-text">Admin Access</h2>
    <p><a href="admin/" class="w3-button orange-theme w3-round">Access Admin Panel</a></p>
</div>

<div class="w3-margin-top">
    <h2 class="orange-text">About</h2>
    <p>Developed by Tarun Shokeen for HTTP 5310 Capstone Project at Humber College.</p>
</div>

<?php
} else {
    echo '<div class="w3-card w3-pale-red w3-border-red">';
    echo '<div class="w3-container">';
    echo '<h3>Database Connection Failed</h3>';
    echo '<p>Unable to connect to the database. Please check your configuration.</p>';
    echo '</div>';
    echo '</div>';
}
?>

</body>
</html>