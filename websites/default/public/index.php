<?php
session_start();
require '../app/autoload.php';

$routes = new \Booking\Routes();

$entryPoint = new \CSY\EntryPoint($routes);

$entryPoint->run();
