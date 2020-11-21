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
    }
    if($cRole_id == 1){
        $setRole = "<span class='nav-link' id='uzivatelPrihlasen' style='font-size:15px; color:lightgray; margin-right: 0.5em;'>".$email."<span> </span><span class='badge badge-pill badge-danger'>Administrátor</span></span>";
        $logButton = "<a href='index.php?logout=1' class='btn-outline-logout'>Odhlásit</a>";
    }elseif($cRole_id == 2){
        $setRole = "<span class='nav-link' id='uzivatelPrihlasen' style='font-size:15px; color:lightgray; margin-right: 0.5em;'>".$email."<span> </span><span class='badge badge-pill badge-warning'>Lékař</span></span>";
        $logButton = "<a href='index.php?logout=1' class='btn-outline-logout'>Odhlásit</a>";
    }elseif($cRole_id == 3){
        $setRole = "<span class='nav-link' id='uzivatelPrihlasen' style='font-size:15px; color:lightgray; margin-right: 0.5em;'>".$email."<span> </span><span class='badge badge-pill badge-primary'>Pacient</span></span>";
        $logButton = "<a href='index.php?logout=1' class='btn-outline-logout'>Odhlásit</a>";
    }else{
        $msg = "<div class='alert alert-danger'>Problém s cRole_id v databázi, kontaktujte správce</div>";
    }

}
else{
    $setRole = "<span class='nav-link' id='uzivatelPrihlasen' style='font-size:15px; color:lightgray; margin-right: 0.5em;'>Uživatel nepřihlášen</span>";
    $logButton = "<a href='login.php' class='btn-outline-login'>Přihlásit</a>";
}

if(isset($_POST['submit'])){
  $x_mail = mysqli_real_escape_string($conn, ($_POST['x_mail']));
  $x_predmet = mysqli_real_escape_string($conn, ($_POST['x_predmet']));
  $x_zprava = mysqli_real_escape_string($conn, ($_POST['x_zprava']));

  $pro = "lttriwww3@gmail.com"; // nastavíme příjemce e-mailu
  $predmet = $x_predmet;
  $zprava = $x_zprava; // samotná zpráva
  // hlavičky
  $hlavicky = 'From: '.$x_mail."\n"; // můj e-mail
  $hlavicky .= "MIME-Version: 1.0\n";
  $hlavicky .= "Content-Transfer-Encoding: QUOTED-PRINTABLE\n"; // způsob kódování
  $hlavicky .= "X-Mailer: PHP\n";
  $hlavicky .= "X-Priority: 1\n"; // priorita (1 nejvyšší, 2 velká, 3 normální ,4 nejmenší)
  $hlavicky .= 'Return-Path: <lttriwww3@gmail.com>'."\n"; // Návratová cesta pro chyby
  $hlavicky .= "Content-Type: text/plain; charset=UTF-8\n"; // Kódování
  // Nyní zbývá odeslání e-mailu a vypsání, zdali se e-mail odeslal.
  $mail = @mail($pro, $predmet, $zprava, $hlavicky);
  if($mail) $mail_suc = "<div class='alert alert-success text-center'>E-mail byl úspěšně odeslán.</div>";
  else $mail_suc = "<div class='alert alert-danger text-center'>E-mail se bohužel nepodařilo odeslat!</div>";
}

?>

<!doctype html>
<html lang="en">
  <head>
    <title>Diplomová práce - Informační systém sportovního lékaře</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/main.css" />
    <!-- Bootstrap CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <script>
        var shiftWindow = function() { scrollBy(0,-110) };
        window.addEventListener("hashchange", shiftWindow);
        function load() { if (window.location.hash) shiftWindow();}
    </script>

