<?php

include('../includes/config.php');
include('../includes/connect.php');
include('../includes/functions.php');
include('auth.php');

check_admin_permission('admin');

if(isset($_GET['logout'])) {
    admin_logout();
}

$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';
$user_id = $_GET['id'] ?? null;

if($_POST) {
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $password = $_POST['password'];
    $role = mysqli_real_escape_string($connect, $_POST['role']);
    
    if(empty($username) || (empty($password) && $action === 'new')) {
        $error = 'Username and password are required.';
    } else {
        if($action === 'new') {
            $check_query = "SELECT id FROM users WHERE username = '$username'";
            $check_result = mysqli_query($connect, $check_query);
            
            if(mysqli_num_rows($check_result) > 0) {
                $error = 'Username already exists.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO users (username, password, role, created_at) VALUES ('$username', '$hashed_password', '$role', NOW())";
                if(mysqli_query($connect, $query)) {
                    $success = 'User created successfully!';
                    $action = 'list';
                } else {
                    $error = 'Error creating user: ' . mysqli_error($connect);
                }
            }
        } elseif($action === 'edit' && $user_id) {
            $update_fields = "username = '$username', role = '$role'";
            if(!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_fields .= ", password = '$hashed_password'";
            }
            
            $query = "UPDATE users SET $update_fields WHERE id = $user_id";
            if(mysqli_query($connect, $query)) {
                $success = 'User updated successfully!';
                $action = 'list';
            } else {
                $error = 'Error updating user: ' . mysqli_error($connect);
            }
        }
    }
}

if(isset($_GET['delete']) && $_GET['delete']) {
    $delete_id = (int)$_GET['delete'];
    
    if($delete_id == $_SESSION['admin_user_id']) {
        $error = 'You cannot delete your own account.';
    } else {
        $delete_query = "DELETE FROM users WHERE id = $delete_id";
        if(mysqli_query($connect, $delete_query)) {
            $success = 'User deleted successfully!';
        } else {
            $error = 'Error deleting user: ' . mysqli_error($connect);
        }
    }
}

$user = null;
if($action === 'edit' && $user_id) {
    $user_query = "SELECT * FROM users WHERE id = $user_id";
    $user_result = mysqli_query($connect, $user_query);
    $user = mysqli_fetch_assoc($user_result);
    
    if(!$user) {
        $error = 'User not found.';
        $action = 'list';
    }
}

if($action === 'list') {
    $users_query = "SELECT * FROM users ORDER BY created_at DESC";
    $users_result = mysqli_query($connect, $users_query);
}

define('PAGE_TITLE', 'User Management');
include('../includes/header.php');

?>

<div class="w3-bar orange-theme">
    <a href="index.php" class="w3-bar-item w3-button w3-border-right">Dashboard</a>
    <a href="assets.php" class="w3-bar-item w3-button w3-border-right">Assets</a>
    <a href="stats.php" class="w3-bar-item w3-button w3-border-right">Statistics</a>
    <a href="users.php" class="w3-bar-item w3-button w3-border-right" style="background-color: rgba(255,255,255,0.2);">Users</a>
    <a href="?logout=1" class="w3-bar-item w3-button w3-right">Logout</a>
</div>

<h2 class="page-title">User Management</h2>

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

<?php if($action === 'list'): ?>
    
    <div class="w3-margin-bottom">
        <a href="?action=new" class="w3-button orange-theme">
            <i class="fa fa-plus"></i> Add New User
        </a>
    </div>
    
    <div class="w3-card">
        <header class="w3-container orange-theme">
            <h3>System Users</h3>
        </header>
        
        <div class="w3-responsive">
            <table class="w3-table w3-striped">
                <thead>
                    <tr class="w3-black">
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($users_result) > 0): ?>
                        <?php while($user_row = mysqli_fetch_assoc($users_result)): ?>
                            <tr>
                                <td><?=$user_row['id']?></td>
                                <td>
                                    <strong><?=htmlspecialchars($user_row['username'])?></strong>
                                    <?php if($user_row['id'] == $_SESSION['admin_user_id']): ?>
                                        <span class="w3-tag w3-blue w3-tiny">You</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="w3-tag <?=$user_row['role'] === 'admin' ? 'w3-red' : 'w3-green'?> w3-round">
                                        <?=ucfirst($user_row['role'])?>
                                    </span>
                                </td>
                                <td><?=date('M j, Y', strtotime($user_row['created_at']))?></td>
                                <td>
                                    <a href="?action=edit&id=<?=$user_row['id']?>" class="w3-button w3-small orange-theme" title="Edit">
                                        Edit
                                    </a>
                                    <?php if($user_row['id'] != $_SESSION['admin_user_id']): ?>
                                        <a href="?delete=<?=$user_row['id']?>" class="w3-button w3-small w3-red" title="Delete" 
                                           onclick="return confirm('Are you sure you want to delete this user?')">
                                            Delete
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="w3-center w3-text-grey w3-padding">
                                No users found. <a href="?action=new">Create your first user</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
<?php elseif($action === 'new' || $action === 'edit'): ?>
    
    <div class="w3-margin-bottom">
        <a href="users.php" class="w3-button w3-grey">
            ← Back to List
        </a>
    </div>
    
    <div class="w3-card">
        <header class="w3-container orange-theme">
            <h3><?=$action === 'new' ? 'Create New User' : 'Edit User'?></h3>
        </header>
        <div class="w3-container w3-padding">
            <form method="post">
                <p>
                    <label class="w3-text-black"><b>Username:</b></label>
                    <input class="w3-input w3-border" type="text" name="username" 
                           value="<?=isset($user) ? htmlspecialchars($user['username']) : ''?>" required>
                </p>
                
                <p>
                    <label class="w3-text-black"><b>Password:</b></label>
                    <input class="w3-input w3-border" type="password" name="password" 
                           <?=$action === 'new' ? 'required' : ''?>>
                    <?php if($action === 'edit'): ?>
                        <small class="w3-text-grey">Leave blank to keep current password</small>
                    <?php endif; ?>
                </p>
                
                <p>
                    <label class="w3-text-black"><b>Role:</b></label>
                    <select class="w3-select w3-border" name="role">
                        <option value="admin" <?=isset($user) && $user['role'] === 'admin' ? 'selected' : ''?>>Admin</option>
                        <option value="user" <?=isset($user) && $user['role'] === 'user' ? 'selected' : ''?>>User</option>
                    </select>
                </p>
                
                <p>
                    <input type="submit" value="<?=$action === 'new' ? 'Create User' : 'Update User'?>" class="w3-button orange-theme">
                    <a href="users.php" class="w3-button w3-grey">Cancel</a>
                </p>
            </form>
        </div>
    </div>
    
<?php endif; ?>

</body>
</html>
