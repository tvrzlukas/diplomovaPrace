<?php

session_start();

include 'dbconn.php';

// připojení k databázi  
$conn = new mysqli($servername,$username,$password,$database);
$conn->set_charset("utf8");

if(isset($_SESSION['user'])){

    $email = $_SESSION['user'];
    $query = "select * from cUzivatel where email = '$email'";
    $run = mysqli_query($conn, $query);
    if(mysqli_num_rows($run)>0){
      $row = mysqli_fetch_array($run);
      $cRole_id = $row['cRole_id'];
      $id_uzivatel = $row['id'];
    }
    if($cRole_id == 1){
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-danger'>Administrátor</span></span>";
    }elseif($cRole_id == 2){
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-warning'>Lékař</span></span>";      
        
        if(isset($_GET['potvrdit'])){
          $x = $_GET['potvrdit'];
          $query = "select * from cRezervace where id_rezervace=$x";
          $run = mysqli_query($conn, $query);
          if(mysqli_num_rows($run)>0){
            $query2 = "update cRezervace set isValidate='1' where id_rezervace=$x";
            $run2 = mysqli_query($conn, $query2);   
            header("location: dashboard.php"); 
          }      
        }
        
        if(isset($_GET['zamitnout'])){
          $x = $_GET['zamitnout'];
          $query = "select * from cRezervace where id_rezervace=$x";
          $run = mysqli_query($conn, $query);
          if(mysqli_num_rows($run)>0){
            $query2 = "update cRezervace set isDeleted='1' where id_rezervace=$x";
            $run2 = mysqli_query($conn, $query2);    
            header("location: dashboard.php"); 
          }  
        }
        
        if(isset($_GET['zrusit'])){
          $x = $_GET['zrusit'];
          $query = "select * from cRezervace where id_rezervace=$x";
          $run = mysqli_query($conn, $query);
          if(mysqli_num_rows($run)>0){
            $query2 = "update cRezervace set isDeleted='1' where id_rezervace=$x";
            $run2 = mysqli_query($conn, $query2);  
            header("location: dashboard.php");   
          }  
        }
     
     
      }elseif($cRole_id == 3){
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-primary'>Pacient</span></span>";
    }else{
        $msg = "<div class='alert alert-danger'>Problém s cRole_id v databázi, kontaktujte správce</div>";
    }

    // ******************************************************************************************


    // SELECTY PRO DASHBOARD - LÉKAŘSKÁ SEKCE

    // ZJIŠTĚNÍ POČTU NEPOTVRZENÝCH REZERVACÍ

      $sql = "SELECT * FROM cRezervace as r JOIN cSluzba as s ON s.id = r.cSluzba_id JOIN cUzivatel as u ON
      u.id = r.cUzivatel_id JOIN cLekar as l ON l.id = r.cLekar_id 
      WHERE r.date >= curdate() and isValidate = 0 and isDeleted = 0 ORDER BY date";
      $result = $conn->query($sql);
      $nepotvrzene_rezervace = $result->num_rows;

    // ZJIŠTĚNÍ POČTU POTVRZENÝCH BUDOUCÍCH REZERVACÍ

      $sql = "SELECT * FROM cRezervace as r JOIN cSluzba as s ON s.id = r.cSluzba_id JOIN cUzivatel as u ON
      u.id = r.cUzivatel_id JOIN cLekar as l ON l.id = r.cLekar_id 
      WHERE r.date >= curdate() and isValidate = 1 and isDeleted = 0 ORDER BY date";
      $result = $conn->query($sql);
      $potvrzene_rezervace = $result->num_rows;

    // ZJIŠTĚNÍ POČTU VŠECH REZERVACÍ V DATABÁZI

      $sql = "SELECT * FROM cRezervace as r JOIN cSluzba as s ON s.id = r.cSluzba_id JOIN cUzivatel as u ON
      u.id = r.cUzivatel_id JOIN cLekar as l ON l.id = r.cLekar_id 
      WHERE isDeleted = 0 ORDER BY date";
      $result = $conn->query($sql);
      $vsechny_rezervace = $result->num_rows;

    // ******************************************************************************************

    // SELECTY PRO DASHBOARD - PACIENTSKÁ SEKCE

    // ZJIŠTĚNÍ POČTU REZERVACÍ AKTUÁLNĚ PŘIHLÁŠENÉHO PACIENTA

      $sql = "SELECT * FROM cRezervace as r JOIN cSluzba as s ON s.id = r.cSluzba_id JOIN 
      cUzivatel as u ON u.id = r.cUzivatel_id JOIN cLekar as l ON l.id = r.cLekar_id 
      WHERE r.date >= curdate() and u.id = $id_uzivatel and isDeleted != 1 ORDER BY date";
      $result = $conn->query($sql);
      $me_rezervace = $result->num_rows;

    // PROBĚHLÉ REZERVACE AKTUÁLNÍHO PACIENTA

      $sql = "SELECT * FROM cRezervace as r JOIN cSluzba as s ON s.id = r.cSluzba_id JOIN 
      cUzivatel as u ON u.id = r.cUzivatel_id JOIN cLekar as l ON l.id = r.cLekar_id 
      WHERE r.date < curdate() and u.id = $id_uzivatel and isValidate != 0 and isDeleted != 1 ORDER BY date";
      $result = $conn->query($sql);
      $probehle_rezervace = $result->num_rows;

    // ******************************************************************************************

    // SELECTY PRO DASHBOARD - ADMIN SEKCE

    // VŠICHNI UŽIVATELÉ 

      $sql = "SELECT * FROM cUzivatel WHERE cRole_id in ('2','3')";
      $result = $conn->query($sql);
      $num_users = $result->num_rows;

    // VŠECHNY SLUŽBY

      $sql = "SELECT * FROM cSluzba as s JOIN cSpecializace as sp ON sp.id = s.cSpecializace_id";
      $result = $conn->query($sql);
      $num_servicies = $result->num_rows;

    // VŠECHNY SPECIALIZACE

      $sql = "SELECT * FROM cSpecializace";
      $result = $conn->query($sql);
      $num_spec = $result->num_rows;

    // NOVÝ UŽIVATEL/SPECIALIZACE/SLUŽBA

    if(isset($_POST['submit_1'])){
      $nazev = $_POST['nazev'];
      $stmt = $conn->prepare("select * from cSpecializace where nazevSpecializace = ?");
      $stmt->bind_param('s',$nazev);
      if($stmt->execute()){
          $result = $stmt->get_result();
          if($result->num_rows >0){
          $alert = "<div class='alert alert-danger text-center'>Zadaná specializace již existuje</div>";
          }
      }else{
        $sql = $conn->prepare("INSERT INTO cSpecializace (nazevSpecializace) value (?)"); 
        $sql->bind_param('s',$nazev);
        $sql->execute();
      }
    }

    if(isset($_POST['submit1'])){
      $email = mysqli_real_escape_string($conn, ($_POST['email']));
      $titul = mysqli_real_escape_string($conn, ($_POST['titul']));
      $name = mysqli_real_escape_string($conn, ($_POST['name']));
      $surname = mysqli_real_escape_string($conn, ($_POST['surname']));
      $password1 = mysqli_real_escape_string($conn, ($_POST['password1']));
      $password2 = mysqli_real_escape_string($conn, ($_POST['password2']));
      $address = mysqli_real_escape_string($conn, ($_POST['address']));
      $phone = mysqli_real_escape_string($conn, ($_POST['phone']));
      $city = mysqli_real_escape_string($conn, ($_POST['city']));
      $state = mysqli_real_escape_string($conn, ($_POST['state']));
      $zip = mysqli_real_escape_string($conn, ($_POST['zip']));
      $role = mysqli_real_escape_string($conn, ($_POST['role']));
      
      $query = "select * from cUzivatel where email = '$email'";
      $run = mysqli_query($conn, $query);
      if(mysqli_num_rows($run)>0){
        $msg = "<div class='alert alert-danger text-center'>Zadaná emailová adresa již existuje</div>";
      }
      else{
        if($password1 != $password2){
          $msg = "<div class='alert alert-danger text-center'>Zadaná hesla se neshodují</div>";
        }
        else{
          //$password1 = strtolower($password1);
          $h_password = md5($password1);
          $token = uniqid(md5(time()));
          $sql = "INSERT INTO cUzivatel(titul,jmeno,prijmeni,email,heslo,token,verify,adresa,mesto,psc,telefon,stat,cRole_id) 
                  values('$titul','$name','$surname','$email','$h_password','$token',0,'$address','$city','$zip','$phone','$state','$role')";
         
          if ($conn->query($sql) === TRUE){
              $_SESSION['reg-suc'] = "<div class='alert alert-success text-center'>Registrace proběhla v pořádku, byl zaslán validační e-mail</div>";
              header("Location: login.php");
          }
          else{
              echo "Error: " . $sql . "<br>" . $conn->error;
          }
        } 

      }
      
  }



}
else{
    header("location: index.php");
}

