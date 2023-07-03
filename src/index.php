<?php
//вывод ошибок для анализа бота
ini_set('error_reporting', E_ALL);
ini_set('display_reporting', 1);
ini_set('display_startup_errors', 1);
//присваиваю переменным токен бота и свой id TG
define("TG_TOKEN", "6283914039:AAGtKlNmN-Wz90CozhggO9oEMyQJ_U_Aa2A");
define("TG_USER_ID", "1257048976");

/*------------- */
// // создаю веб-хук и комментирую
// define("TG_TOKEN", "6283914039:AAGtKlNmN-Wz90CozhggO9oEMyQJ_U_Aa2A");
// define("TG_USER_ID", "1257048976");

// $getQuery = array(
//      "url" => "https://last-try.ru/LastTry/index.php",
// );
// $ch = curl_init("https://api.telegram.org/bot". TG_TOKEN ."/setWebhook?" . http_build_query($getQuery));
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_HEADER, false);

// $resultQuery = curl_exec($ch);
// curl_close($ch);

// echo $resultQuery;
/*------------- */

//функция записи json сообщения в .txt
function writeLogFile($string, $clear = true){
    $log_file_name = __DIR__."/message.txt";
    $now = date("Y-m-d H:i:s");
    if($clear == false) {
		file_put_contents($log_file_name, $now." ".print_r($string, true)."\r\n", FILE_APPEND);
    }
    else {
		file_put_contents($log_file_name, '');
        file_put_contents($log_file_name, $now." ".print_r($string, true)."\r\n", FILE_APPEND);
    }
}
//запись(сбор) данных пользователя в переменную data
$data = file_get_contents('php://input');
$data = json_decode($data, true);

//запись в лог файл .txt
writeLogFile($data, true);
// Теперь выведем полученную информацию на страницу
echo file_get_contents(__DIR__."/message.txt");

// /* для отправки текстовых сообщений */
// function TG_sendMessage($getQuery) {
//     $ch = curl_init("https://api.telegram.org/bot". TG_TOKEN ."/sendMessage?" . http_build_query($getQuery));
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($ch, CURLOPT_HEADER, false);
//     $res = curl_exec($ch);
//     curl_close($ch);

//     return $res;
// }
// /*------------- */

$data = file_get_contents('php://input');
$data = json_decode($data, true);

//запрос на отправку сообщений пользователю бота
function sendTelegram($method, $response)
{
	$ch = curl_init('https://api.telegram.org/bot' . TG_TOKEN . '/' . $method);  
	curl_setopt($ch, CURLOPT_POST, 1);  
	curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$res = curl_exec($ch);
	curl_close($ch);
 
	return $res;
}
//обработка сообщений и команды /start
if (!empty($data['message']['text'])) {
	$text = $data['message']['text'];	
    if (mb_stripos($text, '/start') !== false) {
		sendTelegram(
			'sendMessage', 
			array(
				'chat_id' => $data['message']['chat']['id'],
				'text' => 'Добро пожаловать! Это бот, который может найти названия университетов по названию страны на кириллице(на английском).'
			)
		);
        exit();
	} elseif (mb_stripos($text, !"/start") !== false) {
        $str = 'http://universities.hipolabs.com/search?country=text';
        $result = str_replace('text', $text, $str);
        $json = file_get_contents($result);
        $arr = json_decode($json, true);
        $names = array_column($arr, 'name');
        $i = 1;
        foreach ( $names as $uni){
        if ($i==5) break;
        sendTelegram(
            'sendMessage', 
            array(
                'chat_id' => $data['message']['chat']['id'],
                'text' => 'Ваш университет: ' .$uni
            ),
        );
        $i++;
        }
        
	    exit();
    
    } else {
		sendTelegram(
			'sendMessage', 
			array(
				'chat_id' => $data['message']['chat']['id'],
				'text' => 'Неверно введено название страны, попробуйте еще раз.'
	    	)
	    );
    }
	exit();
}