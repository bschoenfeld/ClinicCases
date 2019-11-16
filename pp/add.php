<?php

session_start();
require('../lib/php/auth/session_check.php');
require('../db.php');
require('api.php');

try {
    // Connect to PP
    $ppApiConfig = getApiConfig($dbh);
    $ppAccountsApi = getPpAccountsApi($ppApiConfig);
    $ppCustomFields = getPpCustomFields($ppApiConfig);

    $contact = $_POST;
    createPpContact($contact, $ppAccountsApi, $ppCustomFields);

    $now = new DateTime("now");
    $q = $dbh->prepare("INSERT INTO pp_actions (id, action, clinicId, name, user, time) VALUES (NULL, :action, :clinicId, :name, :user, :time);");
    $data = array(
        'action' => 'add',
        'clinicId' => $contact['ehCaseNumber'],
        'name' => $contact['firstName'] . ' ' . $contact['lastName'],
        'user' => $_SESSION['login'],
        'time' => $now->format('Y-m-d H:i:s')
    );
    $q->execute($data);

    header('Content-type: application/json');
    echo json_encode(array('success'=>true));

} catch(Exception $e) {
	//400 is sent to trigger an error for ajax requests.
    header('HTTP/1.1 400 Bad Request');

    echo $e->getMessage();
}
