<?php

require_once 'gapcm/src/Google/autoload.php';
session_start();

$client = new Google_Client();
$client->setAuthConfigFile('https://klozure-dashboard.appspot.com/static/client_secrets.json');
$client->addScope('https://spreadsheets.google.com/feeds/');
$client->setRedirectUri('https://klozure-dashboard.appspot.com/oauth2callback.php');

$auth_url = $client->createAuthUrl();

header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));