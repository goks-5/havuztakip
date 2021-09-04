<?php
    include_once 'connect_db.php';
    $success  = "";
    if(isset($_POST['add']))
    {	 
        $gumBaseID  = $_POST['gumBaseID'];
        $data1      = $_POST['data1'];
        $data2      = $_POST['data2'];
        $comment    = $_POST['comment'];
        
        $sql = "INSERT INTO gumBases (gumBaseID,data1,data2,comment,created_at) VALUES ('$gumBaseID','$data1','$data2','$comment',now())";
        if (mysqli_query($conn, $sql))
        {
            $success    =   "Success";
        }
        else
        {
        echo "Error: " . $sql . " " . mysqli_error($conn);
        }
        mysqli_close($conn);
    }
	
    if(isset($_POST['edit']))
    {	 
        $gumBaseID  = $_POST['gumBaseID'];
        $data1      = $_POST['data1'];
        $data2      = $_POST['data2'];
        $comment    = $_POST['comment'];
        
        $sql = "REPLACE INTO gumBases (gumBaseID,data1,data2,comment) VALUES ('$gumBaseID','$data1','$data2','$comment')";
        if (mysqli_query($conn, $sql))
        {
            $success    =   "Success";
        }
        else
        {
        echo "Error: " . $sql . " " . mysqli_error($conn);
        }
        mysqli_close($conn);
    }
	
    if(isset($_POST['delete']))
    {	 
        $sql = "DELETE FROM gumBases WHERE gumBaseID = 'Hju';";

        if (mysqli_query($conn, $sql))
        {
            $success    =   "Success";
        }
        else
        {
        echo "Error: " . $sql . " " . mysqli_error($conn);
        }
        mysqli_close($conn);
    }
?>