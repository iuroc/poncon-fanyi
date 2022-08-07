<?php

/**
 * 翻译API
 */
require 'Poncon.php';

use Poncon\Poncon;

$poncon = new Poncon();
$input = file_get_contents('php://input');
$input = json_decode($input, true);
$from = isset($input['from']) ? $input['from'] : 'auto';
$to = isset($input['to']) ? $input['to'] : 'auto';
$text = isset($input['text']) ? $input['text'] : 'hello';
$text_encoded = urlencode($text);

// 发送请求 获取翻译结果
$cookie_id = rand(0, 9) . "@" . rand(0, 9) . "." . rand(0, 9) . "." . rand(0, 9) . "." . rand(0, 9);
$salt = time() . '0000';
$sign = md5("fanyideskweb{$text}{$salt}Ygy_4c=r#e#4EX^NUGUc5");
$lts = time() . '000';
$options = array(
    'http' => array(
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\nUser-Agent: oyp\nReferer: oyp\nCookie: OUTFOX_SEARCH_USER_ID=$cookie_id;",
        'content' => "i=$text_encoded&from=$from&to=$to&smartresult=dict&client=fanyideskweb&salt=$salt&sign=$sign&doctype=json&version=2.1&keyfrom=fanyi.web&action=FY_BY_REALTlME&lts=$lts",
        'timeout' => 900
    ),
    "ssl" => array(
        "verify_peer" => false,
        "verify_peer_name" => false,
    )
);
$context = stream_context_create($options);
$result = file_get_contents('https://fanyi.youdao.com/translate_o?smartresult=dict&smartresult=rule', false, $context);
echo $result;