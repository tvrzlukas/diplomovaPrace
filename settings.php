<?php

session_start();

include 'dbconn.php';

$conn = new mysqli($servername,$username,$password,$database);
$conn->set_charset("utf8");

if(isset($_SESSION['user'])){

    $email = $_SESSION['user'];
    $query = "select * from cUzivatel where email = '$email'";
    $run = mysqli_query($conn, $query);
    if(mysqli_num_rows($run)>0){
      $row = mysqli_fetch_array($run);
      $u_id = $row['id'];
      $titul = $row['titul'];
      $jmeno = $row['jmeno'];
      $prijmeni = $row['prijmeni'];
      $adresa = $row['adresa'];
      $mesto = $row['mesto'];
      $stat = $row['stat'];
      $psc = $row['psc'];
      $telefon = $row['telefon'];
      $cRole_id = $row['cRole_id'];
    }
    if($cRole_id == 1){
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-danger'>Administrátor</span></span>";
    }elseif($cRole_id == 2){
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-warning'>Lékař</span></span>";
    }elseif($cRole_id == 3){
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-primary'>Pacient</span></span>";
    }else{
        $msg = "<div class='alert alert-danger'>Problém s cRole_id v databázi, kontaktujte správce</div>";
    }
}
else{
    header("location: index.php");
}

if(isset($_POST['save'])){
    $user = $u_id;
    $titul = mysqli_real_escape_string($conn, ($_POST['titul']));
    $jmeno = mysqli_real_escape_string($conn, ($_POST['jmeno']));
    $prijmeni = mysqli_real_escape_string($conn, ($_POST['prijmeni']));
    $adresa = mysqli_real_escape_string($conn, ($_POST['adresa']));
    $telefon = mysqli_real_escape_string($conn, ($_POST['telefon']));
    $mesto = mysqli_real_escape_string($conn, ($_POST['mesto']));
    $stat = mysqli_real_escape_string($conn, ($_POST['stat']));
    $psc = mysqli_real_escape_string($conn, ($_POST['psc']));

    $sql = "update cUzivatel set 
        titul = '$titul',
        jmeno = '$jmeno',
        prijmeni = '$prijmeni',
        adresa = '$adresa',
        telefon = '$telefon',
        psc = '$psc',
        mesto = '$mesto',
        stat = '$stat'
        where id = $u_id";
    if ($conn->query($sql) === TRUE){
        $msg = "<div class='alert alert-success text-center'>Změny úspěšně uloženy do databáze</div>";
        //header("location: settings.php");
    }
}

?>

<!doctype html>
<html lang="en">
  <head>
    <title>Dashboard - Nastavení uživatelského účtu</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/main.css" />
    <!-- Bootstrap CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body class="body">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
              <a class="nav-link" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="reservation.php">Rezervace</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="settings.php">Nastavení<span class="sr-only">(current)</span></a>
              </li>
          </ul>
          <form class="form-inline my-2 my-lg-0" method="post">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <?php echo $setRole ?>
            </li>
            </ul>
            <a href="dashboard.php?logout=1" name="odhlasit" class="btn btn-outline-danger my-2 my-sm-0">Odhlásit</a>
          </form>
        </div>
    </nav>

    <div class="container">

        <div class="box">

            <h2 class="text-left">Nastavení uživatelského účtu</h2>

            <?php

            if(isset($msg)){
                echo $msg;
            }

            ?>

            <form method="post">
                
            <div class="form-group"><label for="">Emailová adresa </label><input type="email" name="email" required disabled class="form-control" value="<?php echo $email;?>" id=""></div>
            <div class="form-group">
                <div class="form-row">
                    
                        <div class="col-md-2 mb-3">
                            <label for="">Titul</label><input type="text" name="titul"  class="form-control" value="<?php echo $titul;?>" id="">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="">Jméno</label><input type="text" name="jmeno"  class="form-control" required value="<?php echo $jmeno;?>" id="">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="">Příjmení </label><input type="text" name="prijmeni" required class="form-control" value="<?php echo $prijmeni;?>" id="">
                        </div>

                </div>
            </div>      
            <div class="form-group">
                <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label for="">Adresa </label><input type="text" name="adresa" required class="form-control" value="<?php echo $adresa;?>" id="">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="">Město </label><input type="text" name="mesto" required class="form-control" value="<?php echo $mesto;?>" id="">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="">Stát </label><input type="text" name="stat" required class="form-control" value="<?php echo $stat;?>" id="">
                        </div>
                    </div>
            </div> 
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label for="">PSČ</label><input type="text" name="psc" required class="form-control" value="<?php echo $psc;?>" id="">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="">Telefon </label><input type="text" name="telefon"  required class="form-control" value="<?php echo $telefon;?>" id="">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="">Role </label><input type="text" name="role" disabled 
                        <?php 
                            switch ($cRole_id){
                                case 1:
                                    echo "value='Administrátor' style='color: white;' class='form-control bg-danger'";
                                break;
                                case 2:
                                    echo "value='Lékař' style='color: black;' class='form-control bg-warning'";
                                break;
                                case 3:
                                    echo "value='Pacient' style='color: white;' class='form-control bg-primary'";
                                break;
                            }
                            ?> id="">
                        </div>
                    </div>
                </div>

                <input class="btn btn-primary" name="save" type="submit" value="Uložit změny" >

            </form>

        </div>

    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>