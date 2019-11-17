<?php

session_start();
require('../lib/php/auth/session_check.php');
require('../db.php');
require('api.php');

try {
    $clinicId = NULL;
    $caseId = NULL;
    $matchThreshold = 80;

    if (isset($_GET['clinicId'])) {
        $clinicId = $_GET['clinicId'];
    } else if (isset($_GET['caseId'])) {
        $caseId = $_GET['caseId'];
    } else if ($_SESSION['group'] != 'admin') {
        exit('You do not have permission');
    }
    
    // Connect to PP
    $ppApiConfig = getApiConfig($dbh);
    $ppAccountsApi = getPpAccountsApi($ppApiConfig);
    $ppCustomFields = getPpCustomFields($ppApiConfig);

    // Get PP contacts
    $ppContacts = getPpContacts($ppAccountsApi);
    
    // Get EH contacts
    $ehData = getEhContacts($dbh, $clinicId, $caseId);
    $ehContacts = $ehData['contacts'];

    $conflictCheck = array(
        'ppContactCount' => count($ppContacts),
        'ehContactCount' => count($ehContacts),
        'checkedCount' => 0,
        'conflicts' => array()
    );

    foreach ($ppContacts as $ppContact) {

        foreach($ehContacts as $ehContact) {
            // If the PP contact is from this case, don't check for conflicts
            if (isset($ppContact['ehCaseNumber']) && $ppContact['ehCaseNumber'] == $ehContact['ehCaseNumber']) {
                continue;
            }

            // If both contacts have their adverse party field and they are both 'No' then it can't be a conflict
            if ($ehContact['adverseParty'] == 'No' && $ppContact['adverseParty'] == 'No') {
                continue;
            }

            // If both contacts have their adverse party field and they are both 'Yes' then it can't be a conflict
            if ($ehContact['adverseParty'] == 'Yes' && $ppContact['adverseParty'] == 'Yes') {
                continue;
            }

            $conflictCheck['checkedCount'] += 1;

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

    if ($clinicId != null || $caseId != null) {

        // Get conflict notes
        $whereClause = "";
        if ($clinicId != null) {
            $whereClause = " WHERE clinic_id = ?";
        } else if($caseId != null) {
            $whereClause = " WHERE id = ?";
        }

        $q = $dbh->prepare("SELECT id, vplc_conflicts_review_needed, vplc_conflicts_notes FROM cm" . $whereClause);
        if ($clinicId != null)  $q->bindParam(1, $clinicId);
        else if($caseId != null)  $q->bindParam(1, $caseId);
        $q->execute();
        $case = $q->fetchAll(PDO::FETCH_ASSOC)[0];

        $caseId = $case['id'];
        $conflicts_review_needed = $case['conflicts_review_needed'];
        $existingConflicts = $case['vplc_conflicts_notes'];
        if ($existingConflicts != null) {
            $existingConflicts = unserialize($existingConflicts);
        }

        // see if the number of conflicts match and the names of the eh contacts match
        // if they dont match, replace the conflicts in the notes with the new contacts
        // then go back through and see if any of the stored conflicts match and have been checked

        // set review need to YES
        if (count($conflictCheck['conflicts']) == 0) {
            // Currently no conflicts, so clear them all
            $conflicts_review_needed = 'No';
        } else {
            // If there are conflicts, then see if they've changed
            // If the conflict already exists, copy the propety indicating if they've been cleared
            $conflictsChanged = false;

            if (count($existingConflict) != count($conflictCheck['conflicts'])) {
                $conflictsChanged = true;
            }
            
            foreach ($existingConflicts as $existingConflict) {
                $matchFound = false;

                foreach($conflictCheck['conflicts'] as $newConflict) {
                    if ($newConflict['eh']['firstName'] == $existingConflict['eh']['firstName'] &&
                        $newConflict['eh']['lastName'] == $existingConflict['eh']['lastName'] && 
                        $newConflict['pp']['firstName'] == $existingConflict['pp']['firstName'] &&
                        $newConflict['pp']['lastName'] == $existingConflict['pp']['lastName'])
                    {
                        $matchFound = true;
                        $newConflict['cleared'] = $existingConflict['cleared'];
                    }
                }

                if (!$matchFound) {
                    $conflictsChanged = true;
                }
            }

            if ($conflictsChanged) {
                $conflicts_review_needed = 'Yes';

                // Send email
                $get_emails = $dbh->prepare("SELECT email FROM cm_users WHERE grp = 'admin' AND status = 'active'");
                $get_emails->execute();
                $emails = $get_emails->fetchAll(PDO::FETCH_ASSOC);
                $subject = "ClinicCases " . CC_PROGRAM_NAME . ": Conflict found";
                $message = "Conflict found for " . $conflictCheck['conflicts'][0]['eh']['ehCaseNumber'];

                foreach ($emails as $e) {
                    mail($e['email'],$subject,$message,CC_EMAIL_HEADERS,"-f ". CC_EMAIL_FROM);
                }
            }
        }

        $q = $dbh->prepare("UPDATE cm SET initial_conflicts_checked = 'Yes', vplc_conflicts_review_needed = ?, vplc_conflicts_notes = ? WHERE id = ?");
        $q->bindParam(1, $conflicts_review_needed);
        $q->bindParam(2, serialize($conflictCheck['conflicts']));
        $q->bindParam(3, $caseId);
        $q->execute();

        $conflictCheck['reviewNeeded'] = $conflicts_review_needed;
    }

    header('Content-type: application/json');
    echo json_encode($conflictCheck);

} catch(Exception $e) {
	//400 is sent to trigger an error for ajax requests.
    header('HTTP/1.1 400 Bad Request');

    echo $e->getMessage();
}
