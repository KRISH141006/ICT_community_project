<?php
$host = "localhost";
$user = "root";
$pass = "Taksh@07";
$db   = "ICT_Community";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>