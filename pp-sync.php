<?php
session_start();
require('lib/php/auth/session_check.php');
require('db.php');
require_once('vendor/autoload.php');

try {
    $q = $dbh->prepare("SELECT accessToken FROM pp_tokens order by expires DESC limit 1");
    $q->execute();
    $tokens = $q->fetchAll(PDO::FETCH_ASSOC);
        
    $config = \Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken($tokens[0]['accessToken']);
    
    $apiInstance = new \Swagger\Client\Api\AccountsApi(
        new \GuzzleHttp\Client(),
        $config
    );
    
    try {
        $result = $apiInstance->accountsGetAccounts();

        foreach($result as $account) {
            echo "<br>" . $account['display_name'] . "<br>";

            foreach($account['primary_contact']['custom_field_values'] as $cf) {
                if($cf['custom_field_ref']['value_type'] == 'Date') {
                    echo "<br>" . $cf['custom_field_ref']['label'] . ":" . $cf['value_date_time']->format('Y-m-d H:i:s') . "<br>";
                } else {
                    echo "<br>" . $cf['custom_field_ref']['label'] . ":" . $cf['value_string'] . "<br>";
                }
            }
        }
    } catch (Exception $e) {
        echo 'Exception when calling AccountsApi->accountsGetAccounts: ', $e->getMessage(), PHP_EOL;
    }

} catch(Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";

	print_r($e);
}

echo "Done";