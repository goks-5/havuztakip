<?php

$servername = '127.0.0.1';
$username = 'taste';
$password = 'pyIZrtZvq&68';
$dbname = 'taste';


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Bağlantı Hatası: " . $conn->connect_error);
}

  $result = $conn->query('select * from users');
  print_r($result->fetch_assoc());
