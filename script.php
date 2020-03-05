<?php

			//updateVersions();
    	//downloadFiles();

      if(isset($_POST['grafikkarte'])){
        echo updatedVersionOnce($_POST['grafikkarte']);
      }

    function getVersionChip($url) {

      $source = file_get_contents($url);

      $doc = new DOMDocument;
      @$doc->loadHTML($source);

      $xpath = new DOMXPath($doc);
      $classname = "tdVersion";

      $elements = $xpath->query("//*[contains(@id, '$classname')]");

      $version = "";

      foreach ($elements as $e) {
        $version = $e->ownerDocument->saveXML($e);
      }
      $tmp = explode("(", $version);
      $tmp2 = explode(")", $tmp[1]);

      return $tmp2[0];
    }




    function loadLinks() {
      updateLog("Links wurden geladen");
      return parse_ini_file("/var/www/html/nvidia/links.ini");
    }

    function loadDonwloadLinks() {
      updateLog("Download Links wurden geladen");
      return parse_ini_file("/var/www/html/nvidia/downloads.ini");
    }

    function loadVersions(){
      updateLog("Versionen wurden geladen");
      return parse_ini_file("/var/www/html/nvidia/versions.ini");
    }

    function updatedVersionOnce($gpu) {
      $link = "";
      foreach(loadLinks() as $key => $value){
        if($key == $gpu){
          $version = getVersionChip($value);
          foreach (loadVersions() as $key2 => $value2) {
            if($key2 == $gpu){
              if($value2 == $version){
                // Version schon vorhanden
                return "0";
              } else {
                updateVersions();
                downloadFiles();
                return $version;
              }
            }
          }

        }
      }




    }

    function updateVersions() {
      $versions = array();
      $string="";
      $t = 0;

      foreach(loadLinks() as $row => $key){
        $versions[$t]['name'] = $row;
        $versions[$t]['version'] = getVersionChip($key);
        $string = $string . $versions[$t]['name'] . "=" . $versions[$t]['version'] . "\r\n";
        $t++;
      }

      $handle = fopen("versions.ini", "w");
      fwrite($handle, $string);
      fclose($handle);
      updateLog("Versionen wurden aktualisiert");

      $downloads = parse_ini_file("/var/www/html/nvidia/downloads.ini");
      $string2 = "";
      $new = "";
      $r = 0;

      foreach($downloads as $row2 => $key2){
        foreach($versions as $row3 => $key3) {

          if($key3['name'] == $row2){
            $exp = explode("Quadro_Certified/", $key2);
            $exp2 = explode("-", $exp[1]);

            $comp = "";
            for($s = 1; $s <= sizeof($exp2)-1; $s++){
              $comp = $comp . "-" . $exp2[$s];
            }
            $new = $new . $key3['name'] . "=" . $exp[0] . "Quadro_Certified/" . $key3['version'] . "/" . $key3['version'] . $comp . "\r\n";
          }
        }
        $r++;
      }

      $handle = fopen("/var/www/html/downloads.ini", "w");
      fwrite($handle, $new);
      fclose($handle);
      updateLog("Download Links wurden aktualisiert");

    }

    function updateLog($text) {
      $handle = fopen("/var/www/html/nvidia/log.txt", "a");
      fwrite($handle, "[" . getCurrentDate() . "] " . $text . "\r\n");
      fclose($handle);
    }

    function checkIfDownloaded($name) {
      $datei = scandir("/var/www/html/nvidia/test/");
      foreach($datei as $file){
        if($file == $name){
          updateLog($name . " ist schon vorhanden!");
          return true;
        }
      }
      return false;
    }


    function downloadFiles() {
      set_time_limit(1000);
      foreach(loadDonwloadLinks() as $key => $row){

        $eintraege = "/mnt/netzlaufwerk/Automatisierte_Installation/grafikkarte/" . $key . "/";
        $datei = scandir($eintraege);
        $exp2 = explode("-", $datei[3]);

        $versionen = loadVersions();
        if($versionen[$key] == $exp2[0]){
          updateLog($key . " wurde Ueberspringen [versionen.ini] " . $versionen[$key] . " = [DATEI] " . $exp2[0]);
          continue;
        }



        $exp = explode("/", $row);

        if(checkIfDownloaded($exp[sizeof($exp)-1]) == false){
		echo "Datei wird heruntergeladen <br>";
		file_put_contents("/var/www/html/nvidia/test/" . $exp[sizeof($exp)-1], fopen("$row", 'r'));
        }


        updateLog($key . " wurde aktualisiert: " . $exp[sizeof($exp)-1]);



	$param1 = "/var/www/html/nvidia/test/" . $exp[sizeof($exp)-1];
	$param2 = $eintraege;
	$param3 = $eintraege . $datei[3];


	//$result = shell_exec("sudo /var/www/html/nvidia/move.sh '" . $param1 . "' '" . $param2 . "' '" . $param3 . "'");

	if(copy($param1, $param2 . $exp[sizeof($exp)-1])){
		updateLog($exp[sizeof($exp)-1]. " wurde kopiert!");
	}
	if(unlink($param3)){
		updateLog($datei[3] . " wurde geloescht!");
	}
      }
    }

    function getCurrentDate() {
      return date("d:m:Y - H:i:s");
    }

    ?>
