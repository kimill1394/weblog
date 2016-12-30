<?php
// phpinfo();

$string = " / account/ index/ php.php";
var_dump($string);
echo "<br>";

$converts = ltrim($string);
var_dump($converts);
echo "<br>";
$converts = explode('/', ltrim($string,'/'));
var_dump($converts);
echo "<br>";
$converts = explode('/', $string);
var_dump($converts);

 ?>
