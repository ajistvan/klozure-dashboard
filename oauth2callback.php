<?php
require_once 'scripts/gapcm/src/Google/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfigFile('https://klozure-dashboard.appspot.com/static/client_secrets.json');
$client->setRedirectUri('https://klozure-dashboard.appspot.com/oauth2callback.php');
$client->addScope('https://spreadsheets.google.com/feeds/');

if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect_uri = 'https://klozure-dashboard.appspot.com/';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}