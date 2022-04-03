<?php
// JSON data transform

$carte = array();
$meteo = array();
$jsonString = "";

$adresse = "";

if (isset($_POST["adresse"])){
    $adresse = $_POST["adresse"];

  



    
    $link = "http://nominatim.openstreetmap.org/search?format=json&polygon=1&addressdetails=1&q=".urlencode($_POST["adresse"]); 


    $opts = array(
            'http' => array(
                      'header' => "User-Agent: unNavigateur 3.7.6\r\n"));

    $context = stream_context_create($opts);
    $jsonString = file_get_contents($link, false, $context);



    if($jsonString){
        $jsonNominatimComplet = json_decode($jsonString); 
        if($jsonNominatimComplet){
          foreach ($jsonNominatimComplet as $jsonNominatim) {

            $lat = $jsonNominatim->lat;
            $lon = $jsonNominatim->lon;

            if($lat && $lon){

                $link = "http://api.openweathermap.org/data/2.5/weather?lang=fr&units=metric&lat=$lat&lon=$lon&APPID=c3ee4d8731247df7e2c945cd6b395e5e";
                $jsonString = file_get_contents($link);
                $jsonMeteo = json_decode($jsonString);

                if($jsonMeteo){

                  $meteo[] = "<article> Le temps a {$jsonMeteo->name} : <img alt '' src = 'http://openweathermap.org/img/w/{$jsonMeteo->weather[0]->icon}.png' /> Température de {$jsonMeteo->main->temp}°C, {$jsonMeteo->weather[0]->description}. </article>";

                }
                $boundingBox = $jsonNominatim->boundingbox;
                $carte[] = "<p style='clear:both;border-top: black thin solid;margin: 2em;'> </p><iframe style='border: none;box-shadow: 1px 1px 3px black;float: left; margin: 0 2em 2em 0;width:600px; height:480px;'   src='http://www.openstreetmap.org/export/embed.html?bbox={$boundingBox[2]}%2C{$boundingBox[0]}%2C{$boundingBox[3]}%2C{$boundingBox[1]}&amp;layer=mapnik' ></iframe>";

            }
          }
        } 
    }
}






?>


<!doctype html>
<html>

<head>
  <meta charset = "utf-8" />
  <title> Cours Web UNS</title>

</head>

<body>

  <aside>
    <p>L'association adresse &rarr; coordonnées est fournie par <a href="http://nominatim.openstreetmap.org/">nominatim</a>. </p>
    <p>Les cartes sont fournies par <a href="http://www.openstreetmap.org">Open Street Map</a>.</p>
    <p>La météo est fournie par <a href="http://openweathermap.org/">openweathermap.org</a>.</p>
    <p>La licence d'utilisation pour ces services est  Creative Commons Attribution-ShareAlike (CC-BY-SA).
    </p>
  </aside>


  <form method = "post">
      <fieldset>
      <legend> Saisissez l'adresse </legend>
      <input type = "text" name="adresse" id = "adresse" value = "<?php echo $adresse;?>"/ >
      </fieldset>

  </form>

  <?php
    foreach($carte as $i => $c){
      echo $c;
      echo $meteo[$i];
    }
   ?>

 </body>
 </html>
