<?php
//Script to evaluate potential conflicts of interest
session_start();
require('../auth/session_check.php');
require('../../../db.php');
require('../utilities/names.php');
require('../utilities/convert_times.php');

include('../airtable/Airtable.php');
include('../airtable/Request.php');
include('../airtable/Response.php');

//function to sort the activities array by subkey - date

function sortBySubkey(&$array, $subkey, $sortType = SORT_DESC) {

    foreach ($array as $subarray) {

        $keys[] = $subarray[$subkey];
    }

    array_multisort($keys, $sortType, $array);
}


$id = $_POST['case_id'];

if (isset($_POST['type']))
{$type = $_POST['type'];}
else
{$type='display';}

$q = $dbh->prepare("SELECT * FROM cm WHERE id = ?");
$q->bindParam(1,$id);
$q->execute();

$case = $q->fetch(PDO::FETCH_ASSOC);

$parties = array();
$parties[] = array(
	'name' => $case['first_name'] . ' ' . $case['last_name'], 
	'role' => 'Tenant', 
	'matches' => array());

$parties[] = array(
	'name' => $case['landlord_first_name'] . ' ' . $case['landlord_last_name'], 
	'role' => 'Landlord', 
	'matches' => array());

$parties[] = array(
	'name' => $case['property_manager_first_name'] . ' ' . $case['property_manager_last_name'], 
	'role' => 'Property Manager', 
	'matches' => array());

$parties[] = array(
	'name' => $case['other_party_a_first_name'] . ' ' . $case['other_party_a_last_name'], 
	'role' => 'Other Party A', 
	'matches' => array());

$parties[] = array(
	'name' => $case['other_party_b_first_name'] . ' ' . $case['other_party_b_last_name'], 
	'role' => 'Other Party B', 
	'matches' => array());

$parties[] = array(
	'name' => $case['other_party_c_first_name'] . ' ' . $case['other_party_c_last_name'], 
	'role' => 'Other Party C', 
	'matches' => array());

$parties[] = array(
	'name' => $case['third_party_caller_first_name'] . ' ' . $case['third_party_caller_last_name'], 
	'role' => 'Third Party Caller', 
	'matches' => array());

use \TANIOS\Airtable\Airtable;
$airtable = new Airtable(array(
    'api_key' => CC_AIRTABLE_KEY,
    'base'    => CC_AIRTABLE_BASE
));
$request = $airtable->getContent('Clients');

$namesChecked = 0;
$namesSkipped = 0;
$conflictCount = 0;

do {
    $response = $request->getResponse();
    foreach($response['records'] as $record) {
		$curName = $record->fields->DisplayName;
		$caseId = $record->fields->EvictionHelplineCaseId;

		if($caseId == $case['clinic_id']) {
			$namesSkipped += 1;
			continue;
		}

		$namesChecked += 1;

		foreach ($parties as &$party) {
			if (ctype_space($party['name'])) {
				continue;
			}
			similar_text($party['name'], $curName, $per);
			if ($per >= 80) {
				$conflictCount += 1;
				$party['matches'][] = array(
					'percentage' => $per, 
					'name' => $curName,
					'airtableId' => $record->fields->id);
			}
		}
	}
}
while( $request = $response->next() );

$return = array(
	'conflicts' => $conflictCount, 
	'namesChecked' => $namesChecked,
	'namesSkipped' => $namesSkipped,
	'parties' => $parties);
echo json_encode($return);