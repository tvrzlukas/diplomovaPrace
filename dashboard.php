<?php

session_start();

include 'dbconn.php';

// připojení k databázi  
$conn = new mysqli($servername,$username,$password,$database);

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

    <!-- DASHBOARD DOCTOR -->
    <?php
    echo $msg;
    ?>

      <section id="dashboard-doctor" style="display: inline;" class="wrapper-index aboutus mt-4 mb-2 py-1">
        <div class="dashboard">

          <div class="box">
            <h2 class="text-left">Nepotvrzené rezervace <span class="badge badge-pill badge-danger">2</span></h2>
            
            <!--  pokud se v databázi nenachází žádná data s prefixem "kPotvrzeni = 1" zobrazí se hláška že 
            v databázi není žádná rezervace k potvrzení -->

            <p style="display: none;">Žádné rezervace k potvrzení</p>
            
            <!--  pokud sql select vrátí data, zobrazit do tabulky -->

            <table class="table table-sm">
              <thead class="thead table-dark bg-dark">
                <tr>
                  <th scope="col">Datum</th>
                  <th scope="col">E-mailová adresa</th>
                  <th scope="col">Jméno</th>
                  <th scope="col">Příjmení</th>
                  <th scope="col">Vyšetření</th>
                  <th scope="col">Slot</th>
                  <th scope="col" width="200px">Akce</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2020-03-26</td>
                  <td>lukas.tvrz@ssw.cz</td>
                  <td>Lukáš</td>
                  <td>Tvrz</td>
                  <td>Fyzioterapie</td>
                  <td>14:50PM - 15:00PM</td>
                  <td style="text-align: center;">  
                      <button id="1" type="button" class="btn badge badge-success">Potvrdit</button>
                      <button id="1" type="button" class="btn badge badge-danger">Zamítnout</button>
                  </td>
                </tr>
                <tr>
                  <td>2020-03-28</td>
                  <td>test@ssw.cz</td>
                  <td>Jan</td>
                  <td>Novák</td>
                  <td>Výživové poradenství</td>
                  <td>10:20AM - 10:30AM</td>
                  <td style="text-align: center;">  
                      <button id="1" type="button" class="btn badge badge-success">Potvrdit</button>
                      <button id="1" type="button" class="btn badge badge-danger">Zamítnout</button>
                  </td>
                </tr>
              </tbody>
            </table>

          </div>

          <div class="box">

            <h2 class="text-left">Budoucí rezervace <span class="badge badge-pill badge-success">2</h2>

              <!--  Zobrazí všechny budoucí rezervace ( den bude >= dnešní den ) -->
              
  
              <table class="table table-sm">
                <thead class="thead table-dark bg-dark">
                  <tr>
                    <th scope="col">Datum</th>
                    <th scope="col">E-mailová adresa</th>
                    <th scope="col">Jméno</th>
                    <th scope="col">Příjmení</th>
                    <th scope="col">Vyšetření</th>
                    <th scope="col">Slot</th>
                    <th scope="col" width="200px">Akce</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>2020-03-26</td>
                    <td>lukas.tvrz@ssw.cz</td>
                    <td>Lukáš</td>
                    <td>Tvrz</td>
                    <td>Fyzioterapie</td>
                    <td>14:50PM - 15:00PM</td>
                    <td style="text-align: center;">   
                      <button id="1" type="button" class="btn badge badge-danger">Zrušit rezervaci</button>
                    </td>
                  </tr>
                  <tr>
                    <td>2020-03-28</td>
                    <td>test@ssw.cz</td>
                    <td>Jan</td>
                    <td>Novák</td>
                    <td>Fyzioterapie</td>
                    <td>10:20AM - 10:30AM</td>
                    <td style="text-align: center;">  
                      <button id="1" type="button" class="btn badge badge-danger">Zrušit rezervaci</button>
                    </td>
                  </tr>
                
                </tbody>
              </table>

              <!--  V případě, že v databázi není žádná budoucí POTVRZENÁ rezervace, vypíše informaci že žádná
              rezervace neexistuje  -->

              <p style="display: none;">Žádné budoucí rezervace</p>

          </div>

          <div class="box">

              <h2 class="text-left">Všechny rezervace <span class="badge badge-pill badge-dark">1</h2>
  
              <!--  Zobrazí všechny rezervace které se nachází v db -->
  
              <table class="table table-sm">
                <thead class="thead table-dark bg-dark">
                  <tr>
                    <th scope="col">Datum</th>
                    <th scope="col">E-mailová adresa</th>
                    <th scope="col">Jméno</th>
                    <th scope="col">Příjmení</th>
                    <th scope="col">Vyšetření</th>
                    <th scope="col">Slot</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>2020-03-26</td>
                    <td>lukas.tvrz@ssw.cz</td>
                    <td>Lukáš</td>
                    <td>Tvrz</td>
                    <td>Fyzioterapie</td>
                    <td>14:50PM - 15:00PM</td>
                  </tr>
                  
                </tbody>
              </table>
  
              <!--  V případě, že v databázi není žádná rezervace, vypíše o tom příslušnou hlášku  -->
  
              <p style="display: none;">Žádné rezervace v databázi</p>
          </div>
        </div>
        
      </section>

    <!-- DASHBOARD PACIENT -->
    
      <section id="dashboard-pacient" style="display: none;" class="wrapper-index aboutus mt-4 mb-2 py-1">
        <div class="container">          

          <div class="box">
            <h2 class="text-left">Mé rezervace <span class="badge badge-pill badge-warning">2</span></h2>

            <!--  pokud se v databázi nenachází žádná data s prefixem "kPotvrzeni = 1" zobrazí se hláška že 
            v databázi není žádná rezervace k potvrzení -->

            <p style="display: none;">Aktuálně nemáte rezervovaný žádný termín</p>
          
            <!--  pokud sql select vrátí data, zobrazit do tabulky -->
  
            <table class="table table-sm">
              <thead class="thead table-dark bg-dark">
                <tr>
                  <th scope="col">Datum</th>
                  <th scope="col">Slot</th>
                  <th scope="col">Název vyšetření</th>
                  <th scope="col">Cena</th>
                  <th scope="col">Potvrzení</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2020-03-26</td>
                  <td>14:50PM - 15:00PM</td>
                  <td>Fyzioterapie</td>
                  <td>1000 Kč</td>
                  <td>
                    <span class="badge badge-success">Potvrzena</span>
                  </td>
                </tr>
                <tr>
                  <td>2020-03-26</td>
                  <td>14:50PM - 15:00PM</td>
                  <td>Fyzioterapie</td>
                  <td>1000 Kč</td>
                  <td>
                    <span class="badge badge-danger">Zamítnuta</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="box">

          <h2 class="text-left">Proběhlé rezervace <span class="badge badge-pill badge-info">2</h2>

          <!--  Zobrazí všechny budoucí rezervace ( den bude >= dnešní den ) -->
          
          <table class="table table-sm">
            <thead class="thead table-dark bg-dark">
              <tr>
                <th scope="col">Datum</th>
                <th scope="col">Slot</th>
                <th scope="col">Název vyšetření</th>
                <th scope="col">Cena</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>2020-03-26</td>
                <td>14:50PM - 15:00PM</td>
                <td>Fyzioterapie</td>
                <td>1000 Kč</td>
              </tr>
              <tr>
                <td>2020-03-26</td>
                <td>14:50PM - 15:00PM</td>
                <td>Fyzioterapie</td>
                <td>1000 Kč</td>
              </tr>
            </tbody>
          </table>
            
    

          <p style="display: none;">V databázi neexistuje žádná rezervace s </p>
          
        </div>

          
      </div>
    </section>
      
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>