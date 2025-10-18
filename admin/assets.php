<?php

include('../includes/config.php');
include('../includes/connect.php');
include('../includes/functions.php');
include('auth.php');

check_admin_login();

if(isset($_GET['logout'])) {
    admin_logout();
}

$error = '';
$success = '';

if($_POST && isset($_POST['create_asset'])) {
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $url = mysqli_real_escape_string($connect, $_POST['url']);
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    
    if(empty($name)) {
        $error = 'Asset name is required.';
    } elseif(empty($url)) {
        $error = 'Website URL is required.';
    } elseif(!filter_var($url, FILTER_VALIDATE_URL)) {
        $error = 'Please enter a valid URL.';
    } else {
        $query = "INSERT INTO assets (name, url, description, created_at) VALUES ('$name', '$url', '$description', NOW())";
        if(mysqli_query($connect, $query)) {
            $success = 'Asset created successfully!';
        } else {
            $error = 'Error creating asset.';
        }
    }
}

if(isset($_GET['delete']) && $_GET['delete']) {
    $delete_id = (int)$_GET['delete'];
    $query = "DELETE FROM assets WHERE id = $delete_id";
    if(mysqli_query($connect, $query)) {
        mysqli_query($connect, "DELETE FROM stats WHERE asset_id = $delete_id");
        $success = 'Asset deleted successfully!';
    } else {
        $error = 'Error deleting asset.';
    }
}

$assets_query = "SELECT *, (SELECT COUNT(*) FROM stats WHERE asset_id = assets.id) as view_count FROM assets ORDER BY created_at DESC";
$assets_result = mysqli_query($connect, $assets_query);

define('PAGE_TITLE', 'Manage Assets');
include('../includes/header.php');

?>

<div class="w3-bar orange-theme">
    <a href="index.php" class="w3-bar-item w3-button w3-border-right">Dashboard</a>
    <a href="assets.php" class="w3-bar-item w3-button w3-border-right" style="background-color: rgba(255,255,255,0.2);">Assets</a>
    <a href="stats.php" class="w3-bar-item w3-button w3-border-right">Statistics</a>
    <a href="users.php" class="w3-bar-item w3-button w3-border-right">Users</a>
    <a href="?logout=1" class="w3-bar-item w3-button w3-right">Logout</a>
</div>

<h2 class="page-title">Manage Assets</h2>
<p style="color: #6c757d; margin-bottom: 30px;">Create assets to track website analytics. Each asset will automatically generate a unique tracking code that you can copy and paste into your website's HTML.</p>

<!-- Status Cards -->
<div class="w3-row-padding w3-margin-bottom">
    <?php
    $total_assets = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM assets"))['count'] ?? 0;
    $total_views = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM stats"))['count'] ?? 0;
    ?>
    <div class="w3-half">
        <div class="stats-card">
            <div class="stats-number"><?= number_format($total_assets) ?></div>
            <div class="stats-label">Total Assets</div>
            <div class="stats-description">Active tracking endpoints</div>
        </div>
    </div>
    
    <div class="w3-half">
        <div class="stats-card">
            <div class="stats-number"><?= number_format($total_views) ?></div>
            <div class="stats-label">Total Views</div>
            <div class="stats-description">Combined asset analytics</div>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<?php if($error): ?>
    <div class="w3-panel alert-error w3-margin-bottom">
        <p style="margin: 0; font-weight: 500;"><?= htmlspecialchars($error) ?></p>
    </div>
<?php endif; ?>

<?php if($success): ?>
    <div class="w3-panel alert-success w3-margin-bottom">
        <p style="margin: 0; font-weight: 500;"><?= htmlspecialchars($success) ?></p>
    </div>
<?php endif; ?>

<!-- Add New Asset Form -->
<div class="form-section">
    <h3>Add New Asset</h3>
    <form method="post">
        <div class="w3-row-padding">
            <div class="w3-half">
                <label class="w3-text-dark-grey" style="font-weight: 600; margin-bottom: 8px; display: block;">
                    Asset Name <span style="color: var(--danger-color);">*</span>
                </label>
                <input class="w3-input" type="text" name="name" placeholder="Enter a descriptive name" required>
            </div>
            
            <div class="w3-half">
                <label class="w3-text-dark-grey" style="font-weight: 600; margin-bottom: 8px; display: block;">
                    Website URL <span style="color: var(--danger-color);">*</span>
                </label>
                <input class="w3-input" type="url" name="url" placeholder="https://example.com" required>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <label class="w3-text-dark-grey" style="font-weight: 600; margin-bottom: 8px; display: block;">
                Description
            </label>
            <textarea class="w3-input" name="description" rows="3" placeholder="Optional description of this asset"></textarea>
        </div>
        
        <div style="margin-top: 25px;">
            <input type="submit" name="create_asset" value="Create Asset" class="w3-button orange-theme w3-large">
        </div>
    </form>
</div>

<!-- Assets Table -->
<div class="w3-card w3-white w3-margin-bottom">
    <div class="section-header">Your Assets & Tracking Codes</div>
    <div class="w3-responsive">
        <table class="w3-table w3-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Views</th>
                    <th>Tracking Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($assets_result) > 0): ?>
                    <?php while($asset = mysqli_fetch_assoc($assets_result)): ?>
                    <tr>
                        <td><span style="color: var(--primary-color); font-weight: 600;"><?= $asset['id'] ?></span></td>
                        <td>
                            <strong><?= htmlspecialchars($asset['name']) ?></strong>
                            <?php if($asset['description']): ?>
                                <br><small style="color: #6c757d;"><?= htmlspecialchars($asset['description']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($asset['url']): ?>
                                <a href="<?= htmlspecialchars($asset['url']) ?>" target="_blank" style="color: var(--primary-color);">
                                    <?= htmlspecialchars(strlen($asset['url']) > 25 ? substr($asset['url'], 0, 25) . '...' : $asset['url']) ?>
                                </a>
                            <?php else: ?>
                                <span style="color: #6c757d;">No URL</span>
                            <?php endif; ?>
                        </td>
                        <td><span style="color: var(--primary-color); font-weight: 600;"><?= number_format($asset['view_count']) ?></span></td>
                        <td>
                            <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px; margin: 5px 0;">
                                &lt;script src="<?= $_SERVER['HTTP_HOST'] ?>/BrickMMO_stats-v1/tracker.js" data-asset-id="<?= $asset['id'] ?>"&gt;&lt;/script&gt;
                            </div>
                            <button onclick="copyTrackingCode('<?= $asset['id'] ?>')" class="w3-button w3-small w3-green w3-round" style="margin-top: 5px;">
                                Copy Code
                            </button>
                        </td>
                        <td>
                            <a href="?delete=<?= $asset['id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this asset? This will also delete all associated statistics.')" 
                               class="w3-button w3-red w3-small w3-round">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #6c757d; padding: 30px;">
                            No assets found. Create your first asset above to get started.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function copyTrackingCode(assetId) {
    const code = `<script src="<?= $_SERVER['HTTP_HOST'] ?>/BrickMMO_stats-v1/tracker.js" data-asset-id="${assetId}"><\/script>`;
    
    const textarea = document.createElement('textarea');
    textarea.value = code;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    
    alert('Tracking code copied to clipboard!');
}
</script>

</body>
</html>