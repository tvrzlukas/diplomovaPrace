<?php

function build_calendar($month, $year){

    $servername = "free1db.zikum.cz";
    $username = "lttri.www3.cz";
    $password = "37fO*T2Wis4]wG";
    $database = "lttri_www3_cz";

    $mysqli = new mysqli($servername,$username,$password,$database);
    $stmt = $mysqli->prepare("select * from bookings where MONTH(date) = ? AND YEAR(date) = ?");
    $stmt->bind_param('ss',$month,$year);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows >0){
            while($row = $result->fetch_assoc()){
                $bookings[] = $row['date'];
            }
            $stmt->close();
        }
    }

    $daysOfWeek = array('Pondělí','Úterý','Středa','Čtvrtek','Pátek','Sobota','Neděle');
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
        switch ($monthName){
            case "January":
                $monthName = "Leden";
                break;
            case "February":
                $monthName = "Únor";
                break;
            case "March":
                $monthName = "Březen";
                break;
            case "April":
                $monthName = "Duben";
                break;
            case "May":
                $monthName = "Květen";
                break;
            case "June":
                $monthName = "Červen";
                break;
            case "July":
                $monthName = "Červenec";
                break;
            case "August":
                $monthName = "Srpen";
                break;
            case "September":
                $monthName = "Září";
                break;
            case "October":
                $monthName = "Říjen";
                break;
            case "November":
                $monthName = "Listopad";
                break;
            case "December":
                $monthName = "Prosinec";
                break;
        }
    $dayOfWeek = $dateComponents['wday'];
    if($dayOfWeek==0){
        $dayOfWeek = 6;
    }else{
        $dayOfWeek = $dayOfWeek-1;
    }
    $dateToday = date('Y-m-d'); 
    $prev_month = date('m',mktime(0,0,0,$month-1,1,$year));
    $prev_year = date('Y',mktime(0,0,0,$month-1,1,$year));
    $next_month = date('m',mktime(0,0,0,$month+1,1,$year));
    $next_year = date('Y',mktime(0,0,0,$month+1,1,$year));
    $calendar= "<center><h2>$monthName $year</h2>";
    $calendar.= "<a class='btn btn-primary btn-sm' href='?month=".$prev_month."&year=".$prev_year."'>Minulý měsíc</a>";
    $calendar.= " <a class='btn btn-dark btn-sm' href='?month=".date('m')."&year=".date('Y')."'>Aktuální měsíc</a> ";
    $calendar.= "<a class='btn btn-primary btn-sm' href='?month=".$next_month."&year=".$next_year."'>Příští měsíc</a></center><br>";

    $calendar.= "<table class='table-cal table-bordered'>";
    $calendar.= "<tr>";
    foreach($daysOfWeek as $day){
        $calendar.= "<th class='header table-dark bg-dark'>$day</th>";
    }
    $calendar.= "</tr><tr>";
    $currentDay = 1;
    if($dayOfWeek > 0){
        for($k=0;$k<$dayOfWeek;$k++){
            $calendar.= "<td class='empty'></td>";
        }
    }
    $month = str_pad($month,2,"0", STR_PAD_LEFT);

    while($currentDay <= $numberDays){

        if ($dayOfWeek==7){
            $dayOfWeek = 0;
            $calendar.= "</tr><tr>";
        }

        $currentDayRel = str_pad($currentDay,2,"0",STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $dayName = strtolower(date('I',strtotime($date)));

        $eventNum = 0;
        $today = $date==date('Y-m-d') ? 'today': ''; 
        if($date<date('Y-m-d')){
                $calendar.= "<td class='$today'><h5>$currentDay</h5> <a href='' class='btn btn-danger btn-sm'>N/A</a></td>";
        }elseif(in_array($date, $bookings)){
                $calendar.= "<td class='$today'><h5>$currentDay</h5> <a href='' class='btn btn-danger btn-sm'>Obsazeno</a></td>";
        }else{
                $calendar.= "<td class='$today'><h5>$currentDay</h5> <a href='' class='btn btn-success btn-sm'>Rezervovat</a></td>";
        }
        
        $currentDay++;
        $dayOfWeek++;
    }
    if($dayOfWeek < 7){
        $remainingDays = 7-$dayOfWeek;
        for($i;$i<$remainingDays;$i++){
            $calendar.= "<td class='empty'></td>";
        }
    }

    $calendar.= "</tr>";
    $calendar.= "</table>";

    echo $calendar;
}

?>

<!doctype html>
<html lang="en">
  <head>
    <title>Dashboard - Rezervace</title>
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
              <a class="nav-link" href="#">Dashboard</a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="#">Rezervace <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Nastavení</a>
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

    <div class="box container">
        <div class="row">
            <div class="col-md-12">
                <?php

                    $dateComponents = getdate();
                    if(isset($_GET['month'])&&isset($_GET['year'])){
                        $month = $_GET['month'];
                        $year = $_GET['year'];
                    }else{
                        $month = $dateComponents['mon'];
                        $year = $dateComponents['year'];
                    }
                    echo build_calendar($month,$year);

                ?>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>