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
      $cRole_id = $row['cRole_id'];
    }
    if($cRole_id == 1){
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-danger'>Administrátor</span></span>";
    }elseif($cRole_id == 2){
        header("location: dashboard.php");
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-warning'>Lékař</span></span>";
    }elseif($cRole_id == 3){
        header("location: dashboard.php");
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-primary'>Pacient</span></span>";
    }else{
        $msg = "<div class='alert alert-danger'>Problém s cRole_id v databázi, kontaktujte správce</div>";
    }
}
else{
    header("location: index.php");
}

if(isset($_POST['vlozitUzivatel'])){
    $u_email = mysqli_real_escape_string($conn, ($_POST['u_email']));
    $titul = mysqli_real_escape_string($conn, ($_POST['titul']));
    $jmeno = mysqli_real_escape_string($conn, ($_POST['jmeno']));
    $prijmeni = mysqli_real_escape_string($conn, ($_POST['prijmeni']));
    $heslo1 = mysqli_real_escape_string($conn, ($_POST['heslo1']));
    $heslo2 = mysqli_real_escape_string($conn, ($_POST['heslo2']));
    $adresa = mysqli_real_escape_string($conn, ($_POST['adresa']));
    $telefon = mysqli_real_escape_string($conn, ($_POST['telefon']));
    $mesto = mysqli_real_escape_string($conn, ($_POST['mesto']));
    $stat = mysqli_real_escape_string($conn, ($_POST['stat']));
    $psc = mysqli_real_escape_string($conn, ($_POST['psc']));
    $role = $_POST['role'];

    $sql = "SELECT * FROM cUzivatel WHERE email = '$u_email'";
    $run = mysqli_query($conn, $sql);
    if(mysqli_num_rows($run)>0){
        $msg = "<div class='alert alert-danger text-center'>Uživatel s touto emailovou adresou již existuje</div>";
    }
    else{
        if($heslo1 != $heslo2){
            $msg = "<div class='alert alert-danger text-center'>Zadaná hesla se neshodují</div>";
        }
        else{
            $heslo1 = md5($heslo1);
            $stmt = $conn->prepare("INSERT INTO cUzivatel (email,titul,jmeno,prijmeni,heslo,adresa,telefon,mesto,stat,psc,cRole_id,verify) values (?,?,?,?,?,?,?,?,?,?,?,1)");
            $stmt->bind_param('sssssssssss',$u_email,$titul,$jmeno,$prijmeni,$heslo1,$adresa,$telefon,$mesto,$stat,$psc,$role);
            $stmt->execute();
            $_SESSION['ins-suc'] = "<div class='alert alert-success text-center'>Uživatel úspěšně vložen do databáze</div>";
            header("location: dashboard.php");

            //$sql = "INSERT INTO cUzivatel(email,titul,jmeno,prijmeni,heslo,adresa,telefon,mesto,stat,psc,cRole_id,verify)
            //values('$u_email','$titul','$jmeno,'$prijmeni','$heslo1','$adresa','$telefon','$mesto','$stat','$psc','$role','1')";
            //if ($conn->query($sql) === TRUE){
            //    $_SESSION['ins-suc'] = "<div class='alert alert-success text-center'>Uživatel úspěšně vložen do databáze</div>";
            //    header("location: dashboard.php");
            //}
        }

    }
}

if(isset($_POST['vlozitSpecializace'])){
    $specializace = mysqli_real_escape_string($conn, ($_POST['specializace']));
    $kontrolaSp = "SELECT * FROM cSpecializace WHERE nazevSpecializace = '$specializace'";
    $run = mysqli_query($conn, $kontrolaSp);
    if(mysqli_num_rows($run)>0){
        $msg = "<div class='alert alert-danger text-center'>Specializace s tímto názvem již existuje</div>";
    }else{
        $sql = "INSERT INTO cSpecializace(nazevSpecializace)
                values('$specializace')";
        if ($conn->query($sql) === TRUE){
            $_SESSION['ins-suc'] = "<div class='alert alert-success text-center'>Specializace úspěšně vložena do databáze</div>";
            header("Location: dashboard.php");}
    }
}

