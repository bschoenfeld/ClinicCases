<?php

session_start();
require('../lib/php/auth/session_check.php');
require('../db.php');
require('api.php');

try {
    $clinicId = NULL;
    $matchThreshold = 80;

    if (isset($_GET['clinicId'])) {
        $clinicId = $_GET['clinicId'];
    } else if(isset($_POST['clinicId'])) {
        $clinicId = $_POST['clinicId'];
        $matchThreshold = $_POST['threshold'];
    }

    if ($clinicId == NULL) {
        throw new Exception('No clinic id');
    }

    $ppContacts = NULL;

    if(isset($_POST['ppContacts'])) {
        $ppContacts = $_POST['ppContacts'];
    } else {
        // Connect to PP
        $ppApiConfig = getApiConfig($dbh);
        $ppAccountsApi = getPpAccountsApi($ppApiConfig);
        $ppCustomFields = getPpCustomFields($ppApiConfig);

        // Get PP contacts
        $ppContacts = getPpContacts($ppAccountsApi);
    }
    
    // Get EH contacts
    $ehData = getEhContacts($dbh, $clinicId);
    $ehContacts = $ehData['contacts'];

    $conflictCheck = array(
        'clinicId' => $clinicId,
        'conflicts'=> array()
    );

    foreach ($ppContacts as $ppContact) {
        // If the PP contact is from this case, don't check for conflicts
        if (isset($ppContact['ehCaseNumber']) && $ppContact['ehCaseNumber'] == $clinicId) {
            continue;
        }

        foreach($ehContacts as $ehContact) {
            // If both contacts have their adverse party field and they are both 'No' then it can't be a conflict
            if ($ehContact['adverseParty'] == 'No' && $ppContact['adverseParty'] == 'No') {
                continue;
            }

            // If both contacts have their adverse party field and they are both 'Yes' then it can't be a conflict
            if ($ehContact['adverseParty'] == 'Yes' && $ppContact['adverseParty'] == 'Yes') {
                continue;
            }

            $ppName = $ppContact['firstName'] . ' ' . $ppContact['lastName'];
            $ehName = $ehContact['firstName'] . ' ' . $ehContact['lastName'];
            similar_text($ppName, $ehName, $per);
            if ($per >= $matchThreshold) {
                $conflictCheck['conflicts'][] = array(
                    'pp' => $ppContact,
                    'eh' => $ehContact
                );
            }
        }
    }

    header('Content-type: application/json');
    echo json_encode($conflictCheck);

} catch(Exception $e) {
	//400 is sent to trigger an error for ajax requests.
    header('HTTP/1.1 400 Bad Request');

    echo $e->getMessage();
}
