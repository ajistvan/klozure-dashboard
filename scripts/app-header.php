<?php
//auth check
session_start();
$client_token_check = $_SESSION['access_token'];
if (! check_token($client_token_check)){
    header("Location: scripts/authenticate.php");
}

//get widget data from spreadsheet
$sheetvalues = get_sheet_data($_SESSION['access_token']);
$dwdata = build_dash_widget_array($sheetvalues);
?>

<html>
    <head>
        <link href='https://fonts.googleapis.com/css?family=Asap:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
        <link href='stylesheets/app-css.css' rel='stylesheet' type='text/css'>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>