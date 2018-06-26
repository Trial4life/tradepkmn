<?php

// PARAMETRI DA MODIFICARE
$WEBHOOK_URL = 'https://tradepkmn.herokuapp.com/execute.php';
$BOT_TOKEN = '426704509:AAFGRQmqKWz9ywUj--pkYHA0CC1d_SJ3qPU';

// NON APPORTARE MODIFICHE NEL CODICE SEGUENTE
$API_URL = 'https://api.telegram.org/bot' . $BOT_TOKEN .'/';
$method = 'setWebhook';
$parameters = array('url' => $WEBHOOK_URL);
$url = $API_URL . $method. '?' . http_build_query($parameters);
$handle = curl_init($url);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 0);
curl_setopt($handle, CURLOPT_TIMEOUT, 120);
$result = curl_exec($handle);
print_r($result);