?>


<!doctype html>
<html lang="en">
  <head>
    <title>Dashboard</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/main.css" />
    <!-- Bootstrap CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body class="body">

  <?php

    if(isset($_GET['logout'])){
        session_unset();
        session_destroy();
        header("location: index.php");
        exit();
    }

  ?>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
              <a class="nav-link" href="dashboard.php">Dashboard <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="reservation.php">Rezervace</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="settings.php">Nastavení</a>
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

  <?php

  /*
  *******************
  DASHBOARD - ADMINISTRATOR
  *******************
  */

  if($cRole_id == 1){

    ECHO "<section id='dashboard-admin' class='wrapper-index aboutus mt-4 mb-2 py-1'>";

    if(isset($_SESSION['ins-suc'])){
      echo $_SESSION['ins-suc'];
      unset($_SESSION['ins-suc']);
    }

    ECHO "<div class='container'><div class='box'><h2 class='text-left'>Uživatelé <span class='badge badge-pill badge-danger'>$num_users</span></h2>";

    if($num_users > 0){

      ECHO "<table class='table table-sm'>
      <thead class='thead table-dark bg-dark'>
        <tr>
          <th scope='col'>ID</th>
          <th scope='col'>Jmeno</th>
          <th scope='col'>Příjmení</th>
          <th scope='col'>Email</th>
          <th scope='col' width='300px'>Role</th>
        </tr>
      </thead>";

      $sql = "SELECT u.id as `uid`,u.jmeno,u.prijmeni,u.email,u.cRole_id,s.id as `sid`,s.nazevSpecializace FROM cUzivatel as u
      LEFT JOIN cLekar as l ON l.cUzivatel_id = u.id 
      LEFT JOIN cSpecializace as s ON s.id = l.cSpecializace_id
      WHERE cRole_id in ('2','3') 
      ORDER BY uid ASC";
      $result = $conn->query($sql);

      if ($result->num_rows > 0){

        while ($row = $result->fetch_assoc()){
        echo "<tr><td>".$row['uid']."</td><td>".$row['jmeno']."</td><td>".$row['prijmeni']."</td><td>".$row['email']."</td><td>";
        if ($row['cRole_id'] == 2){
          echo "<span class='badge badge-warning'>Lékař</span>"." "."Specializace: ".$row['sid']." - ".$row['nazevSpecializace'];
        }elseif ($row['cRole_id'] == 3){
          echo "<span class='badge badge-primary'>Pacient</span>";
        }
        echo "</td></tr>";
        }

      } 
      echo "</table>";
    } else {
      echo "<p>V databázi se nenachází žádný uživatel</p>";
    }
    ECHO "<a href='insert.php?insert=1' class='btn btn-danger'>Přidat nového uživatele</a>";
    echo " ";
    ECHO "<a href='insert.php?insert=4' class='btn btn-warning'>Přiřadit lékaři specializaci</a>";

    echo "</div>";

        ECHO "<div class='box'><h2 class='text-left'>Specializace <span class='badge badge-pill badge-primary'>$num_spec</span></h2>";

        if($num_spec > 0){
    
          ECHO "<table class='table table-sm'>
          <thead class='thead table-dark bg-dark'>
            <tr>
              <th scope='col'>ID</th>
              <th scope='col'>Název</th>
            </tr>
          </thead>";
    
          $sql = "SELECT * FROM cSpecializace";
          $result = $conn->query($sql);
    
          if ($result->num_rows > 0){
    
            while ($row = $result->fetch_assoc()){
            echo "<tr><td>".$row['id']."</td><td>".$row['nazevSpecializace']."</td></tr>";
            }
    
          } 
          echo "</table>";
        } else {
          echo "<p>V databázi se nenachází žádná specializace</p>";
        }
    
        ECHO "<a href='insert.php?insert=2' class='btn btn-primary'>Přidat nového specializaci</a>";
        
    
        echo "</div>";
    
        ?>

        <?php

    ECHO "<div class='box'><h2 class='text-left'>Služby <span class='badge badge-pill badge-success'>$num_servicies</span></h2>";

    if($num_servicies > 0){

      ECHO "<table class='table table-sm'>
      <thead class='thead table-dark bg-dark'>
        <tr>
          <th scope='col'>ID</th>
          <th scope='col'>Název</th>
          <th scope='col' width='100px'>Cena</th>
          <th scope='col' width='250px'>Patří do specializace</th>
        </tr>
      </thead>";

      $sql = "SELECT s.id AS `sid`,s.nazev,s.cena,sp.nazevSpecializace FROM cSluzba as s JOIN cSpecializace as sp ON sp.id = s.cSpecializace_id";
      $result = $conn->query($sql);

      if ($result->num_rows > 0){

        while ($row = $result->fetch_assoc()){
        echo "<tr><td>".$row['sid']."</td><td>".$row['nazev']."</td><td>".$row['cena']." Kč"."</td><td>".$row['nazevSpecializace']."</td></tr>";
        }

      } 
      echo "</table>";
    } else {
      echo "<p>V databázi se nenachází žádná služba</p>";
    }

    ECHO "<a href='insert.php?insert=3' class='btn btn-success'>Přidat novou službu</a>";


    echo "</div>";

    ECHO "</section>";

  }

  /*
  *******************
  DASHBOARD - LÉKAŘ
  *******************
  */
  
  if($cRole_id == 2){

    echo "<section id='dashboard-doctor' style='display: inline;' class='wrapper-index aboutus mt-4 mb-2 py-1'>
    <div class='dashboard'><div class='box'><h2 class='text-left'>Nepotvrzené rezervace <span class='badge badge-pill badge-danger'>$nepotvrzene_rezervace</span></h2>"; 

    if($nepotvrzene_rezervace > 0){
          echo "<table class='table table-sm'>
          <thead class='thead table-dark bg-dark'>
            <tr>
              <th scope='col' width='130px' >Datum</th>
              <th scope='col'>E-mailová adresa</th>
              <th scope='col'>Jméno</th>
              <th scope='col'>Příjmení</th>
              <th scope='col'>Vyšetření</th>
              <th scope='col'>Slot</th>
              <th scope='col' width='200px'>Akce</th>
            </tr>
          </thead>";

          $sql = "SELECT * FROM cRezervace as r JOIN cSluzba as s ON s.id = r.cSluzba_id JOIN cUzivatel as u ON
          u.id = r.cUzivatel_id JOIN cLekar as l ON l.id = r.cLekar_id 
          WHERE r.date >= curdate() and isValidate = 0 and isDeleted = 0 ORDER BY date";
          $result = $conn->query($sql);

          if ($result->num_rows > 0){

            while ($row = $result->fetch_assoc()){
              echo "<tr><td>".$row['date']."</td><td>".$row['email']."</td><td>".$row['jmeno']."</td><td>".$row['prijmeni']."</td><td>".$row['nazev'].
              "</td><td>".$row['timeslot']."</td><td>"."<div style='margin: 0 auto;'><a href='dashboard.php?potvrdit=".$row['id_rezervace']."' class='btn badge badge-success'>Potvrdit</a>".
              " "."<a href='dashboard.php?zamitnout=".$row['id_rezervace']."' class='btn badge badge-danger'>Zamítnout</a></div>".
              "</td></tr>";

          }


        }
      echo "</table></div>";

    }else{
      echo "<p>Žádné rezervace k potvrzení</p></div>";
    } 


  echo "<div class='box'><h2 class='text-left'>Budoucí rezervace <span class='badge badge-pill badge-success'>$potvrzene_rezervace</h2>";




  if($potvrzene_rezervace > 0){
    echo "<table class='table table-sm'>
    <thead class='thead table-dark bg-dark'>
      <tr>
        <th scope='col' width='130px' >Datum</th>
        <th scope='col'>E-mailová adresa</th>
        <th scope='col'>Jméno</th>
        <th scope='col'>Příjmení</th>
        <th scope='col'>Vyšetření</th>
        <th scope='col'>Slot</th>
        <th scope='col' width='200px'>Akce</th>
      </tr>
    </thead>";

    $sql = "SELECT * FROM cRezervace as r JOIN cSluzba as s ON s.id = r.cSluzba_id JOIN cUzivatel as u ON
    u.id = r.cUzivatel_id JOIN cLekar as l ON l.id = r.cLekar_id 
    WHERE r.date >= curdate() and isValidate = 1 and isDeleted = 0 ORDER BY date";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){

      while ($row = $result->fetch_assoc()){
        echo "<tr><td>".$row['date']."</td><td>".$row['email']."</td><td>".$row['jmeno']."</td><td>".$row['prijmeni']."</td><td>".$row['nazev'].
        "</td><td>".$row['timeslot']."</td><td>"."<div style='margin: 0 auto;'><a href='dashboard.php?zrusit=".$row['id_rezervace']."' class='btn badge badge-danger'>Zrušit rezervaci</a>".
        "</div>".
        "</td></tr>";

    }


  }
  echo "</table></div>";

  }else{
  echo "<p>V databázi nejsou žádné budoucí rezervace</p></div>";
  }

  ECHO "<div class='box'><h2 class='text-left'>Všechny rezervace <span class='badge badge-pill badge-dark'>$vsechny_rezervace</h2>";

  if($vsechny_rezervace > 0){
    echo "<table class='table table-sm'>
    <thead class='thead table-dark bg-dark'>
      <tr>
        <th scope='col' width='130px' >Datum</th>
        <th scope='col'>E-mailová adresa</th>
        <th scope='col'>Jméno</th>
        <th scope='col'>Příjmení</th>
        <th scope='col'>Vyšetření</th>
        <th scope='col'>Slot</th>
      </tr>
    </thead>";

    $sql = "SELECT * FROM cRezervace as r JOIN cSluzba as s ON s.id = r.cSluzba_id JOIN cUzivatel as u ON
    u.id = r.cUzivatel_id JOIN cLekar as l ON l.id = r.cLekar_id 
    WHERE isDeleted = 0 ORDER BY date";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){

      while ($row = $result->fetch_assoc()){
        echo "<tr><td>".$row['date']."</td><td>".$row['email']."</td><td>".$row['jmeno']."</td><td>".$row['prijmeni']."</td><td>".$row['nazev'].
        "</td><td>".$row['timeslot']."</td></tr>";

    }


  }
  echo "</table></div>";

  }else{
  echo "<p>V databázi nejsou žádné rezervace</p></div>";
  }
  echo "</section>";
  }

  /*
  *******************
  DASHBOARD - PACIENT
  *******************
  */

  if($cRole_id == 3){

    ECHO "<section id='dashboard-pacient' class='wrapper-index aboutus mt-4 mb-2 py-1'>
    <div class='container'><div class='box'><h2 class='text-left'>Mé budoucí rezervace <span class='badge badge-pill badge-warning'>$me_rezervace</span></h2>";

    if($me_rezervace > 0){

      ECHO "<table class='table table-sm'>
      <thead class='thead table-dark bg-dark'>
        <tr>
          <th scope='col'>Datum</th>
          <th scope='col'>Slot</th>
          <th scope='col'>Název vyšetření</th>
          <th scope='col'>Lékař</th>
          <th scope='col'>Cena</th>
          <th scope='col'>Potvrzení</th>
        </tr>
      </thead>";

      $sql = "SELECT * FROM cRezervace as r JOIN cSluzba as s ON s.id = r.cSluzba_id JOIN cUzivatel as u ON u.id = r.cUzivatel_id 
              JOIN cLekar as l ON l.id = r.cLekar_id JOIN cUzivatel as ul ON ul.id = l.cUzivatel_id 
      WHERE r.date >= curdate() and u.id = $id_uzivatel ORDER BY date";
      $result = $conn->query($sql);

      if ($result->num_rows > 0){

        while ($row = $result->fetch_assoc()){
        echo "<tr><td>".$row['date']."</td><td>".$row['timeslot']."</td><td>".$row['nazev']."</td><td>".$row['titul']." ".$row['jmeno']." ".$row['prijmeni']."</td><td>".$row['cena']." Kč".
        "</td><td>";
        if ($row['isDeleted'] == 1){
          echo "<span class='badge badge-danger'>Zamítnuta</span>";
        }elseif ($row['isDeleted'] == 0 and $row['isValidate'] == 0){
          echo "<span class='badge badge-warning'>Nepotvrzena</span>";
        }else{
          echo "<span class='badge badge-success'>Potvrzena</span>";
        }
        echo"</td></tr>";
        }

      } 
      echo "</table></div>";
    } else {
      echo "<p>Aktuálně nemáte rezervovaný žádný termín</p></div>";
    }

    ECHO "<div class='box'><h2 class='text-left'>Proběhlé rezervace<span class='badge badge-pill badge-info'>$probehle_rezervace</span></h2>";

    if($probehle_rezervace > 0){

      ECHO "<table class='table table-sm'>
      <thead class='thead table-dark bg-dark'>
        <tr>
          <th scope='col'>Datum</th>
          <th scope='col'>Slot</th>
          <th scope='col'>Název vyšetření</th>
          <th scope='col'>Lékař</th>
          <th scope='col'>Cena</th>
        </tr>
      </thead>";

      $sql = "SELECT * FROM cRezervace as r JOIN cSluzba as s ON s.id = r.cSluzba_id JOIN cUzivatel as u ON u.id = r.cUzivatel_id 
      JOIN cLekar as l ON l.id = r.cLekar_id JOIN cUzivatel as ul ON ul.id = l.cUzivatel_id 
              WHERE r.date < curdate() and u.id = $id_uzivatel and isValidate != 0 and isDeleted != 1 ORDER BY date";
      $result = $conn->query($sql);

      if ($result->num_rows > 0){

        while ($row = $result->fetch_assoc()){
        echo "<tr><td>".$row['date']."</td><td>".$row['timeslot']."</td><td>".$row['nazev']."</td><td>".$row['titul']." ".$row['jmeno']." ".$row['prijmeni']."</td><td>".$row['cena']." Kč".
        "</td></tr>";
        }

      } 
      echo "</table></div>";
    } else {
      echo "<p>V databázi není žádná uskutečněná rezervace</p></div>";
    }
    echo "</section>";

  }



  ?>
      
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>