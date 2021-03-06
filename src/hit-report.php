<?php

header('Content-Type: application/json');
include('buffer.php');

$intervals = array('30', '600', '1800', '3600', '21600', '43200','86400','259200','604800');
$domain_codes = array('akam', 'cccc', 'ecst', 'fstl', 'qtil');
if (isset($_GET['domain_codes'])) {
    $domain_codes = split(',', $_GET['domain_codes']);
}

if (isset($_GET['week'])) {
    $start = strtotime("2016W".$_GET['week']);
    $end = $start + (86400 * 7);
}
if (isset($_GET['date'])) {
    $start = strtotime($_GET['date']." 00:00:00");
    $end = $start + 86400;
}
$host = $_GET['host'];
$data = array();
foreach ($domain_codes as $domain_code) {
    $data[$domain_code] = array();
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
        $data[$domain_code][$interval] = ($hit / $total) * 100;
    }
}
print json_encode($data);
?>

