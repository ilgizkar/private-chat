<?php

namespace App\Http\Controllers;

error_reporting(0);
use App\Category;
use App\Video;
use Illuminate\Http\Request;

class VkController extends Controller
{
    public function vk_api()
    {
        $token = env('VK_TOKEN');
        $callback_code = env('VK_CALLBACK_CODE');
        $data = json_decode(file_get_contents('php://input'));
        $userId = $data->object->user_id;
        $userInfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids=".$userId."&v=5.50&access_token=".$token));
        $user_name = $userInfo->response[0]->first_name;

        switch ($data->type) {

            case 'confirmation':
                return response($callback_code, 200);
                break;

            case 'message_new':

                switch ($data->object->body) {

                    case 'На главную':
                    case 'Поиск по названию':
                        $this->sendOnlyMessage($userId, 'Введите название фильма для поиска', 'main');
                        break;

                    case 'Поиск по жанру':
                        $this->sendOnlyMessage($userId, 'Выберите поджанр фильма для поиска', 'category');
                        break;

                    case 'Мистика':
                        $video = Video::getRandomVideo('Мистика');
                        $this->sendVideo($userId, $video, 'category');
                        break;

                    case 'Маньяки':
                        $video = Video::getRandomVideo('Маньяки');
                        $this->sendVideo($userId, $video, 'category');
                        break;

                    case 'Зомби':
                        $video = Video::getRandomVideo('Зомби');
                        $this->sendVideo($userId, $video, 'category');
                        break;

                    case 'Вампиры':
                        $video = Video::getRandomVideo('Вампиры');
                        $this->sendVideo($userId, $video, 'category');
                        break;

                    case 'Трэш':
                        $video = Video::getRandomVideo('Трэш');
                        $this->sendVideo($userId, $video, 'category');
                        break;

                    case 'Монстры':
                        $video = Video::getRandomVideo('Монстры');
                        $this->sendVideo($userId, $video, 'category');
                        break;

                    default:
                        $video = Video::findVideo($data->object->body);
                        if($video) {
                            $this->sendVideo($userId, $video, 'main');
                            break;
                        } else {
                            $this->sendOnlyMessage($userId, 'Видео не найденно, попробуйте ввести другое название', 'main');
                            break;
                        }

                }
        }

    }

    public function show()
    {
        return 1111;
    }

    protected function sendVideo($userId, $video, $keyboard_type)
    {
        $token = env('VK_TOKEN');
        $request_params = array(
            'message' => $video->name.' ('.$video->date.')
                    
'.$video->description,
            'user_id' => $userId,
            'access_token' => $token,
            'attachment' => $video->media,
            'v' => '5.50',
            'keyboard' => json_encode($this->getKeyboard($keyboard_type), JSON_UNESCAPED_UNICODE)
        );
        $get_params = http_build_query($request_params);
        file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
        return response('ok', 200);
    }

    public function sendOnlyMessage($userId, $message, $keyboard_type)
    {
        $token = env('VK_TOKEN');
        $request_params = array(
            'message' => $message,
            'user_id' => $userId,
            'access_token' => $token,
            'v' => '5.50',
            'keyboard' => json_encode($this->getKeyboard($keyboard_type), JSON_UNESCAPED_UNICODE)
        );
        $get_params = http_build_query($request_params);
        file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
        return response('ok', 200);
    }

    protected function getKeyboard($type)
    {
        switch ($type) {
            case 'default':
                return [
                    "one_time" => false,
                    "buttons" => [[
                        ["action" => [
                            "type" => "text",
                            "label" => "Другой фильм"],
                            "color" => "primary"],
                        ["action" => [
                            "type" => "text",
                            "label" => "На главную"],
                            "color" => "default"],
                    ]]];
                break;

            case 'main':
                return [
                    "one_time" => false,
                    "buttons" => [[
                        ["action" => [
                            "type" => "text",
                            "label" => "Поиск по жанру"],
                            "color" => "primary"],
                    ]]];
                break;

            case 'category':
                return [
                    "one_time" => false,
                    "buttons" => [[
                        ["action" => [
                            "type" => "text",
                            "label" => "Мистика"],
                            "color" => "primary"],
                        ["action" => [
                            "type" => "text",
                            "label" => "Маньяки"],
                            "color" => "primary"],
                        ["action" => [
                            "type" => "text",
                            "label" => "Зомби"],
                            "color" => "primary"],
                    ], [
                        ["action" => [
                            "type" => "text",
                            "label" => "Вампиры"],
                            "color" => "primary"],
                        ["action" => [
                            "type" => "text",
                            "label" => "Трэш"],
                            "color" => "primary"],
                        ["action" => [
                            "type" => "text",
                            "label" => "Монстры"],
                            "color" => "primary"],
                    ],[
                        ["action" => [
                            "type" => "text",
                            "label" => "Поиск по названию"],
                            "color" => "primary"],
                    ]]];
                break;
        }

    }
}
