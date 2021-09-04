<?php
    $servername = '127.0.0.1';
    $username = 'taste';
    $password = 'pyIZrtZvq&68';
    $dbname = 'taste';

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection Error: " . $conn->connect_error);
    }
?>
