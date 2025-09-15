<?php

// ************************************************************
// Connect to the database
// 
// Load environment variables from the .env file and then use
// the database variables to connect to a MySQL database. 

$env = file(__DIR__.'/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach($env as $value)
{
  $value = explode('=', $value);  
  define($value[0], $value[1]);
}

$connect = mysqli_connect(
  DB_HOST, 
  DB_USERNAME, 
  DB_PASSWORD, 
  DB_DATABASE);

  // Check connection and display error if failed
if (!$connect) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set charset to utf8mb4 for proper Unicode support
mysqli_set_charset($connect, "utf8mb4");

?>
