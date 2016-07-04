<?php

include ('Valite.php');

$valite = new Valite();
$picture = "6.jpeg";
$valite->setImage($picture);
$valite->getHec();
$ert = $valite->run();
//$ert = "1234";
print_r($ert);
echo '<br><img src="'.$picture.'"><br>';
$valite->Draw();

echo "<br>-------------------------------<br>\n";
//print_r($valite->getColor());

echo "<br>-------------------------------<br>\n";

$valite->DrawKeys();

?>