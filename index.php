<?php

/**
 * HTTP page visit statistics
 */

require_once 'bootstrap.inc.php';

// 1. Work around with origin domain.
$origin = @$_SERVER['HTTP_ORIGIN'] ?: http_die('请求无效：无效的域名');

// TODO: WILDCARD ORIGINAL TO BE SUPPORTED
if (in_array($origin, Config::ALLOWED_ORIGIN)) {
    // Only allow the config valid origin.
    header("Access-Control-Allow-Origin: $origin");
}

// 2. Work around with the ip.
$ip = @$_SERVER['HTTP_CLIENT_IP'] ?: @$_SERVER['HTTP_X_FORWARDED_FOR']
    ?: @$_SERVER['REMOTE_ADDR'] ?: http_die('请求无效：无法识别来源 ip');

// 3. Work around with the referer.
$from_url = @$_SERVER['HTTP_REFERER'] ?: http_die('请求无效：没有来源页面');

function get_visit_count($ip, $url)
{
    $pdo = Config::pdo();

    $now = time();

    $result = $pdo->query("
        select last_time from hstats_ip_status
        where ip = '$ip' and url = '$url'
        limit 1;
    ");
    if (!$result) die(implode("\t", $pdo->errorInfo()));

    $rows = $result->fetchAll();

    if (sizeof($rows) == 0) {
        $result = $pdo->exec("
            insert into hstats_ip_status (ip, url, last_time)
            values ('$ip', '$url', '$now');
        ");
        if (!$result) http_die(implode("\t", $pdo->errorInfo()));
        increase_count($url);
    } elseif (intval($rows[0][0]) + 24 * 60 * 60 < $now) {
        $result = $pdo->exec("
            update hstats_ip_status
            set last_time = '$now';
            where ip = '$ip' and url = '$url', 
        ");
        if (!$result) die(implode("\t", $pdo->errorInfo()));
        increase_count($url);
    }

    $result = $pdo->query("
        select cnt from hstats_visit_count
        where url = '$url' limit 1;
    ");
    if (!$result) http_die(implode("\t", $pdo->errorInfo()));

    $rows = $result->fetchAll();
    return @intval($rows[0][0]) ?: 0;

}

function increase_count($url)
{
    $pdo = Config::pdo();
    $result = $pdo->exec("
            insert into hstats_visit_count (url, cnt)
            values ('$url', 1)
            ON DUPLICATE KEY UPDATE cnt = cnt + 1;
        ");
    if (!$result) http_die(implode("\t", $pdo->errorInfo()));
}

exit(strval(get_visit_count($ip, $from_url)));



