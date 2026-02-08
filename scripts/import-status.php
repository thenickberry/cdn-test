<?php

mysql_connect('localhost', 'cdntest', 'sw33tjesus');
mysql_select_db('cdntest');

while ($f= fgets(STDIN)) {
    $filename = trim($f);
    list($cwd, $node, $frequency, $origin, $timestamp) = split("/", $filename, 5);

    $contents = file_get_contents($filename);

    $cache_status = '';
    if (stripos($contents,"HIT") !== false) {
        $cache_status = 'hit';
    } elseif (strpos($contents,"MISS") !== false) {
        $cache_status = 'miss';
    } else {
        $cache_status = 'na';
    }

    # create a unique hash for ID to make the ID simple
    $id = sha1(implode("", array($timestamp, $frequency, $node, $origin)));
    $query = "UPDATE records SET cache_status='${cache_status}' WHERE id='${id}'";
    mysql_query($query);
    print "$filename.\n";
}

mysql_close();
?>
