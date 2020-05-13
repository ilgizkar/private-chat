<?php
function cron() {
    $token =env('VK_TOKEN');

    $message = 'Вам сообщение от sdfdfs: 
        
        dgfgdfgdfgdfgdf
        
         https://ilgizkar.ru/home';

    $query = file_get_contents("https://api.vk.com/method/messages.send?user_id=454162779&message=".urlencode($message)."&v=5.37&access_token=".$token);
}

cron();
