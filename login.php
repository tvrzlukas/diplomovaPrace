<?php

session_start();

include 'dbconn.php';

// připojení k databázi  
$conn = new mysqli($servername,$username,$password,$database);
$conn->set_charset("utf8");

if(isset($_POST['prihlasit'])){
  $email = mysqli_real_escape_string($conn, ($_POST['email']));
  $heslo = mysqli_real_escape_string($conn, ($_POST['password']));
  $heslo = md5($heslo);

  $query = "select * from cUzivatel where email = '$email'";
  $run = mysqli_query($conn, $query);
  if(mysqli_num_rows($run)>0){
    $row = mysqli_fetch_array($run);
    $db_heslo = $row['heslo'];
    $db_email = $row['email'];
    $verify = $row['verify'];
    if($db_heslo == $heslo){
      
      if($verify == 1){
        $_SESSION['user'] = $email;
        header("location: dashboard.php");
      }else{
        $msg = "<div class='alert alert-danger'>Emailová adresa není ověřena</div>";
      }     
    }else{
      $msg = "<div class='alert alert-danger'>Zadaná hesla se neshodují</div>";
    }
  }else{
    $msg = "<div class='alert alert-danger'>Emailová adresa nenalezena</div>";

  }


}


?>



<!doctype html>
<html lang="en">
  <head>
    <title>Přihlášení</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/main.css" />
    <!-- Bootstrap CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Javascript main -->
    <script src="js/main.js"></script>
  </head>

  <script src="https://www.google.com/recaptcha/api.js" async defer></script>


  <body class="body-login">

    <div class="wrapper" id="wrapper_small">
      <div class="text-center"> 
        <a href="index.php">
          <img src="/img/logo_male.png" width="300" height="75"  style="margin-bottom: 10px; margin-top: 10px;">
        </a><br>

      </div>

      <?php 
      if(isset($msg))
      {
        echo $msg;
      }
      if(isset($_SESSION['reg-suc'])){
        echo $_SESSION['reg-suc'];
        unset($_SESSION['reg-suc']);
      }
      if(isset($_SESSION['message'])){
        echo $_SESSION['message'];
        unset($_SESSION['message']);
      }
      ?>

      <form class="form-signin" action="login.php" method="post"> 
        <div class="form-group">

          <label for="">Přihlašovací jméno</label>
          <input type="text" required class="form-control" name="email" placeholder="přihlašovací jméno" >

        </div>
        <div class="form-group">

          <label for="">Heslo</label>
          <input type="password" required class="form-control" name="password" placeholder="heslo" >

        </div>
        
          <input type="submit" class="btn-outline-register btn-block" name="prihlasit" value="Přihlásit">
      </form>
            
      <div class="login-page-div text-center"> 

          <a href="#" class="smaller">Zapomenuté heslo</a><br>
          <a href="verifyRegistration.php" class="smaller">Registrace nového uživatele</a><br>
          <a href="index.php" class="smaller">Zpět</a>
          
      </div>

    </div>
      
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>


