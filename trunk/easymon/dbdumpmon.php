<?
$dname = "/home/ludum/db";

foreach (array("ludum","compo") as $k) {
    $fname = "$dname/dump-$k.sql";

    if ((time()-filemtime($fname)) > (36*60*60)) { die; }

    $fname = "$dname/stderr-$k.txt";
    if (filesize($fname)) { die; }
}

echo "OK";
?>