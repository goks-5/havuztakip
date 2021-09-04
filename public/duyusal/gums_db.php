<?php
    include_once 'connect_db.php';
    $success  = "";
    if(isset($_POST['add']))
    {	 
        $gumID  = $_POST['gumID'];
        $data1   = $_POST['data1'];
        $data2   = $_POST['data2'];
        $comment = $_POST['comment'];
        
        $sql = "INSERT INTO gums (gumID,data1,data2,comment,created_at) VALUES ('$gumID','$data1','$data2','$comment',now())";
        if (mysqli_query($conn, $sql))
        {
            $success    =   "New record created successfully !";
        }
        else
        {
        echo "Error: " . $sql . " " . mysqli_error($conn);
        }
        mysqli_close($conn);
    }
?>