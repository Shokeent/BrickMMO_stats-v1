<?php

include('../includes/config.php');
include('../includes/connect.php');
include('../includes/functions.php');
include('auth.php');

check_admin_login();

if(isset($_GET['logout'])) {
    admin_logout();
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="stats_export_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Date/Time', 'Asset Name', 'Page URL', 'IP Address', 'Browser', 'OS', 'Referrer']);
    
    $export_query = "
        SELECT s.*, a.name as asset_name 
        FROM stats s 
        LEFT JOIN assets a ON s.asset_id = a.id 
        ORDER BY s.viewed_at DESC
    ";
    $export_result = mysqli_query($connect, $export_query);
    
    while ($row = mysqli_fetch_assoc($export_result)) {
        fputcsv($output, [
            $row['viewed_at'],
            $row['asset_name'] ?: 'Unknown',
            $row['url'],
            $row['ip_address'],
            $row['browser'] ?: 'Unknown',
            $row['os'] ?: 'Unknown',
            $row['referrer'] ?: 'Direct'
        ]);
    }
    
    fclose($output);
    exit;
}


$selected_asset = $_GET['asset_id'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';


$where_conditions = [];
$where_params = [];

if ($selected_asset) {
    $where_conditions[] = "s.asset_id = ?";
    $where_params[] = $selected_asset;
}

if ($date_from) {
    $where_conditions[] = "DATE(s.viewed_at) >= ?";
    $where_params[] = $date_from;
}

if ($date_to) {
    $where_conditions[] = "DATE(s.viewed_at) <= ?";
    $where_params[] = $date_to;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";


$total_views = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM stats"))['count'];
$today_views = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM stats WHERE DATE(viewed_at) = CURDATE()"))['count'];
$week_views = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM stats WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['count'];
$total_assets = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM assets"))['count'];

$filtered_stats_query = "
    SELECT COUNT(*) as filtered_count 
    FROM stats s 
    LEFT JOIN assets a ON s.asset_id = a.id 
    $where_clause
";

if (!empty($where_params)) {
    $stmt = mysqli_prepare($connect, $filtered_stats_query);
    if (!empty($where_params)) {
        $types = str_repeat('s', count($where_params));
        mysqli_stmt_bind_param($stmt, $types, ...$where_params);
    }
    mysqli_stmt_execute($stmt);
    $filtered_count = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['filtered_count'];
} else {
    $filtered_count = mysqli_fetch_assoc(mysqli_query($connect, $filtered_stats_query))['filtered_count'];
}

$detailed_stats_query = "
    SELECT s.*, a.name as asset_name 
    FROM stats s 
    LEFT JOIN assets a ON s.asset_id = a.id 
    $where_clause
    ORDER BY s.viewed_at DESC 
    LIMIT 50
";

if (!empty($where_params)) {
    $stmt = mysqli_prepare($connect, $detailed_stats_query);
    if (!empty($where_params)) {
        $types = str_repeat('s', count($where_params));
        mysqli_stmt_bind_param($stmt, $types, ...$where_params);
    }
    mysqli_stmt_execute($stmt);
    $detailed_stats_result = mysqli_stmt_get_result($stmt);
} else {
    $detailed_stats_result = mysqli_query($connect, $detailed_stats_query);
}

$browser_stats_query = "
    SELECT browser, COUNT(*) as count 
    FROM stats s 
    LEFT JOIN assets a ON s.asset_id = a.id 
    $where_clause
    GROUP BY browser 
    ORDER BY count DESC 
    LIMIT 10
";

if (!empty($where_params)) {
    $stmt = mysqli_prepare($connect, $browser_stats_query);
    if (!empty($where_params)) {
        $types = str_repeat('s', count($where_params));
        mysqli_stmt_bind_param($stmt, $types, ...$where_params);
    }
    mysqli_stmt_execute($stmt);
    $browser_stats_result = mysqli_stmt_get_result($stmt);
} else {
    $browser_stats_result = mysqli_query($connect, $browser_stats_query);
}

// Get OS breakdown
$os_stats_query = "
    SELECT os, COUNT(*) as count 
    FROM stats s 
    LEFT JOIN assets a ON s.asset_id = a.id 
    $where_clause
    GROUP BY os 
    ORDER BY count DESC 
    LIMIT 10
";

if (!empty($where_params)) {
    $stmt = mysqli_prepare($connect, $os_stats_query);
    if (!empty($where_params)) {
        $types = str_repeat('s', count($where_params));
        mysqli_stmt_bind_param($stmt, $types, ...$where_params);
    }
    mysqli_stmt_execute($stmt);
    $os_stats_result = mysqli_stmt_get_result($stmt);
} else {
    $os_stats_result = mysqli_query($connect, $os_stats_query);
}

$top_assets_query = "
    SELECT a.name, a.id, COUNT(s.id) as view_count
    FROM assets a
    LEFT JOIN stats s ON a.id = s.asset_id
    GROUP BY a.id, a.name
    ORDER BY view_count DESC
    LIMIT 10
";
$top_assets_result = mysqli_query($connect, $top_assets_query);

$assets_query = "SELECT id, name FROM assets ORDER BY name";
$assets_result = mysqli_query($connect, $assets_query);

define('PAGE_TITLE', 'Statistics');
include('../includes/header.php');

?>

<div class="w3-bar orange-theme">
    <a href="index.php" class="w3-bar-item w3-button w3-border-right">Dashboard</a>
    <a href="assets.php" class="w3-bar-item w3-button w3-border-right">Assets</a>
    <a href="stats.php" class="w3-bar-item w3-button w3-border-right" style="background-color: rgba(255,255,255,0.2);">Statistics</a>
    <a href="users.php" class="w3-bar-item w3-button w3-border-right">Users</a>
    <a href="?logout=1" class="w3-bar-item w3-button w3-right">Logout</a>
</div>

<h2 class="page-title">Statistics Dashboard</h2>

<!-- Filters Section -->
<div class="w3-card w3-margin-bottom">
    <header class="w3-container orange-theme">
        <h3>Filters & Export</h3>
    </header>
    <div class="w3-container w3-padding">
        <form method="GET" class="w3-row-padding">
            <div class="w3-col l3 m6 s12">
                <label class="w3-text-black"><b>Asset:</b></label>
                <select class="w3-select w3-border" name="asset_id">
                    <option value="">All Assets</option>
                    <?php while($asset = mysqli_fetch_assoc($assets_result)): ?>
                        <option value="<?=$asset['id']?>" <?=$selected_asset == $asset['id'] ? 'selected' : ''?>>
                            <?=htmlspecialchars($asset['name'])?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="w3-col l3 m6 s12">
                <label class="w3-text-black"><b>From Date:</b></label>
                <input class="w3-input w3-border" type="date" name="date_from" value="<?=$date_from?>">
            </div>
            
            <div class="w3-col l3 m6 s12">
                <label class="w3-text-black"><b>To Date:</b></label>
                <input class="w3-input w3-border" type="date" name="date_to" value="<?=$date_to?>">
            </div>
            
            <div class="w3-col l3 m6 s12">
                <label class="w3-text-black"><b>Actions:</b></label>
                <div>
                    <button type="submit" class="w3-button orange-theme w3-margin-right">Filter</button>
                    <a href="?export=csv<?=!empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : ''?>" 
                       class="w3-button w3-green">Export CSV</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Performance Metrics -->
<h2 class="orange-text">Performance Metrics</h2>
<div class="w3-row-padding w3-margin-bottom">
    <div class="w3-col l3 m6 s12">
        <div class="w3-card w3-container w3-center w3-padding">
            <h3 class="orange-text" style="font-size: 36px;"><?=$total_views?></h3>
            <p><strong>Total Views</strong></p>
            <p class="w3-text-grey">All-time statistics</p>
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
    
    <div class="w3-col l3 m6 s12">
        <div class="w3-card w3-container w3-center w3-padding">
            <h3 class="orange-text" style="font-size: 36px;"><?=$filtered_count?></h3>
            <p><strong>Filtered Results</strong></p>
            <p class="w3-text-grey">Matching current filters</p>
        </div>
    </div>
</div>

<!-- Charts and Breakdowns -->
<div class="w3-row-padding w3-margin-bottom">
    <div class="w3-col l4">
        <div class="w3-card">
            <header class="w3-container orange-theme">
                <h3>Browser Breakdown</h3>
            </header>
            <div class="w3-container w3-padding">
                <?php if(mysqli_num_rows($browser_stats_result) > 0): ?>
                    <?php while($browser = mysqli_fetch_assoc($browser_stats_result)): ?>
                        <?php $percentage = $filtered_count > 0 ? round(($browser['count'] / $filtered_count) * 100, 1) : 0; ?>
                        <div class="w3-row w3-margin-bottom">
                            <div class="w3-col s7"><?=$browser['browser'] ?: 'Unknown'?></div>
                            <div class="w3-col s3"><?=$browser['count']?></div>
                            <div class="w3-col s2"><?=$percentage?>%</div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="w3-text-grey">No browser data available</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="w3-col l4">
        <div class="w3-card">
            <header class="w3-container orange-theme">
                <h3>Operating Systems</h3>
            </header>
            <div class="w3-container w3-padding">
                <?php if(mysqli_num_rows($os_stats_result) > 0): ?>
                    <?php while($os = mysqli_fetch_assoc($os_stats_result)): ?>
                        <?php $percentage = $filtered_count > 0 ? round(($os['count'] / $filtered_count) * 100, 1) : 0; ?>
                        <div class="w3-row w3-margin-bottom">
                            <div class="w3-col s7"><?=$os['os'] ?: 'Unknown'?></div>
                            <div class="w3-col s3"><?=$os['count']?></div>
                            <div class="w3-col s2"><?=$percentage?>%</div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="w3-text-grey">No OS data available</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="w3-col l4">
        <div class="w3-card">
            <header class="w3-container orange-theme">
                <h3>Top Assets</h3>
            </header>
            <div class="w3-container w3-padding">
                <?php mysqli_data_seek($top_assets_result, 0); ?>
                <?php if(mysqli_num_rows($top_assets_result) > 0): ?>
                    <?php while($asset = mysqli_fetch_assoc($top_assets_result)): ?>
                        <div class="w3-row w3-margin-bottom">
                            <div class="w3-col s8">
                                <a href="?asset_id=<?=$asset['id']?>" class="w3-text-orange">
                                    <?=htmlspecialchars($asset['name'])?>
                                </a>
                            </div>
                            <div class="w3-col s4"><strong><?=$asset['view_count']?></strong></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="w3-text-grey">No asset data available</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Statistics Table -->
<div class="w3-card w3-margin-top">
    <header class="w3-container orange-theme">
        <h2>Detailed Page Views (Showing latest 50)</h2>
    </header>
    <div class="w3-responsive">
        <table class="w3-table w3-striped w3-small">
            <thead>
                <tr class="w3-black">
                    <th>Date/Time</th>
                    <th>Asset</th>
                    <th>Page URL</th>
                    <th>IP Address</th>
                    <th>Browser</th>
                    <th>OS</th>
                    <th>Referrer</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($detailed_stats_result) > 0): ?>
                    <?php while($stat = mysqli_fetch_assoc($detailed_stats_result)): ?>
                        <tr>
                            <td><?=date('M j, Y H:i', strtotime($stat['viewed_at']))?></td>
                            <td>
                                <span class="w3-tag orange-theme w3-small">
                                    <?=htmlspecialchars($stat['asset_name'] ?: 'Unknown')?>
                                </span>
                            </td>
                            <td>
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                     title="<?=htmlspecialchars($stat['url'])?>">
                                    <?=htmlspecialchars($stat['url'])?>
                                </div>
                            </td>
                            <td><?=htmlspecialchars($stat['ip_address'])?></td>
                            <td><?=htmlspecialchars($stat['browser'] ?: 'Unknown')?></td>
                            <td><?=htmlspecialchars($stat['os'] ?: 'Unknown')?></td>
                            <td>
                                <?php if($stat['referrer']): ?>
                                    <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                         title="<?=htmlspecialchars($stat['referrer'])?>">
                                        <?=htmlspecialchars($stat['referrer'])?>
                                    </div>
                                <?php else: ?>
                                    <span class="w3-text-grey">Direct</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="w3-center w3-text-grey w3-padding">
                            No data found matching your filters.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>