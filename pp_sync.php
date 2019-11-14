<html>
<head>

<?php
session_start();
require('lib/php/auth/session_check.php');
require('db.php');
require('pp_func.php');


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
        'modifies' => array(),
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
                        $toSync['modifies'][] = $ehContact;
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

    echo "<script>window.toSync = JSON.parse('" . json_encode($toSync) . "');</script>";

} catch(Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";

	print_r($e);
}

?>

<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>

<script>

</script>

</head>
<body>
<h1>Test</h1>
</body>
</html>