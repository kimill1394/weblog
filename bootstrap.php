<?php
require 'mvc/Loader.php';

$loader = new Loader();
$loader=>regDirectory(dirname(__FILE__).'/mvc');
$loader=>regDirectory(dirname(__FILE__).'/models');
// __FILE__ : now where i am and what's my name
// dirname() : where i am
// http://php.net/manual/en/function.dirname.php
$loader->register();
 ?>
