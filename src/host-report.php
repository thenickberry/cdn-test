<?php

header('Content-Type: application/json');
include('buffer.php');

$intervals = array('30', '600', '1800', '3600', '21600', '43200','86400','259200','604800');
$domain_codes = array('akam', 'cccc', 'ecst', 'fstl', 'qtil');
if (isset($_GET['domain_codes'])) {
    $domain_codes = split(',', $_GET['domain_codes']);
}
if (isset($_GET['domain_code'])) {
    $domain_code = $_GET['domain_code'];
}

if (isset($_GET['week'])) {
    $start = strtotime("2016W".$_GET['week']);
    $end = $start + (86400 * 7);
}
if (isset($_GET['date'])) {
    $start = strtotime($_GET['date']." 00:00:00");
    $end = $start + 86400;
}
$data = array();
$hosts = array(
    'linode-fremont',
    'linode-us-east',
    'linode-eu-uk',
    'ec2-ireland',
    'linode-eu-de',
    'azure-eu-nl',
    'azure-eu-nl-a1',
    'ec2-india',
    'ec2-singapore',
    'azure-apac-hk',
);

if ($domain_code == 'cccc' || $domain_code == 'qtil') {
    $hosts = array('samir-china');
}

foreach ($hosts as $host) {
    $data[$host] = array();
    foreach ($intervals as $interval) {
        $total = 0;
        $hit = 0;
        $path = "results/${host}/${interval}/${domain_code}";
        $files = scandir($path);
        foreach ($files as $file) {
            if (strstr($file, ".")) continue;
            if (isset($start) && isset($end)) {
                if ((int)$file < (int)$start || (int)$file > (int)$end) continue;
            }
            $filename = $path."/".$file;
            // if HIT
            if (strpos(file_get_contents($filename),"HIT") !== false) $hit++;
            $total++;
        }
        $data[$host][$interval] = ($hit / $total) * 100;
    }
}
print json_encode($data);
?>

