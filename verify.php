<?php

session_start();

include 'dbconn.php';

// připojení k databázi  
$conn = new mysqli($servername,$username,$password,$database);

if(isset($_GET['token'])){
    $t = $_GET['token'];
    $query = "select * from cUzivatel where token='$t'";
    $run = mysqli_query($conn, $query);
    if(mysqli_num_rows($run)>0){
        $row = mysqli_fetch_array($run);
        $id = $row['id'];
        $query2 = "update cUzivatel set verify='1', token='' where id='$id'";
        $run2 = mysqli_query($conn, $query2);
        $_SESSION['message'] = "<div class='alert alert-success text-center'>Účet byl úspěšně ověřen</div>";
        header("location: login.php");
       
    }else{
        header("location: login.php");
    }
}else{
    header("location: login.php");
}

?>
