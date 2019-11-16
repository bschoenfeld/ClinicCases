<?php

session_start();
require('../lib/php/auth/session_check.php');
require('../db.php');
require('api.php');

if ($_SESSION['permissions']['group_name'] != 'admin') {
    exit('You do not have permission');
}

try {
    // Connect to PP
    $ppApiConfig = getApiConfig($dbh);
    $ppAccountsApi = getPpAccountsApi($ppApiConfig);

    // Get PP contacts
    $ppContacts = getPpContacts($ppAccountsApi, true);

    header('Content-type: application/json');
    echo json_encode($ppContacts);

} catch(Exception $e) {
	//400 is sent to trigger an error for ajax requests.
    header('HTTP/1.1 400 Bad Request');

    echo $e->getMessage();
}
