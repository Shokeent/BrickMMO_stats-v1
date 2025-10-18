<?php

include('../includes/config.php');
include('../includes/connect.php');
include('../includes/functions.php');
include('auth.php');

check_admin_login();

if(isset($_GET['logout'])) {
    admin_logout();
}

$total_assets_query = "SELECT COUNT(*) as count FROM assets";
$total_assets_result = mysqli_query($connect, $total_assets_query);
$total_assets = mysqli_fetch_assoc($total_assets_result)['count'];

$total_stats_query = "SELECT COUNT(*) as count FROM stats";
$total_stats_result = mysqli_query($connect, $total_stats_query);
$total_stats = mysqli_fetch_assoc($total_stats_result)['count'];

$today_stats_query = "SELECT COUNT(*) as count FROM stats WHERE DATE(viewed_at) = CURDATE()";
$today_stats_result = mysqli_query($connect, $today_stats_query);
$today_stats = mysqli_fetch_assoc($today_stats_result)['count'];

$unique_visitors_query = "SELECT COUNT(DISTINCT ip_address) as count FROM stats";
$unique_visitors_result = mysqli_query($connect, $unique_visitors_query);
$unique_visitors = mysqli_fetch_assoc($unique_visitors_result)['count'];

$top_assets_query = "SELECT a.name, a.url, COUNT(s.id) as view_count 
                     FROM assets a 
                     LEFT JOIN stats s ON a.id = s.asset_id 
                     GROUP BY a.id 
                     ORDER BY view_count DESC 
                     LIMIT 5";
$top_assets = mysqli_query($connect, $top_assets_query);

$recent_activity_query = "SELECT s.*, a.name as asset_name 
                         FROM stats s 
                         LEFT JOIN assets a ON s.asset_id = a.id 
                         ORDER BY s.viewed_at DESC 
                         LIMIT 8";
$recent_activity = mysqli_query($connect, $recent_activity_query);

define('PAGE_TITLE', 'Dashboard');
include('../includes/header.php');

?>

<div class="w3-bar orange-theme">
    <a href="index.php" class="w3-bar-item w3-button w3-border-right" style="background-color: rgba(255,255,255,0.2);">Dashboard</a>
    <a href="assets.php" class="w3-bar-item w3-button w3-border-right">Assets</a>
    <a href="stats.php" class="w3-bar-item w3-button w3-border-right">Statistics</a>
    <a href="users.php" class="w3-bar-item w3-button w3-border-right">Users</a>
    <a href="?logout=1" class="w3-bar-item w3-button w3-right">Logout</a>
</div>

<h2 class="page-title">Dashboard</h2>
<p style="color: #6c757d; margin-bottom: 30px;">Welcome back, <strong><?=$_SESSION['admin_username']?></strong>! Here's your analytics overview.</p>

<!-- Main Statistics Cards -->
<div class="w3-row-padding w3-margin-bottom">
    <div class="w3-quarter">
        <div class="stats-card">
            <div class="stats-number"><?= number_format($total_stats) ?></div>
            <div class="stats-label">Total Page Views</div>
            <div class="stats-description">All-time analytics tracking</div>
        </div>
    </div>
    
    <div class="w3-quarter">
        <div class="stats-card">
            <div class="stats-number"><?= number_format($total_assets) ?></div>
            <div class="stats-label">Active Assets</div>
            <div class="stats-description">Tracking endpoints</div>
        </div>
    </div>
    
    <div class="w3-quarter">
        <div class="stats-card">
            <div class="stats-number"><?= number_format($unique_visitors) ?></div>
            <div class="stats-label">Unique Visitors</div>
            <div class="stats-description">Distinct IP addresses</div>
        </div>
    </div>
    
    <div class="w3-quarter">
        <div class="stats-card">
            <div class="stats-number"><?= number_format($today_stats) ?></div>
            <div class="stats-label">Today's Views</div>
            <div class="stats-description">Views since midnight</div>
        </div>
    </div>
</div>

<!-- Content Sections -->
<div class="w3-row-padding">
    <div class="w3-half">
        <div class="w3-card w3-white w3-margin-bottom">
            <div class="section-header">Top Performing Assets</div>
            <div class="w3-container">
                <table class="w3-table w3-striped">
                    <thead>
                        <tr>
                            <th>Asset Name</th>
                            <th>URL</th>
                            <th>Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($top_assets) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($top_assets)): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                                    <td>
                                        <?php if($row['url']): ?>
                                            <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" style="color: var(--primary-color);">
                                                <?= htmlspecialchars(strlen($row['url']) > 25 ? substr($row['url'], 0, 25) . '...' : $row['url']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #6c757d;">No URL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span style="color: var(--primary-color); font-weight: 600;"><?= number_format($row['view_count']) ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: #6c757d; padding: 20px;">No assets found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="w3-half">
        <div class="w3-card w3-white w3-margin-bottom">
            <div class="section-header">Recent Activity</div>
            <div class="w3-container">
                <table class="w3-table w3-striped">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Browser</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($recent_activity) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($recent_activity)): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['asset_name'] ?: 'Unknown Asset') ?></strong></td>
                                    <td style="color: #6c757d;"><?= htmlspecialchars($row['browser'] ?: 'Unknown') ?></td>
                                    <td style="color: #6c757d; font-size: 12px;">
                                        <?= date('M j, g:i A', strtotime($row['viewed_at'])) ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: #6c757d; padding: 20px;">No recent activity</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="w3-card w3-white w3-margin-bottom">
    <div class="section-header">Quick Actions</div>
    <div class="w3-container" style="padding: 25px;">
        <div class="w3-row-padding">
            <div class="w3-third">
                <a href="assets.php" class="w3-button orange-theme w3-block w3-round">
                    Manage Assets
                </a>
                <p style="text-align: center; color: #6c757d; margin: 15px 0 0 0;">Add or edit tracking assets</p>
            </div>
            <div class="w3-third">
                <a href="stats.php" class="w3-button orange-theme w3-block w3-round">
                    View Analytics
                </a>
                <p style="text-align: center; color: #6c757d; margin: 15px 0 0 0;">Detailed statistics and reports</p>
            </div>
            <div class="w3-third">
                <a href="users.php" class="w3-button orange-theme w3-block w3-round">
                    User Management
                </a>
                <p style="text-align: center; color: #6c757d; margin: 15px 0 0 0;">Manage admin users</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>