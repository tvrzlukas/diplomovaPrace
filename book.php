<?php

session_start();

include 'dbconn.php';

$conn = new mysqli($servername,$username,$password,$database);
$conn->set_charset("utf8");

// načtení Specializací do pole 

$sql3 = "SELECT * FROM cSluzba";
$result3 = mysqli_query($conn, $sql3);
$slArray = array();
if(mysqli_num_rows($result3)>0){
    while($a = mysqli_fetch_assoc($result3)){
        $slArray[] = $a;
    }
}

if(isset($_SESSION['user'])){

    $email = $_SESSION['user']; 
    $query = "select * from cUzivatel where email = '$email'";
    $run = mysqli_query($conn, $query);
    if(mysqli_num_rows($run)>0){
      $row = mysqli_fetch_array($run);
      $j = $row['jmeno'];
      $p = $row['prijmeni'];
      $cUzivatel_id = $row['id'];
      $cRole_id = $row['cRole_id'];
    }
    if($cRole_id == 1){
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-danger'>Administrátor</span></span>";
    }elseif($cRole_id == 2){
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-warning'>Lékař</span></span>";
    }elseif($cRole_id == 3){
        $setRole = "<span style='margin-right: 0.5em;color:white;'>".$email."<span> </span><span class='badge badge-pill badge-primary'>Pacient</span></span>";
    }else{
        $alert = "<div class='alert alert-danger'>Problém s cRole_id v databázi, kontaktujte správce</div>";
    }
    $conn->close();
}
else{
    header("location: index.php");
}

include 'dbconn.php';

$mysqli = new mysqli($servername,$username,$password,$database);
$mysqli->set_charset("utf8");

if(isset($_GET['date'])){
    $date = $_GET['date'];
    $stmt = $mysqli->prepare("select * from cRezervace where date = ? and isDeleted = 0");
    $stmt->bind_param('s',$date);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows >0){
            while($row = $result->fetch_assoc()){
                $bookings[] = $row['timeslot'];
            }
            $stmt->close();
        }
    }
}

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $timeslot = $_POST['timeslot'];
    $cSluzba_id = $_POST['sluzba'];
    $cLekar_id = 1;
    //$stmt = $mysqli->prepare("select * from bookings where date = ? AND timeslot = ?");
    $stmt = $mysqli->prepare("select * from cRezervace where date = ? AND timeslot = ? and isDeleted = 0");
    $stmt->bind_param('ss',$date,$timeslot);
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows >0){
            $msg = "<div class='alert alert-danger text-center'>Rezervace již existuje</div>";

        }else{
            //$stmt = $mysqli->prepare("insert into bookings (name,timeslot,email,date) values (?,?,?,?)");
            $stmt = $mysqli->prepare("insert into cRezervace (date,timeslot,cSluzba_id,cUzivatel_id,cLekar_id) values (?,?,?,?,?)");
            $stmt->bind_param('sssss',$date,$timeslot,$cSluzba_id,$cUzivatel_id,$cLekar_id);
            $stmt->execute();
            $msg = "<div class='alert alert-success text-center'>Rezervace provedena úspěšně</div>";
            $bookings[]=$timeslot;
            $stmt->close();
            $mysqli->close();

        }
    }

}

$duration = 60;
$cleanup = 0;
$start = "08:00";
$end = "17:00";

function timeslots($duration,$cleanup,$start,$end){
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT".$duration."M");
    $cleanupInterval = new DateInterval("PT".$cleanup."M");
    $slots = array();

    for ($intStart = $start;$intStart<$end;$intStart->add($interval)->add($cleanupInterval)){
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);
        if($endPeriod>$end){
            break;
        }
        $slots[] = $intStart->format("H:iA")." - ".$endPeriod->format("H:iA");
    }

    return $slots;

}
?>

<!doctype html>
<html lang="en">
  <head>
    <title>Vytvoření nové rezervace</title>
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
        <a class="navbar-brand" href="#">Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item">
              <a class="nav-link" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="reservation.php">Rezervace <span class="sr-only">(current)</span></a>
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

        <div class="book">
            <h1 class="text-center">Vytvoření rezervace pro den: [<?php echo date('d/m/Y', strtotime($date)); ?>]</h1>
            
            <div class="col-md-12">
                <?php

                    if (isset($alert)){
                        echo $alert;
                    }

                    if (isset($msg)){
                        echo $msg;
                    }

                ?>
            </div>  

                <?php

                $timeslots  = timeslots($duration,$cleanup,$start,$end);
                foreach($timeslots as $ts){

                ?>

                <?php if(in_array($ts, $bookings)){ ?>
                    <button class="btn btn-danger btn-sm"><?php echo $ts; ?></button>
                <?php }else{ ?> 
                    <button class="btn btn-success btn-sm click" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?></button>
                    <?php } ?>
                <?php } ?>
                <div><a href="reservation.php" name="zpet" class="btn btn-sm btn-danger my-2 my-sm-0" style="margin: 5px;">Zpět do kalendáře</a></div>
        </div> 
        
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Rezervace: <span id="slot"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="">Timeslot</label><br>
                                        <input type="text" class="form-control" readonly required name="timeslot" id="timeslot">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Email</label><br>
                                        <input type="email" class="form-control" readonly required name="email" id="email" value="<?php echo $email ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Jméno</label><br>
                                        <input type="text" class="form-control" readonly required name="name" id="name" value="<?php echo $j." ".$p; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Služba</label><br>
                                        <select name="sluzba" id="sluzba" class="form-control">                            
                                            <!--<option value="1">Poradenství v oblasti životního stylu - 1200Kč</option>
                                            <option value="2">Funkční diagnostika - vyšetření laktátové křivky + analýza tělesné skladby + konzultace - 1900Kč</option>
                                            <option value="3">Funkční diagnostika - vyšetření laktátové křivky - opakované vyšetření do 12 měsíců - 1700Kč</option>
                                            <option value="4">Vstupní vyšetření pohybového aparátu fyzioterapeutem (1 hodina) - 1250Kč</option>
                                            <option value="5">Fyzioterapie (1hodina) - 1000Kč</option>
                                            <option value="6">Fyzioterapie pro děti (30 minut) - 550Kč</option>
                                            <option value="7">Analýza tělesné skladby + konzultace - 300Kč</option>
                                            <option value="8">Modelace ortopedických stélek	- 1800Kč</option>
                                            <option value="9">Základní výživové poradenství + analýza tělesné skladby - 1600Kč</option>
                                            <option value="10">Výživové poradenství s kontrolní konzultací - 2900Kč</option>
                                            <option value="11">Výživové poradneství se dvěma kontrolními konzultacemi a jídelníčkem na 3 dny - 5800Kč</option> -->
                                            
                                            <?php
                                            foreach($slArray as $option){
                                            echo "<option value='".$option['id']."'>".$option['nazev']." - (Cena: ".$option['cena']." Kč)</option>";
                                            }  
                                            ?>

                                        </select>    
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn float-right btn-primary" name="submit">Odeslat</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
            
        <script>
            $(".click").click(function(){
                var timeslot = $(this).attr('data-timeslot');
                $("#slot").html(timeslot);
                $("#timeslot").attr('value',timeslot);
                $("#myModal").modal("show");
            })
        </script>
    </body>

</html>