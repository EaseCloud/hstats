<?php

require_once 'bootstrap.inc.php';

function install()
{
    $dbname = Config::$db_name;

    $pdo = Config::pdo('');

    $sql = "
        CREATE DATABASE  IF NOT EXISTS `$dbname` /*!40100 DEFAULT CHARACTER SET utf8 */;
        USE `$dbname`;

        CREATE TABLE IF NOT EXISTS `hstats_ip_status` (
          `ip` varchar(45) NOT NULL,
          `url` varchar(255) NOT NULL,
          `last_time` int(11) NOT NULL,
          PRIMARY KEY (`ip`, `url`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        CREATE TABLE IF NOT EXISTS `hstats_visit_count` (
          `url` varchar(255) NOT NULL,
          `cnt` bigint(20) NOT NULL DEFAULT '0',
          PRIMARY KEY (`url`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

    $result = $pdo->exec($sql);

    if ($result == 1) {
        echo '安装成功';
    } else {
        echo '安装动作没有执行，原因：' . $result;
    }

}

install();

