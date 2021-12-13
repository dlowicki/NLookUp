<?php
require("script.php");
if(!isset($_GET['q']))
{
  header('Location: ?q=Dashboard');
}
?>
<!DOCTYPE html>
<html lang="ger" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Startseite | NLookUp</title>
    <link href="style.css" rel="stylesheet">
    <script src="jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
  </head>
  <body>
    <!--
    Created 28.02.2020
    -->
    <div id="container">
      <div id="main">
        <div id="main-header">
          <h2>Nvidia Dashboard</h2>
          <div id="navigation">
            <a href="?q=Dashboard">Dashboard</a> <a href="?q=Protokoll">Protokoll</a>
          </div>
        </div>

        <div id="main-body">
            <?php
            if(isset($_GET['q'])){
              if($_GET['q'] == "Dashboard"){
                echo "<ul>";
                $version = parse_ini_file("versions.ini");
                foreach($version as $key => $value) {
                  echo "<li id='$key'><p>$key<b>v$value</b></p>";
                    echo '<div class="icon-container">';
                      echo '<div class="icon"><i class="fas fa-trash"></i></div>';
                      echo '<div class="icon" onClick="downloadFile('; echo  "'" . $key . "'"; echo ')"><i class="fas fa-cloud-download-alt"></i></div>';
                      echo '<div class="icon" onClick="updateFile('; echo  "'" . $key . "'"; echo ')"><i class="fas fa-redo-alt"></i></div>';
                    echo '</div>';
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
