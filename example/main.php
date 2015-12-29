<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require("../Captcha.php");

if (isset($_GET["check"])) {
    $isok = (Captcha::check($_GET["check"])) ? TRUE : FALSE;
    
    header("Content-Type: application/json");
    echo json_encode(["isok" => $isok]);

} else {
    try {
		Captcha::make();
    }
    catch (\Exception $ex) {
        var_dump($ex->getMessage());
    } 
}
