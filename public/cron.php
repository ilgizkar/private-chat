<?php
function cron() {
    $token = '0b9295c4d383cb0c4bb30332a01aa5cf4762afb777ab07c7371d43c98d95a49712750530d22921557b4e1';

    $message = 'Ежедневное оповещение';

    $query = file_get_contents("https://api.vk.com/method/messages.send?user_id=454162779&message=".urlencode($message)."&v=5.37&access_token=".$token);
}

cron();
