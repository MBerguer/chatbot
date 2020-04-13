<?php

namespace App\Conversations;

use Validator;
use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Logs;

class ExchangeConversation extends Conversation
{

    public function exchange()
    {

        $valueFrom = $this->params['valueFrom'];
        $valueTo = $this->params['valueTo'];

        $api_key = Config::get('services.amdoren.key');

        $url = "https://www.amdoren.com/api/currency.php?api_key=".$api_key."&from=".$valueFrom."&to=".$valueTo;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $json_string = curl_exec($ch);
        $parsed_json = json_decode($json_string);

        $error = $parsed_json->error;
        $error_message = $parsed_json->error_message;
        $amount = $parsed_json->amount;

        if ($error){
            $this->say('ERROR: '.$error_message);
        } else {
            $this->say('Rate: '.$amount);
        }

        $log = new Logs();
        $log->type = 'EXCHANGE_SERVICE';
        $log->message = 'From: '.$valueFrom.' To: '.$valueTo;
        $log->save();

    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $email = $this->bot->userStorage()->get('email'); //from the user chat object.

        if ($email){
            $existingUser = User::where('email', $email)->first();
            $lgin = Auth::login($existingUser, true);
        }

        if(Auth::check())
        {
            $this->exchange();
        }
        else
        {
            $this->say('You need to login or register before using the bot.');
        }
    }

    protected $params;

    public function __construct($params) {
        $this->params = $params;
    }
}
