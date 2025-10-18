<?php

function check_admin_login() {
    if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        redirect('login.php');
    }
}

function admin_logout() {
    session_destroy();
    redirect('login.php');
}

function check_admin_permission($required_role = 'admin') {
    check_admin_login();
    
    if($_SESSION['admin_role'] !== $required_role && $_SESSION['admin_role'] !== 'admin') {
        redirect('index.php?error=permission_denied');
    }
}

?>