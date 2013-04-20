<?php

$link = $_GET["link"];
$link="http://".$link;
header("Location: $link"); 
exit;
?>

