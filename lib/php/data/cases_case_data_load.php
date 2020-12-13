<?php //scripts for case data tab in case detail
@session_start();
require_once dirname(__FILE__) . '/../../../db.php';
require_once(CC_PATH . '/lib/php/auth/session_check.php');
require_once(CC_PATH . '/lib/php/utilities/convert_times.php');

if (isset($_REQUEST['id'])) {
	$case_id = $_REQUEST['id'];
}

if (isset($_REQUEST['type'])) {
	$type = $_REQUEST['type'];
}
//Get case data
$q = $dbh->prepare("SELECT * FROM cm WHERE id = ?");
$q->bindParam(1,$case_id);
$q->execute();
$case_data = $q->fetch(PDO::FETCH_ASSOC);

//Get columns config
$q = $dbh->prepare("SELECT * from cm_columns ORDER BY display_order ASC");
$q->execute();
$columns = $q->fetchAll(PDO::FETCH_ASSOC);

$dta = null;

foreach ($columns as $col) {
	if ($_SESSION['group'] == 'cc_agent' && $col['hide_cc_agent']) {
		continue;
	}
	if ($_SESSION['group'] == 'volunteer' && $col['hide_volunteer']) {
		continue;
	}
	if ($_SESSION['group'] != 'admin' && $col['admin_only']) {
		continue;
	}
	//push the value of the field in case_data onto $columns
	if ($col['db_name'] !== 'assigned_users') {//we don't want assigned users in this view
		$field =  $col['db_name'];
		$field_value = $case_data[$field];
		$col['value'] = $field_value;
		$dta[] = $col;
	}
}

if (!$_SESSION['mobile']){
    include '../../../html/templates/interior/cases_case_data.php';
}
