<?php

$email = "tvrzlukas@icloud.com";
$token = uniqid(md5(time()));;

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
if($mail) echo 'E-mail byl úspěšně odeslán.';
else echo 'E-mail se bohužel nepodařilo odeslat!';
?>