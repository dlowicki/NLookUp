<?php
if(isset($_POST['grafikkarte'])){
  $g = $_POST['grafikkarte'] . ".zip";
  $file = "test/" . $g;

  set_time_limit(0);
  $file2 = @fopen($file,"rb");
  while(!feof($file2))
  {
  	print(@fread($file2, 1024*8));
  	ob_flush();
  	flush();
  }

}
?>