if(isset($_POST['vlozitSluzba'])){
    $sluzba = mysqli_real_escape_string($conn, ($_POST['sluzba']));
    $sluzbaCena = mysqli_real_escape_string($conn, ($_POST['sluzbaCena']));
    $kontrolaS = "SELECT * FROM cSluzba WHERE nazev = '$sluzba'";
    $run = mysqli_query($conn, $kontrolaS);
    if(mysqli_num_rows($run)>0){
        $msg = "<div class='alert alert-danger text-center'>Služba se zadaným názvem již existuje</div>";
    }else{
        $sql = "INSERT INTO cSluzba(nazev,cena,cSpecializace_id)
                values('$sluzba','$sluzbaCena','1')";
        if ($conn->query($sql) === TRUE){
            $_SESSION['ins-suc'] = "<div class='alert alert-success text-center'>Služba úspěšně vložena do databáze</div>";
            header("Location: dashboard.php");}
    }
}

if(isset($_POST['priraditLekare'])){
    $lekar = mysqli_real_escape_string($conn, ($_POST['lekar']));
    $lekarSpecializace = mysqli_real_escape_string($conn, ($_POST['lekarSpecializace']));

    $sql5 = "SELECT * FROM cLekar where cUzivatel_id = $lekar";
    $run = mysqli_query($conn, $sql5);
    if(mysqli_num_rows($run)>0){
        $sql6 = "UPDATE cLekar SET cSpecializace_id = $lekarSpecializace WHERE cUzivatel_id = $lekar";
        if ($conn->query($sql6) === TRUE){
            $_SESSION['ins-suc'] = "<div class='alert alert-success text-center'>Lékaři [id: ".$lekar."] byla specializace úspěšně změněna</div>";
            header("Location: dashboard.php");}
    }else{
        $sql7 = "INSERT INTO cLekar (cSpecializace_id,cUzivatel_id) values ('$lekarSpecializace','$lekar')";
        if ($conn->query($sql7) === TRUE){
            $_SESSION['ins-suc'] = "<div class='alert alert-success text-center'>Lékaři [id: ".$lekar."] byla specializace úspěšně vložena do databáze</div>";
            header("Location: dashboard.php");}
    }
}

// načtení Specializací do pole -> pro vybrání při přidání nové služby:

    $sql3 = "SELECT * FROM cSpecializace";
    $result3 = mysqli_query($conn, $sql3);
    $spArray = array();
    if(mysqli_num_rows($result3)>0){
        while($a = mysqli_fetch_assoc($result3)){
            $spArray[] = $a;
        }
    }

