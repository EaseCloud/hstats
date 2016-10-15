<?php

/**
 * @param $msg
 * @return int
 */
function http_die($msg) {
    @header('HTTP/1.1 400 Bad Request');
    return intval(die($msg));
}