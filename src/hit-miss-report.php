<?php

$intervals = array('30', '600', '1800', '3600', '21600', '43200','86400','259200','604800');
$domain_codes = array('akam', 'cccc', 'ecst', 'fstl', 'llnw', 'qtil');

$data = array();
foreach ($intervals as $interval) {
    $data[$interval] = array();
    foreach ($domain_codes as $domain_code) {
        $data[$interval][$domain_code] = ["total" => 0, "hit" => 0];
        $path = "results/${interval}/${domain_code}";
        $files = scandir($path);
        foreach ($files as $file) {
            if (isset($_GET['date'])) {
                $start = strtotime($_GET['date']." 00:00:00");
                $end = $start + 86400;
                if ((int)$file < (int)$start || (int)$file > (int)$end) continue;
            }
            $filename = $path."/".$file;
            if (strstr($filename, ".")) continue;
            // if HIT
            if (strpos(file_get_contents($filename),"HIT") !== false) $data[$interval][$domain_code]["hit"]++;
            $data[$interval][$domain_code]["total"]++;
        }
        $data[$interval][$domain_code]["hit_pct"] = ($data[$interval][$domain_code]["hit"] / $data[$interval][$domain_code]["total"]) * 100;
        $data[$interval][$domain_code]["miss_pct"] = 100 - $data[$interval][$domain_code]["hit_pct"];
    }
}
header('Content-Type: application/json');
print json_encode($data);
?>