// načtení Uživatelů( lékařů ) do pole -> pro přiřazení specializace

    $sql4 = "SELECT * FROM cUzivatel WHERE cRole_id = 2";
    $result4 = mysqli_query($conn, $sql4);
    $lekarArray = array();
    if(mysqli_num_rows($result4)>0){
        while($b = mysqli_fetch_assoc($result4)){
            $lekarArray[] = $b;
        }
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <title>Dashboard - Vložení nových dat do databáze</title>
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
            <li class="nav-item active">
              <a class="nav-link" href="dashboard.php">Dashboard<span class="sr-only">(current)</span></a>
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

    <div class="container">

        <div class="box">

            
            <form method="post">

            <?php

                if(isset($_GET['insert'])){

                    if($_GET['insert'] == 1){
                        echo "<h2 class='text-left'>Vložení nového záznamu do tabulky [cUzivatel]</h2>";
                        ?>
                        <?php

                            if(isset($msg)){
                                echo $msg;
                            }

                        ?>
                        <div class="form-group"><label for="">Emailová adresa </label><input type="email" name="u_email" required class="form-control" value="<?php echo $u_email;?>" id=""></div>
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
                                    <div class="col-md-6 mb-3">
                                        <label for="">Heslo</label><input type="password" name="heslo1"  class="form-control" required value="" id="">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">Kontrola hesla </label><input type="password" name="heslo2" required class="form-control" value="" id="">
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
                                        <label for="">Typ účtu </label> 
                                        <select class="form-control" name="role">
                                            <option value="2">Lékař</option>
                                            <option value="3">Pacient</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                
                <input class="btn btn-success" name="vlozitUzivatel" type="submit" value="Vložit" >
                <a href="dashboard.php" name="back" class="btn btn-outline-danger my-2 my-sm-0">Zpět</a>

                
                <?php

                    }elseif($_GET['insert'] == 2){
                        echo "<h2 class='text-left'>Vložení nového záznamu do tabulky [cSpecializace]</h2>";

                        ?>

                        <?php

                        if(isset($msg)){
                            echo $msg;
                        }

                        ?>

                        <div class="form-group"><label for="">Název specializace </label><input type="text" name="specializace" required class="form-control" value="<?php echo $specializace;?>" id=""></div>

                        <input class="btn btn-success" name="vlozitSpecializace" type="submit" value="Vložit" >
                        <a href="dashboard.php" name="back" class="btn btn-outline-danger my-2 my-sm-0">Zpět</a>

                        <?php

                    }elseif($_GET['insert'] == 3){
                        echo "<h2 class='text-left'>Vložení nového záznamu do tabulky [cSluzba]</h2>";

                        ?>

                        <?php

                        if(isset($msg)){
                            echo $msg;
                        }

                        ?>

                        <div class="form-group"><label for="">Název služby </label><input type="text" name="sluzba" required class="form-control" value="<?php echo $sluzba;?>" id=""></div>

                        <div class="form-group"><label for="">Cena </label><input type="text" name="sluzbaCena" required class="form-control" value="<?php echo $sluzbaCena;?>" id=""></div>
                        
                        <div class="form-group"><label for="">Přiřadit specializaci </label>
                        
                        <select class="form-control" name="sluzbaSpecializace">
                            <?php 
                            
                                 foreach($spArray as $option){
                                     echo "<option value='".$option['id']."'>".$option['nazevSpecializace']."</option>";
                                 }                           
                            
                            ?>
                        </select>
                        
                        </div>

                        <input class="btn btn-success" name="vlozitSluzba" type="submit" value="Vložit" >
                        <a href="dashboard.php" name="back" class="btn btn-outline-danger my-2 my-sm-0">Zpět</a>


                        <?php

                    }
                    elseif($_GET['insert'] == 4){
                        echo "<h2 class='text-left'>Přiřazení specializace lékaři</h2>";

                        ?>

                        <?php

                        if(isset($msg)){
                            echo $msg;
                        }

                        ?>

                        <div class="form-group"><label for="">Zvolit lékaře</label>

                        <select class="form-control" name="lekar">
                            <?php 
                            
                                 foreach($lekarArray as $optionlekar){
                                     echo "<option value='".$optionlekar['id']."'>".$optionlekar['titul']." ".$optionlekar['jmeno']." ".$optionlekar['prijmeni']."</option>";
                                 }                           
                            
                            ?>
                        </select>
                    
                        </div>
                        
                        <div class="form-group"><label for="">Přiřadit specializaci </label>
                        
                        <select class="form-control" name="lekarSpecializace">
                            <?php 
                            
                                 foreach($spArray as $option){
                                     echo "<option value='".$option['id']."'>".$option['nazevSpecializace']."</option>";
                                 }                           
                            
                            ?>
                        </select>
                        
                        </div>

                        <input class="btn btn-success" name="priraditLekare" type="submit" value="Vložit" >
                        <a href="dashboard.php" name="back" class="btn btn-outline-danger my-2 my-sm-0">Zpět</a>


                        <?php

                    }

                }




            ?>
                
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