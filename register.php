<?php

session_start();

include 'dbconn.php';

// připojení k databázi  
$conn = new mysqli($servername,$username,$password,$database);
$conn->set_charset("utf8");

if(isset($_POST['submit'])){
    $email = $_SESSION['registrace'];
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

    if($password1 != $password2){
        $msg = "<div class='alert alert-danger text-center'>Zadaná hesla se neshodují</div>";
    }
    else{

        // kontrola síly hesla
        if ( strlen($password1 ) < 8 ) {
          $msg = "<div class='alert alert-danger text-center'>Zadané heslo je příliš krátké</div>";
        } 
        elseif (!preg_match("#[0-9]+#", $password1 )) 
        {
          $msg = "<div class='alert alert-danger text-center'>Zadané heslo musí obsahovat alespoň jedno číslo</div>";
        } 
        elseif (!preg_match("#[a-z]+#", $password1)){
          $msg = "<div class='alert alert-danger text-center'>Zadané heslo musí obsahovat alespoň jedno písmeno</div>";
        }
        elseif (!preg_match("#\W+#", $password1)){
          $msg = "<div class='alert alert-danger text-center'>Zadané heslo musí obsahovat alespoň jeden speciální znak</div>";
        }else{

          //$password1 = strtolower($password1);
        $h_password = md5($password1);
        $token = uniqid(md5(time()));
        $sql = "INSERT INTO cUzivatel(titul,jmeno,prijmeni,email,heslo,token,verify,adresa,mesto,psc,telefon,stat,cRole_id) 
                values('$titul','$name','$surname','$email','$h_password','$token',0,'$address','$city','$zip','$phone','$state','3')";

        if ($conn->query($sql) === TRUE){
              // ODESLÁNÍ VALIDAČNÍHO MAILU

              $pro = $email; // nastavíme příjemce e-mailu
              $predmet = 'Ověření registrace - Centrum zdraví a zdravého pohybu';
              $zprava = "Dobrý den,\npro ověření uživatelského účtu stačí kliknout na odkaz níže:\n "; // samotná zpráva
              $zprava .= "lttri.www3.cz/verify.php?token"."=3D".$token."\n\n\n";
              $zprava .= "Toto je automatický e-mail, neodpovídejte!\n";
              $zprava .= "Centrum zdraví a zdravého pohybu\n";
              // hlavičky
              $hlavicky = 'From: lttriwww3@gmail.com'."\n"; // můj e-mail
              $hlavicky .= "MIME-Version: 1.0\n";
              $hlavicky .= "Content-Transfer-Encoding: QUOTED-PRINTABLE\n"; // způsob kódování
              $hlavicky .= "X-Mailer: PHP\n";
              $hlavicky .= "X-Priority: 1\n"; // priorita (1 nejvyšší, 2 velká, 3 normální ,4 nejmenší)
              $hlavicky .= 'Return-Path: <lttriwww3@gmail.com>'."\n"; // Návratová cesta pro chyby
              $hlavicky .= "Content-Type: text/plain; charset=UTF-8\n"; // Kódování
              // Nyní zbývá odeslání e-mailu a vypsání, zdali se e-mail odeslal.
              $mail = @mail($pro, $predmet, $zprava, $hlavicky);
                   
              if($mail) {
                $_SESSION['reg-suc'] = "<div class='alert alert-success text-center'>Registrace proběhla v pořádku, byl zaslán validační e-mail</div>";
                header("Location: login.php");
              }
              else echo 'E-mail se bohužel nepodařilo odeslat!';
              
        }
        else{
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        }

        
    } 
}

?>

<!doctype html>
<html lang="en">
  <head>
    <title>Registrace nového uživatele</title>
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


    <div class="wrapper" id="register">
      <div class="text-center"> 
        <a href="index.php">
          <img src="/img/logo_male.png" width="300" height="75"  style="margin-bottom: 10px; margin-top: 10px;">
        </a><br><label class="smaller">Registrace nového uživatele</label>

      </div>

        <?php

            echo $msg;

        ?>

      <form class="form-signin" method="post"> 
        <div class="form-group">

          <label for="login">Přihlašovací jméno</label>
          <input type="email" required class="form-control" id="login" name="email" value="<?php echo $_SESSION['registrace'] ?>" disabled>

        </div>
        
        <div class="form-row">
        <div class="form-group col-md-2">
            <label for="name">Titul</label>
            <input type="text" class="form-control" id="titul" name="titul" placeholder="titul" value="<?php if(isset($titul)){echo $titul;} ?>">
          </div>
          <div class="form-group col-md-5">
            <label for="name">Jméno *</label>
            <input type="text" required class="form-control" id="name" name="name" placeholder="jméno" value="<?php if(isset($name)){echo $name;} ?>">
          </div>
          <div class="form-group col-md-5">
            <label for="surname">Příjmení *</label>
            <input type="text" required class="form-control" id="surname" name="surname" placeholder="příjemní" value="<?php if(isset($surname)){echo $surname;} ?>">
          </div>

        </div> 

        <div class="form-row">

          <div class="form-group col-md-6">
            <label for="password">Heslo *</label>
            <input type="password" required class="form-control" id="password" name="password1" placeholder="heslo">
          </div>

          <div class="form-group col-md-6">
            <label for="password_2">Ověření hesla *</label>
            <input type="password" required class="form-control" id="password_2" name="password2" placeholder="ověření hesla">
          </div>

        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="address">Adresa *</label>
            <input type="text" required class="form-control" id="address" name="address" placeholder="adresa" value="<?php if(isset($address)){echo $address;} ?>">
            </div>
            
            <div class="form-group col-md-6">
            
            <label for="address">Telefon *</label>
            <div class="input-group-prepend">
                <div class="input-group-text">+420</div>
                <input type="text" required class="form-control" id="phone" maxlength="9" name="phone" placeholder="telefon" value="<?php if(isset($phone)){echo $phone;} ?>">
            </div>
            </div>

        </div>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="city">Město *</label>
            <input type="text" required class="form-control" id="city" name="city" placeholder="město" value="<?php if(isset($city)){echo $city;} ?>">
          </div>

         
          <div class="form-group col-md-5">
            <label for="state">Stát *</label>
            <input type="text" required class="form-control" id="state" name="state" placeholder="stát" value="<?php if(isset($state)){echo $state;} ?>">
          </div>
          
          <div class="form-group col-md-3">
            <label for="zip">PSČ *</label>
            <input type="text" required class="form-control" id="zip" maxlength="5" name="zip" placeholder="psč" value="<?php if(isset($zip)){echo $zip;} ?>">
          </div>
        </div>

        <!-- <div class="g-recaptcha" data-sitekey="your_site_key"></div> -->
       
        <input type="submit" name="submit" class="btn-outline-register btn-block" value="Registrovat">

      </form>
            
      <div class="login-page-div text-center"> 

          <a href="verifyRegistration.php" class="smaller">Zpět</a><br>
          <span class="smaller" style="color: red;"><i>(*) Povinné</i></span>
          
      </div>

    </div>
      
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>
