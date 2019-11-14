<?php

require_once('vendor/autoload.php');

function getApiConfig($dbh) {
    $q = $dbh->prepare("SELECT accessToken FROM pp_tokens order by expires DESC limit 1");
    $q->execute();
    $tokens = $q->fetchAll(PDO::FETCH_ASSOC);
    
    return \Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken($tokens[0]['accessToken']);
}

function getPpAccountsApi($config) {
    return new \Swagger\Client\Api\AccountsApi(
        new \GuzzleHttp\Client(),
        $config
    );
}

function getPpCustomFields($config) {
    $customFieldsApi = new \Swagger\Client\Api\CustomFieldsApi(
        new \GuzzleHttp\Client(),
        $config
    );
    $customFields = $customFieldsApi->customFieldsGetCustomFieldsForContact();

    $customFieldIds = array();

    foreach($customFields as $customField) {
        $customFieldIds[$customField['label']] = $customField['id'];
    }

    return $customFieldIds;
}

function getPpContacts($apiInstance) {
    $contacts = array();

    $accounts = $apiInstance->accountsGetAccounts();

    foreach ($accounts as $account) {
        $contact = array(
            'accountId' => $account['id'],
            'contactId' => $account['primary_contact']['id'],
            'firstName' => $account['primary_contact']['first_name'],
            'lastName' => $account['primary_contact']['last_name']
        );

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
    $deleted = array();

    $q = $dbh->prepare("SELECT clinic_id, first_name, last_name,
        landlord_first_name, landlord_last_name,
        property_manager_first_name, property_manager_last_name,
        other_party_a_first_name, other_party_a_last_name, other_party_a_adverse,
        other_party_b_first_name, other_party_b_last_name, other_party_b_adverse,
        other_party_c_first_name, other_party_c_last_name, other_party_c_adverse FROM cm");
    $q->execute();
    $cases = $q->fetchAll(PDO::FETCH_ASSOC);

    foreach($cases as $case) {
        $firstName = trim($case['first_name']);
        $lastName = trim($case['last_name']);

        if ($firstName == 'DELETED' && $lastName == 'DELETED') {
            $deleted[] = $case['clinic_id'];
            continue;
        }

        if ($firstName == '' && $lastName == '') {
            $deleted[] = $case['clinic_id'];
            continue;
        }

        $contacts[] = array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'role' => 'Tenant',
            'adverseParty' => 'No',
            'ehCaseNumber' => $case['clinic_id']
        );

        $landlordFirstName = trim($case['landlord_first_name']);
        $landlordLastName = trim($case['landlord_last_name']);
        if ($landlordLastName != '') {
            $contacts[] = array(
                'firstName' => $landlordFirstName,
                'lastName' => $landlordLastName,
                'role' => 'Landlord',
                'adverseParty' => 'Yes',
                'ehCaseNumber' => $case['clinic_id']
            );
        }

        $managerFirstName = trim($case['property_manager_first_name']);
        $managerLastName = trim($case['property_manager_last_name']);
        if ($managerLastName != '') {
            $contacts[] = array(
                'firstName' => $managerFirstName,
                'lastName' => $managerLastName,
                'role' => 'Property Manager',
                'adverseParty' => 'Yes',
                'ehCaseNumber' => $case['clinic_id']
            );
        }

        $otherAFirstName = trim($case['other_party_a_first_name']);
        $otherALastName = trim($case['other_party_a_last_name']);
        if ($otherAFirstName != '') {
            $contacts[] = array(
                'firstName' => $otherAFirstName,
                'lastName' => $otherALastName,
                'role' => 'Other Party A',
                'adverseParty' => $case['other_party_a_adverse'],
                'ehCaseNumber' => $case['clinic_id']
            );
        }

        $otherBFirstName = trim($case['other_party_b_first_name']);
        $otherBLastName = trim($case['other_party_b_last_name']);
        if ($otherBFirstName != '') {
            $contacts[] = array(
                'firstName' => $otherBFirstName,
                'lastName' => $otherBLastName,
                'role' => 'Other Party B',
                'adverseParty' => $case['other_party_b_adverse'],
                'ehCaseNumber' => $case['clinic_id']
            );
        }

        $otherCFirstName = trim($case['other_party_c_first_name']);
        $otherCLastName = trim($case['other_party_c_last_name']);
        if ($otherCFirstName != '') {
            $contacts[] = array(
                'firstName' => $otherCFirstName,
                'lastName' => $otherCLastName,
                'role' => 'Other Party C',
                'adverseParty' => $case['other_party_c_adverse'],
                'ehCaseNumber' => $case['clinic_id']
            );
        }
    }

    return array('contacts' => $contacts, 'deleted' => $deleted);
}

function createPpContact($ehContact, $ppAccountsApi, $ppCustomFields) {
    $newPpAccount = new \Swagger\Client\Model\Account(
        array(
            'id' => trim(com_create_guid(), '{}'),
            'primary_contact' => new \Swagger\Client\Model\Contact(
                array(
                    'id' => trim(com_create_guid(), '{}'),
                    'first_name' => $ehContact['firstName'],
                    'last_name' => $ehContact['lastName'],
                    'custom_field_values' => array(
                        new \Swagger\Client\Model\CustomFieldValue(
                            array(
                                'value_string' => $ehContact['role'],
                                'custom_field_ref' => new \Swagger\Client\Model\CustomFieldRef(
                                    array('id' => $ppCustomFields['Role'])
                                )
                            )
                        ),
                        new \Swagger\Client\Model\CustomFieldValue(
                            array(
                                'value_string' => $ehContact['adverseParty'],
                                'custom_field_ref' => new \Swagger\Client\Model\CustomFieldRef(
                                    array('id' => $ppCustomFields['Adverse Party'])
                                )
                            )
                        ),
                        new \Swagger\Client\Model\CustomFieldValue(
                            array(
                                'value_string' => $ehContact['ehCaseNumber'],
                                'custom_field_ref' => new \Swagger\Client\Model\CustomFieldRef(
                                    array('id' => $ppCustomFields['EH Case Number'])
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    $ppAccountsApi->accountsPostAccount($newPpAccount);
}

?>
