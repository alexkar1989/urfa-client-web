<?php
#Laravel example

URFA_UTM_VERSION=5.3 //Версия ядра
URFA_UTM_HOST=172.24.130.80 //Ип ядра UTM
URFA_URL=http://localhost:14542/urfa/
URFA_KEY=Noxaigh0aighadaik0ku //Ключ api
URFA_LOGIN=user //Системный пользователь ядра 
URFA_PASSWORD=ohfaa6i //Пароль Системного пользователя ядра

return [
    'utm' => [
        'version' => env('URFA_UTM_VERSION', '5.2'),
        'host' => env('URFA_UTM_HOST', '127.0.0.1'),
    ],
    'url' => env('URFA_URL', 'localhost'),
    'key' => env('URFA_KEY'),
    'login' => env('URFA_LOGIN', 'user'),
    'password' => env('URFA_PASSWORD', 'password'),
];

#CLI
/** In progress*/

#WEB
function urfaQuery($function, $query)
{
    $data = [];
    $data['system']['version'] = Config::get('urfa.utm.version');
    $data['system']['host'] = Config::get('urfa.utm.host');
    $data['system']['login'] = Config::get('urfa.login');
    $data['system']['password'] = Config::get('urfa.password');
    $data['system']['function'] = $function;
    $data['data'] = $query;
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-type: application/x-www-form-urlencoded\r\napikey: " . Config::get('urfa.key'),
            'content' => http_build_query($data),
        ],
    ];
    return json_decode(file_get_contents(Config::get('urfa.url'), false, stream_context_create($opts)), true);
}

$query = ['user_id' => $userId];
$result = urfaQuery('rpcf_edit_user_new', $query);
