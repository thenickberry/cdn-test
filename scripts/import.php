<?php

mysql_connect('localhost', 'cdntest', 'sw33tjesus');
mysql_select_db('cdntest');

while ($f= fgets(STDIN)) {
    $filename = trim($f);
    list($cwd, $node, $frequency, $origin, $timestamp) = split("/", $filename, 5);

    $output = file_get_contents($filename);

    # pluck out the fabric and pop
    $fabric = '';
    $pop = '';
    if (preg_match_all("/X-Li-Fabric: (.*)/", $output, $matches)) {
        $fabric = $matches[1][0];
    }
    if (preg_match_all("/X-Li-Pop: (.*)/", $output, $matches)) {
        $pop = $matches[1][0];
    }

    # pluck out the timings
    preg_match_all("/(time_.*): (.*)/", $output, $matches);
    $time_namelookup = $matches[2][0];
    $time_connect = $matches[2][1];
    $time_appconnect = $matches[2][2];
    $time_pretransfer = $matches[2][3];
    $time_redirect = $matches[2][4];
    $time_starttransfer = $matches[2][5];
    $time_total = $matches[2][6];

    # create a unique hash for ID to make the ID simple
    $id = sha1(implode("", array($timestamp, $frequency, $node, $origin)));

    print $filename;

    # check to see if the record already exists
    $query = "SELECT id FROM records WHERE id='${id}'";
    $result = mysql_query($query);
    if (mysql_num_rows($result) != 0) {
        print "...skipping\n";
        continue;
    }
    print "\n";

    $query = "INSERT INTO records VALUES ('$id', $timestamp, $frequency, '$node', '$origin', '$time_namelookup', '$time_connect', '$time_appconnect', '$time_pretransfer', '$time_redirect', '$time_starttransfer', '$time_total', '$pop', '$fabric')";
    $result = mysql_query($query);

    $query = "INSERT INTO raw VALUES ('$id', '$output')";
    $result = mysql_query($query);
}

?>
