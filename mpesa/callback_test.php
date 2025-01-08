<?php
$callbackData = file_get_contents('php://input');
$res = json_decode($callbackData, true);

file_put_contents('status.log', print_r($res, true), FILE_APPEND);
