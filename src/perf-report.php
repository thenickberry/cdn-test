<?php

header('Content-Type: application/json');
include('buffer.php');

$intervals = array('30', '600', '1800', '3600', '21600', '43200','86400','259200','604800');
$domain_codes = array('akam', 'cccc', 'ecst', 'fstl', 'qtil');
if (isset($_GET['domain_codes'])) {
    $domain_codes = split(',',$_GET['domain_codes']);
}

function get_percentile($percentile, $array) {
    sort($array);
    $index = ($percentile/100) * count($array);
    if (floor($index) == $index) {
         $result = ($array[$index-1] + $array[$index])/2;
    }
    else {
        $result = $array[floor($index)];
    }
    return $result;
}

$host = $_GET['host'];
$data = array();
$hit = array();
$miss= array();
foreach ($domain_codes as $domain_code) {
    foreach ($intervals as $interval) {
        $total = 0;
        $path = "results/${host}/${interval}/${domain_code}";
        $files = scandir($path);
        foreach ($files as $file) {
            if (isset($_GET['date'])) {
                $start = strtotime($_GET['date']." 00:00:00");
                $end = $start + 86400;
                if ((int)$file < (int)$start || (int)$file > (int)$end) continue;
            }
            $filename = $path."/".$file;
            if (strstr($filename, ".")) continue;
            $contents = file_get_contents($filename);
            // if time_total is in the file, append to either $hit or $miss depending whether it was a hit or a miss
            if (strpos($contents,"time_total") !== false) {
                preg_match('/time_total: ([0-9]+\.[0-9]+)/', $contents, $matches);
                if (strpos($contents,"HIT") !== false) {
                    $hit[$domain_code][] = $matches[1];
                } elseif (strpos($contents,"MISS") !== false) {
                    $miss[$domain_code][] = $matches[1];
                } else {
                    // what the fuck
                }
            }
        }
        
        $data['hit_avg'][$domain_code] = array_sum($hit[$domain_code]) / count($hit[$domain_code]);
        $data['hit_50pct'][$domain_code] = get_percentile(50, $hit[$domain_code]);
        $data['hit_90pct'][$domain_code] = get_percentile(90, $hit[$domain_code]);
        $data['miss_avg'][$domain_code] = array_sum($miss[$domain_code]) / count($miss[$domain_code]);
        $data['miss_50pct'][$domain_code] = get_percentile(50, $miss[$domain_code]);
        $data['miss_90pct'][$domain_code] = get_percentile(90, $miss[$domain_code]);
    }
}
print json_encode($data);
?>

