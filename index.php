
<?php
require_once "vendor/autoload.php";
use src\Dispatcher\dispatcher;

$action = $_GET['action'] ?? null;
$dispatcher = new Dispatcher($action);
$dispatcher->run();
?>


