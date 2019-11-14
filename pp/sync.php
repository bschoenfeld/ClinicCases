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

    // Get PP contacts
    $ppContacts = getPpContacts($ppAccountsApi);
    //echo 'Found ' . count($ppContacts) . ' PP contacts <br>';

    // Get EH contacts
    $ehData = getEhContacts($dbh);
    $ehContacts = $ehData['contacts'];
    $ehDeletedCases = $ehData['deleted'];
    //echo 'Found ' . count($ehContacts) . ' EH contacts <br>';

    $toSync = array(
        'adds' => array(),
        'deletes' => array()
    );

    // Loop through the PP contacts and see if we need to delete any
    foreach ($ppContacts as $ppContact) {
        if (in_array($ppContact['ehCaseNumber'], $ehDeletedCases)) {
            $toSync['deletes'][] = array(
                'accountId' => $ppContact['accountId'],
                'contactId' => $ppContact['contactId']
            );
        }
    }

    // Loop through the EH contacts make sure all the contacts are in PP
    foreach($ehContacts as $ehContact) {
        $foundInPp = False;
        foreach($ppContacts as $ppContact) {
            if($ehContact['ehCaseNumber'] == $ppContact['ehCaseNumber'] && $ehContact['role'] == $ppContact['role']) {
                // See if we need to update PP with any changes made in EH
                if ($ehContact['firstName'] != $ppContact['firstName'] ||
                    $ehContact['lastName'] != $ppContact['lastName'] ||
                    $ehContact['adverseParty'] != $ppContact['adverseParty']) {
                        $ehContact['accountId'] = $ppContact['accountId'];
                        $ehContact['contactId'] = $ppContact['contactId'];
                        $toSync['deletes'][] = $ehContact;
                        $toSync['adds'][] = $ppContact;
                }
                $foundInPp = True;
                break;
            }
        }

        if (!$foundInPp) {
            // Create in PP
            //createPpContact($ehContact, $ppAccountsApi, $ppCustomFields);
            $toSync['adds'][] = $ehContact;
        }
    }

    header('Content-type: application/json');
    echo json_encode($toSync);

} catch(Exception $e) {
	//400 is sent to trigger an error for ajax requests.
    header('HTTP/1.1 400 Bad Request');

    echo $e->getMessage();
}
