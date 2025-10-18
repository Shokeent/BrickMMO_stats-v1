<?php

include('../includes/config.php');
include('../includes/connect.php');
include('../includes/functions.php');

$error = '';

if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    redirect('index.php');
}

if($_POST) {
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $password = $_POST['password'];
    
    if(empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($connect, $query);
        
        if(mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            if(password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                
                redirect('index.php');
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

define('PAGE_TITLE', 'Admin Login');
include('../includes/header.php');

?>

<h1>Admin Login</h1>

<div class="w3-row">
    <div class="w3-col l4 m2">&nbsp;</div>
    <div class="w3-col l4 m8 s12">
        <div class="w3-card">
            <header class="w3-container orange-theme">
                <h2>Admin Access</h2>
            </header>
            <div class="w3-container w3-padding">
                <?php if($error): ?>
                    <div class="w3-panel w3-red w3-round">
                        <p><?=$error?></p>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <p>
                        <label class="w3-text-black"><b>Username:</b></label>
                        <input class="w3-input w3-border" type="text" name="username" required>
                    </p>
                    
                    <p>
                        <label class="w3-text-black"><b>Password:</b></label>
                        <input class="w3-input w3-border" type="password" name="password" required>
                    </p>
                    
                    <p>
                        <input type="submit" value="Login" class="w3-button orange-theme w3-block">
                    </p>
                </form>
            </div>
        </div>
    </div>
    <div class="w3-col l4 m2">&nbsp;</div>
</div>

</body>
</html>