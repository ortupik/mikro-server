<?php
$callbackData = file_get_contents('php://input');
$res = json_decode($callbackData, true);

file_put_contents('stk_callback.log', print_r($res, true), FILE_APPEND);
