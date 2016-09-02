<?php

header('Content-Type: application/json');
include('buffer.php');

date_default_timezone_set('US/Pacific');
$intervals = array('30', '600', '1800', '3600', '21600', '43200','86400','259200','604800');
$domain_codes = array('akam', 'cccc', 'ecst', 'fstl', 'llnw', 'qtil');

$data = array();
foreach ($intervals as $interval) {
    foreach ($domain_codes as $domain_code) {
        $path = "results/${interval}/${domain_code}";
        $files = scandir($path);
        foreach ($files as $file) {
            if (strstr($file, '.')) continue;
            $week = date('W',$file);
            $data[$week] = 1;
        }
    }
}
$data = array_keys($data);
sort($data);
print json_encode($data);
?>

