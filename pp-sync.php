<?php
session_start();
require('lib/php/auth/session_check.php');
require('db.php');
require_once('vendor/autoload.php');

function getApiInstance($dbh) {
    $q = $dbh->prepare("SELECT accessToken FROM pp_tokens order by expires DESC limit 1");
    $q->execute();
    $tokens = $q->fetchAll(PDO::FETCH_ASSOC);
    
    $config = \Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken($tokens[0]['accessToken']);
    
    return new \Swagger\Client\Api\AccountsApi(
        new \GuzzleHttp\Client(),
        $config
    );
}

function getPpContacts($apiInstance) {
    $contacts = array();

    $accounts = $apiInstance->accountsGetAccounts();
    foreach($accounts as $account) {
        $contact = array('name' => $account['display_name']);

        foreach($account['primary_contact']['custom_field_values'] as $cf) {
            switch($cf['custom_field_ref']['label']) {
                case "Role":
                    $contact['role'] = $cf['value_string'];
                    break;
                case "Adverse Party":
                    $contact['adverseParty'] = $cf['value_string'];
                    break;
                case "EH Case Number":
                    $contact['ehCaseNumber'] = $cf['value_string'];
                    break;
            }
        }

        $contacts[] = $contact;
    }

    return $contacts;
}

function getEhContacts($dbh) {
    $contacts = array();

    $q = $dbh->prepare("SELECT clinic_id, first_name, last_name,
        landlord_first_name, landlord_last_name,
        property_manager_first_name, property_manager_last_name,
        other_party_a_first_name, other_party_a_last_name, other_party_a_adverse,
        other_party_b_first_name, other_party_b_last_name, other_party_b_adverse,
        other_party_c_first_name, other_party_c_last_name, other_party_c_adverse FROM cm");
    $q->execute();
    $cases = $q->fetchAll(PDO::FETCH_ASSOC);

    foreach($cases as $case) {
        $contacts[] = array(
            'name' => $case['first_name'] . ' ' . $case['last_name'],
            'role' => 'Tenant',
            'adverseParty' => 'No',
            'ehCaseNumber' => $case['clinic_id']
        );

        if ($case['landlord_last_name'] != '') {
            $contacts[] = array(
                'name' => trim($case['landlord_first_name'] . ' ' . $case['landlord_last_name']),
                'role' => 'Landlord',
                'adverseParty' => 'Yes',
                'ehCaseNumber' => $case['clinic_id']
            );
        }

        if ($case['property_manager_last_name'] != '') {
            $contacts[] = array(
                'name' => trim($case['property_manager_first_name'] . ' ' . $case['property_manager_last_name']),
                'role' => 'Property Manager',
                'adverseParty' => 'Yes',
                'ehCaseNumber' => $case['clinic_id']
            );
        }

        if ($case['other_party_a_last_name'] != '') {
            $contacts[] = array(
                'name' => trim($case['other_party_a_first_name'] . ' ' . $case['other_party_a_last_name']),
                'role' => 'Other Party A',
                'adverseParty' => $case['other_party_a_adverse'],
                'ehCaseNumber' => $case['clinic_id']
            );
        }

        if ($case['other_party_b_last_name'] != '') {
            $contacts[] = array(
                'name' => trim($case['other_party_b_first_name'] . ' ' . $case['other_party_b_last_name']),
                'role' => 'Other Party B',
                'adverseParty' => $case['other_party_b_adverse'],
                'ehCaseNumber' => $case['clinic_id']
            );
        }

        if ($case['other_party_c_last_name'] != '') {
            $contacts[] = array(
                'name' => trim($case['other_party_c_first_name'] . ' ' . $case['other_party_c_last_name']),
                'role' => 'Other Party C',
                'adverseParty' => $case['other_party_c_adverse'],
                'ehCaseNumber' => $case['clinic_id']
            );
        }
    }

    return $contacts;
}

try {
    $ehContacts = getEhContacts($dbh);
    foreach($ehContacts as $contact) {
        print_r($contact);
        echo "<br>";
    }

    $apiInstance = getApiInstance($dbh);

    $ppContacts = getPpContacts($apiInstance);
    print_r($ppContacts);

} catch(Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";

	print_r($e);
}

echo "Done";