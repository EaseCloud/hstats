<?php

/**
 * HTTP page visit statistics
 */

require_once 'bootstrap.inc.php';

$domain = @$_GET['domain'] ?: '';

if(!$domain) die();

$pdo = Config::pdo();

$result = $pdo->query("SELECT url, cnt FROM `hstats_visit_count` where url like 'http://$domain/%'  or url like 'https://$domain/%'");

$rows = $result->fetchAll();
if (!$result) die(implode("\t", $pdo->errorInfo()));

foreach($rows as $row) {
    echo "{$row[0]}\t{$row[1]}\n";
}


