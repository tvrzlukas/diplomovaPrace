<?php

include 'dbconn.php';

$mysqli = new mysqli($servername,$username,$password,$database);

if(isset($_GET['date'])){
    $date = $_GET['date'];
    $stmt = $mysqli->prepare("select * from bookings where date = ?");
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
    $stmt = $mysqli->prepare("select * from bookings where date = ? AND timeslot = ?");
    $stmt->bind_param('ss',$date,$timeslot);
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows >0){
            $msg = "<div class='alert alert-danger text-center'>Rezervace již existuje</div>";

        }else{
            $stmt = $mysqli->prepare("insert into bookings (name,timeslot,email,date) values (?,?,?,?)");
            $stmt->bind_param('ssss',$name,$timeslot,$email,$date);
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
          <form class="form-inline my-2 my-lg-0">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <span style="margin-right: 0.5em;color:white;">lukas.tvrz@ssw.cz<span> </span><span class="badge badge-pill badge-warning">Lékař</span></span>
                <span style="margin-right: 0.5em;color:white; display: none;">lukas.tvrz@ssw.cz<span> </span><span class="badge badge-pill badge-primary">Pacient</span></span>
                <span style="margin-right: 0.5em;color:white; display: none;>lukas.tvrz@ssw.cz<span> </span><span class="badge badge-pill badge-danger">Administrátor</span></span>
            </li>
            </ul>
            <button class="btn btn-outline-danger my-2 my-sm-0" type="submit">Odhlásit</button>
          </form>
        </div>
    </nav>

        <div class="book">
            <h1 class="text-center">Vytvoření rezervace pro den: [<?php echo date('d/m/Y', strtotime($date)); ?>]</h1>
            
            <div class="col-md-12">
                <?php

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
                    <button class="btn btn-danger btn-sm book"><?php echo $ts; ?></button>
                <?php }else{ ?> 
                    <button class="btn btn-success btn-sm book" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?></button>
                    <?php } ?>
                <?php } ?>
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
                                        <input type="text" class="form-control" readonly require name="timeslot" id="timeslot">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Jméno</label><br>
                                        <input type="text" class="form-control" required name="name" id="name">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Email</label><br>
                                        <input type="email" class="form-control" required name="email" id="email">
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
            $(".book").click(function(){
                var timeslot = $(this).attr('data-timeslot');
                $("#slot").html(timeslot);
                $("#timeslot").attr('value',timeslot);
                $("#myModal").modal("show");
            })
            
        </script>
    </body>

</html>