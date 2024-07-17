<?php

// Make a connection
$servername = 'localhost';
$username = 'root';
$password = 'root';
$database = 'forum_website';

$conn = mysqli_connect($servername, $username, $password, $database) or die('Connection failed!');
