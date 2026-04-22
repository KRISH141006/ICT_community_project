<?php
$host = "10.129.151.226";
$user = "krish";
$pass = "";
$db   = "ICT_Community";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "connected";
?>