</head>
  <body class="body" onload="load()">

    <?php

    if(isset($_GET['logout'])){
        session_unset();
        session_destroy();
        header("location: index.php");
        exit();
    }

    ?>

    <!-- Navigace -->
        <nav class="navbar navbar-expand-lg nav-back fixed-top" id="nav">
            <div class="container">
                <a class="navbar-brand" href="#" id="logo_velke">
                    <img src="/img/logo.png" width="100" height="80" alt="">
                  </a>
                <a class="navbar-brand" href="#" id="logo_male">
                    <img src="/img/logo_male.png" width="350" height="80"  alt="">
                </a> 
                
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#myNavbar" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">+</span>
                </button>

                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="navbar-nav ml-left">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">Domů</a>
                        </li>
                        <li class="nav-item">
                            <a href="#aboutus" class="nav-link">O nás</a>
                        </li>
                        <li class="nav-item">
                            <a href="#servicies" class="nav-link">Služby</a>
                        </li>
                        <li class="nav-item">
                            <a href="#price" class="nav-link">Ceník</a>
                        </li>
                        <li class="nav-item">
                            <a href="#contact" class="nav-link">Kontakt</a>
                        </li>


                    </ul>
                </div>

                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <?php echo $setRole ?>
                        </li>

                        <li class="nav-item">
                            <?php echo $logButton ?>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    <!-- Hlavní obrázek na stránce -->

        <section id="image" class="d-flex align-items-center"> 
            <div class="container text-center position-relative">
                <!--<h1>Jste rekreační běžec, cyklista nebo triatlonista? </h1>-->
                <h1>Centrum zdraví a zdravého pohybu</h1>
                <h2 class="text-uppercase">Chcete zlepšit kondici, životní styl nebo se zbavit kil navíc?</h2>
                <?php
                    if(isset($_SESSION['user'])){
                        ECHO "<a href='dashboard.php' id='linkButton' class='btn btn-success'> 
                        Vstoupit do aplikace
                        </a>";
                    }                     
                ?>
            </div>
        </section>

    <!-- Hlavní sekce stránky ( nepřihlášený uživatel (statická) -->

    

        <section id="aboutus" class="wrapper-index aboutus mt-5 mb-2 py-3">
            <div class="container">

                    <?php

                    if(isset($mail_suc)){
                      echo $mail_suc;
                    }

                    ?>
                    

                <h2 class="text-left">O nás</h2>
                <p id="plaintext">Centrum zdraví a zdravého pohybu bylo založeno v roce 2010 jako jedno z menších specializovaných nestátních zdravotnických zařízení. Na našem pracovišti se vám věnují lékaři z oborů tělovýchovného lékařství, rehabilitace, kardiologie a vnitřního lékařství, kteří úzce spolupracující s týmem fyzioterapeutů. Jsme vybaveni moderními diagnostickými i terapeutickými přístroji. Staráme se o širokou veřejnost i profesionální sportovce.

                </p>   
                <p id="plaintext">Ať už hledáte pomoc pro úlevu od bolesti, zlepšení tělesné kondice či zvýšení úrovně sportovního výkonu, obraťte se na naše specialisty. 
                </p>
            </div>
        </section>

        <section id="servicies" class="wrapper-index services mt-5 mb-2 py-3">

            <div class="container">
                <div class="section-title">
                <h2 class="text-left">Služby</h2>
                <p id="plaintext">Nabízíme komplexní služby pro rekreační i výkonnostní běžce, cyklisty, triatlonisty a všechny, kdo chtějí získat kondici a zdraví. 
                </p>
                <p class="podnadpis text-left">Trénujte správně a bez rizika!</p>
                <p id="plaintext">Všechny služby (funkční vyšetření, analýzy techniky pohybu, fyzioterapii, tréninkové plány, výživové poradenství...) nabízíme samostatně nebo v oblíbených balíčcích.</p>
            </div>
                
            <div class="container">

<div class="karty">
  <div class="card-deck">
    <div class="card">
      <img src="/img/run.jpg" class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Běh</h5>
        <p class="card-text">Služby pro rekreační i výkonnostní běžce</p>
        <ul style="list-style-type:square ;">
          <li>Vyšetření laktátové křivky</li>
          <li>Klidové i zátěžové EKG, klidová spirometrie</li>
          <li>Určení bezrizikových tréninkových zón</li>
          <li>Kontrola techniky běhu</li>
          <li>Individuální tréninkovýn plán</li>
        </ul>
      </div>
      <div class="card-footer">
        <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#beh">Zjistit více</button>
      </div>
    </div>

    <div class="card">
      <img src="/img/bike.jpg" class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Cyklistika a Triatlon</h5>
        <p class="card-text">Funkční vyšetření, tréninkové plány...</p>
        <ul style="list-style-type:square ;">
          <li>Vyšetření laktátové křivky</li>
          <li>Klidové i zátěžové EKG, klidová spirometrie</li>
          <li>Určení bezrizikových tréninkových zón</li>
          <li>Vyšetření srdce a laktátové křivky + klidová spirometrie</li>
          <li>Individuální tréninkovýn plán</li>
        </ul>
      </div>
      <div class="card-footer">
        <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#bike">Zjistit více</button>
      </div>
    </div>
    
  </div>
</div>

<div class="karty">
  <div class="card-deck">

    <div class="card">
      <img src="/img/yoga.jpg" class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Pohyb, cvičení a fitness</h5>
        <p class="card-text">Individuální cvičení s trenérem...</p>
        <ul style="list-style-type:square ;">
          <li>Kruhový trénink</li>
          <li>Cvičení zaměřené na střed těla</li>
          <li>Kompenzace sedavého zaměstnání</li>
        </ul>
      </div>
      <div class="card-footer">
        <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#yoga">Zjistit více</button>
      </div>
    </div>

    <div class="card">
      <img src="/img/fyzio.jpg" class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Fyzioterapie & Léčba pohybem</h5>
        <p class="card-text">Bolí Vás záda, krční páteř.. ?</p>
        <ul style="list-style-type:square ;">
          <li>Komplexní vyšetření pohybového aparátu včetně zaměření na konkrétní sportovní disciplínu</li>
          <li>Léčbu bolestí pohybového aparátu od plosek nohou až po hlavu</li>
          <li>Zlepšení kvality pohybových stereotypů</li>
          <li>Možnost i sestavení rozcvičky a strečinku na míru</li>
        </ul>
      </div>
      <div class="card-footer">
        <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#fyzio">Zjistit více</button>
      </div>
    </div>

  </div>
</div>

<div class="karty">
  <div class="card-deck">

    <div class="card">
      <img src="/img/vyziva.jpg" class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Výživové poradenství</h5>
        <p class="card-text">Určení tělesné skladby, individuální výživová strategie
          <ul style="list-style-type:square ;">
            <li>Základní výživové poradenství + určení tělesné skladby zdarma</li>
            <li>Výživové poradenství s kontrolní konzultací</li>
            <li>Výživové poradenství se dvěma kontrolními konzultacemi a individuálním jídelníčkem na 3 dny</li>
          </ul>
        </p>
      </div>
      <div class="card-footer">
        <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#vyziva">Zjistit více</button>
      </div>
    </div>
    <div class="card">
      <img src="/img/lifestyle.jpg" class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Poradenství v oblastni životního stylu</h5>
        <p class="card-text">
          <ul style="list-style-type:square ;">
            <li>Poradenství v oblasti životního stylu</li>
            <li>Určení tělesné skladby</li>
            <li>Výživové poradenství</li>
          </ul>
        </p>
      </div>  
      <div class="card-footer">
        <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#lifestyle">Zjistit více</button>
      </div>
    </div>
  </div>
</div>

</div>

<!--  Modal okna ( informace o jednotilivých službách ) -->

<div class="modal fade" id="bike" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Cyklistika a Triatlon</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
    Výsledky funkčního vyšetření (laktátové křivky) pomohou poměrně spolehlivě určit vaše tréninkové zóny vzhledem k tepové frekvenci a výkonu tak, abyste se jako začátečníci na kole jen netrápili a skutečně spalovali tuky, popř. zvyšovali kondici anebo jako zkušenější cyklisté optimálně řídili svůj trénink. 
    Během jedné návštěvy získáte velmi cenné informace pro řízení tréninku a současně vyloučíme i možná zdravotní rizika (kolaps oběhové soustavy, srdeční arytmie, infarkt myokardu). Ve spolupráci s našimi předními kardiology zjistíme, na co vaše srdce má a co už je pro něj momentálně nadprahové zatížení. Doporučené tréninkové zóny vám sdělíme v hodnotách tepové frekvence i výkonu ve wattech. Současně tak minimalizujete zdravotní riziko a maximalizujete účinnost tréninku. Správně řízený trénink by měl srdce přiměřeně posilovat a zlepšovat jeho funkci a ne ho opakovaně přetěžovat!               </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavřít</button>
  </div>
</div>
</div>
</div>

<div class="modal fade" id="yoga" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Pohyb, cvičení a fitness</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
    V našem centru chápeme pohyb jako cestu k radosti, osobní pohodě a zdraví. Aby tomu tak skutečně bylo, nabízíme individuální přístup a 
    péči s respektováním všech přání a specifik Vaší osobnosti. Naši trenéři Vám pomohou s výběrem a doporučí pro Vás optimální pohybovou aktivitu. Sami potom dohlédnou na to, abyste cvičili správně, aby bylo cvičení efektivní a skutečně zdraví prospěšné. Z forem pohybových aktivit preferujeme cvičení s vlastním tělem nebo jednoduchým náčiním (TRX, bosu apod.). Samozřejmostí je průběžné sledování skladby Vašeho těla na přístroji              </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavřít</button>
  </div>
</div>
</div>
</div>

<div class="modal fade" id="fyzio" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Fyzioterapie a Léčba pohybem</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
    V našem centru vám pomůžeme i v případě, že vaše tělo nefunguje tak, 
    jak má. Vhodně indikovaný pohyb má i léčebné účinky. Důraz při tom 
    klademe na správnou diagnostiku, tedy zjištění pravé příčiny problému. 
    Jen tak může být úspěšná i následná terapie bez toho, 
    že by se problémy vracely znovu a znovu. I v oblasti fyzioterapie je
    pro nás prioritní komplexní přístup spočívající v možnosti spolupráce s psychoterapeutem, 
    fitness trenérem a zátěžovou diagnostikou.              
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavřít</button>
  </div>
</div>
</div>
</div>

<div class="modal fade" id="vyziva" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Výživové poradenství</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
    Délka konzultace s výživovým poradcem představuje cca 60 - 90 min. Kontrolní návštěva s vyhodnocením efektivity výživové strategie 
    a její případnou úpravou má platnost dva měsíce od prvního vyšetření.              
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavřít</button>
  </div>
</div>
</div>
</div>

<div class="modal fade" id="lifestyle" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Poradenství v oblastni životního stylu</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
    Cílem poradenství, společné práce terapeuta a 
    klienta je nalézt výhodnější způsoby, 
    strategie v každodenních  úkonových stereotypech, v řešení problémů - 
    menších i větších,  hledat rezervy v různých oblastech, které by pak 
    zlepšily  kvalitu života klienta.              
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavřít</button>
  </div>
</div>
</div>
</div>

<div class="modal fade" id="beh" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Běh</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
    Běh pro člověka mnohdy představuje vysoké zatížení pohybového aparátu. Aby nedocházelo k jeho poškození a zbytečným zdravotním problémům je potřeba běhat správně technicky. Balíček služeb vedle vyšetření pohybového aparátu zkušeným fyzioterapeutem obsahuje právě i analýzu techniky běhu. Balíček je vhodný jako prevence, ale i v případě, že vás již něco bolí, kdy se snažíme zjistit skutečnou příčinu problému a navrhnout opatření k jejímu odstranění.
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavřít</button>
  </div>
</div>
</div>
</div>

            </div>
        </section>

        <section id="price" class="wrapper-index price mt-5 mb-2 py-3">
            <div class="container">
                <h2 class="text-left">Ceník</h2>
                <table class="table table-sm table-striped">
                    <thead class="thead-dark"> 
                      <tr>
                        <th>Služba</th>
                        <th>Cena</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Poradenství v oblasti životního stylu</td>
                        <td>1200,-</td>
                      </tr>
                      <tr>
                        <td>Funkční diagnostika - vyšetření laktátové křivky + analýza tělesné skladby + konzultace</td>
                        <td>1900,-</td>
                      </tr>
                      <tr>
                        <td style="color:red;font-style: italic;">Funkční diagnostika - vyšetření laktátové křivky - opakované vyšetření do 12 měsíců</td>
                        <td>1700,-</td>
                      </tr>
                      <tr>
                        <td>Vstupní vyšetření pohybového aparátu fyzioterapeutem (1 hodina)</td>
                        <td>1250,-</td>
                      </tr>
                      <tr>
                        <td>Fyzioterapie (1hodina)</td>
                        <td>1000,-</td>
                      </tr>
                      <tr>
                        <td>Fyzioterapie pro děti (30 minut)</td>
                        <td>550,-</td>
                      </tr>
                      <tr>
                        <td>Analýza tělesné skladby + konzultace</td>
                        <td>300,-</td>
                      </tr>
                      <tr>
                        <td>Modelace ortopedických stélek</td>
                        <td>1800,-</td>
                      </tr>
                      <tr>
                        <td>Základní výživové poradenství + analýza tělesné skladby</td>
                        <td>1600,-</td>
                      </tr>
                      <tr>
                        <td>Výživové poradenství s kontrolní konzultací</td>
                        <td>2900,-</td>
                      </tr>
                      <tr>
                        <td>Výživové poradneství se dvěma kontrolními konzultacemi a jídelníčkem na 3 dny</td>
                        <td>5800,-</td>
                      </tr>
                    </tbody>
                  </table>

            </div>
        </section>

        <section id="contact" class="wrapper-index contact">
            <div class="container">
                <div class="section-title text-left mt-5">
                    <h2>Kontakt</h2>
                    <p>Provozní doba: PO-PÁ 7:00 - 22:00</p>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="contact-box">
                                    <h3>Adresa</h3>
                                    <p>Poděbradská 10 <br>PSČ: 190 00 -  Praha 9</p>
                                </div>
                            </div>
                            <div class="contactbox col-md-6">
                                <div class="contact-box mt-4">
                                    <h3>Email</h3>
                                    <p>test@email.cz
                                    <br>contact@email.cz </p>
                                </div>
                            </div>
                            <div class="contactbox col-md-6">
                                <div class="contact-box mt-4">
                                    <h3>Telefon</h3>
                                    <p>+ 420 606 123 123<br>+ 420 226 123 123</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 md-2">
                        <form action="" method="post">
                            <div class="form-row">
                                <div class="col-md-6 form-group">
                                    <input type="text" name="text" class="form-control" required
                                    id="name" placeholder="Jméno">
                                </div>
                                <div class="col-md-6 form-group">
                                    <input type="email" class="form-control" name="x_mail" required
                                    id="email" placeholder="E-mail">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="x_predmet" required
                                placeholder="Předmět">
                            </div>
                            <div class="ta form-group">
                                <textarea name="x_zprava" class="form-control" rows="5" draggable="false" required></textarea>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="submit">Odeslat zprávu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer py-4 mt-5">
            <div class="container">
                <div class="row align-items-center">
                        <div class="col-lg-4 text-lg-center">
                                Copyright &copy; Lukáš Tvrz 2020
                        </div>
                        <div class="col-lg-4 my-3 my-lg-0">
                                <a href="#" class="btn btn-back btn-social mx-2">
                                    <i class="fa fa-facebook"></i>
                                </a>
                        </div>
                </div>
            </div>
        </footer>

      
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>