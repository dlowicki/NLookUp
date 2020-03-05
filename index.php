<?php
require("script.php");
?>
<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Startseite | NLookUp</title>
    <link href="style.css" rel="stylesheet">
    <script src="jquery.min.js"></script>
  </head>
  <body>
    <!--
    Created 28.02.2020
    -->
    <div id="container">
      <div id="navigation">
        <img src="img/logo2.png" width="4%">
        <div id="nav-list">
          <ul>
            <li><a href="">Test 1</a></li>
            <li><a href="">Test 2</a></li>
          </ul>
        </div>
      </div>

      <div id="main">
        <div id="main-header">
          <h2><?php if(isset($_GET['q'])) { echo $_GET['q']; } ?></h2>
           <h3>5.03.2020 00:01:00 Uhr</h3>
        </div>

        <div id="main-body">

            <?php
            if(isset($_GET['q'])){
              if($_GET['q'] == "Dashboard"){
                echo "<ul>";
                $version = parse_ini_file("versions.ini");
                foreach($version as $key => $value) {
                  echo "<li id='" . $key . "'>";
                    echo "<p>" . $key . " <b>v" . $value . "</b></p>";
                    echo '<div class="icon3 icon"><img src="img/muell.svg"></div>
                          <div class="icon2 icon" onClick="downloadFile(';
                    echo  "'" . $key . "'";
                    echo  ')"><img src="img/herunterladen.svg"></div>
                          <div class="icon1 icon" onClick="updateFile(';
                    echo  "'" . $key . "'";
                    echo  ')"><img src="img/neu-laden.svg"></div>';
                  echo "</li>";
                }
                echo "</ul>";
              } else if($_GET['q'] == "Protokoll") {
                if ($fh = fopen('log.txt', 'r')) {
                    $sort = array();
                    $t = 0;
                      while (!feof($fh)) {
                          $line = fgets($fh);
                          $sort[$t] = $line;
                          $t++;
                      }
                      fclose($fh);
                      echo "<div id='main-protokoll'>";
                      for($r = sizeof($sort)-1; $r >= 0; $r--){
                        echo "<p>" . $sort[$r] . "</p>";
                      }
                      echo "</div>";
                  }

              }
            }


            ?>

        </div>

        <div id="main-sidebar">
          <ul>
            <li><a href="?q=Dashboard">Dashboard</a></li>
            <li><a href="?q=Protokoll">Protokoll</a></li>
          </ul>
        </div>
      </div>



    </div>

    <script>

      function downloadFile(gpu){
        window.location.href = "test/" + gpu + ".exe";
      }

      function updateFile(gpu) {
        document.getElementById(gpu).style.backgroundColor = "rgba(20, 70, 20, 1)";
        $.ajax({
              type: 'POST',
              url: 'script.php',
              data: { grafikkarte: gpu },
              success: function (result) {
		              document.getElementById(gpu).style.backgroundColor = "rgba(46, 125, 50, 1)";
                if(result == 0){
                  alert("Keine neue Version vorhanden");
                } else {
                  alert("Neue Version " + result + " gefunden!");
		                window.location.href=window.location.href;
                }
              }
            });
      }





    </script>
  </body>
</html>
