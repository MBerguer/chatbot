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
use App\Accounts;
use App\Currency;

class SetDefaultConversation extends Conversation
{
    public function setDefault()
    {

        $curr = strtoupper($this->params['curr']);
        $idUser = Auth::user()->id;

        $acc = Accounts::where('user_id' , $idUser)->first(); //if null, the user does not have an account
        $fixedCurr = Currency::where('currency_str' , $curr)->first(); //if null, the user does not have an account

        if (!$fixedCurr)
        {
            $this->say('That currency id is invalid, please try again:');
            return;
        }
        $fixedCurr = $fixedCurr->currency_str;

        if ($acc)
        {
            $oldAmt = $acc->amount; //by default we will see if this changes below
            if($oldAmt>0)
            {
                //cheching the current rate between the two currencies

                $api_key = Config::get('services.amdoren.key');

                $url = "https://www.amdoren.com/api/currency.php?api_key=".$api_key."&from=".$acc->currency."&to=".$fixedCurr;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $json_string = curl_exec($ch);
                $parsed_json = json_decode($json_string);

                $error = $parsed_json->error;
                $error_message = $parsed_json->error_message;
                $rate = $parsed_json->amount;

                //convert the amount of money the user currently has
                if(!$error)
                {
                    $newAmt = $oldAmt * $rate;
                    $acc->update(array('currency' => $fixedCurr, 'amount' => $newAmt));
                }
                else
                {
                    $this->say('ATTENTION: There was an error with the currency API, so (JUST TO TEST) updating the amount a constant rate ($1 old => $2.1 new) ');
                    $this->say('API_ERROR: '. $error_message);
                    $newAmt = $oldAmt * 2.1; // HARDCODED JUST FOR TESTING
                    $acc->update(array('currency' => $fixedCurr, 'amount' => $newAmt));
                }


            }
            else
            {
                // 0 keeps 0
                $newAmt = $oldAmt;
                $acc->update(array('currency' => $fixedCurr));
                $this->say('Default currency successfully set to: '.$fixedCurr);
            }

            $this->say('Now you have '.$fixedCurr. ' ' .$newAmt.' on your account');

            $log = new Logs();
            $log->type = 'SET_DEFAULT_OK';
            $log->message = 'Account:'.$acc->id;
            $log->save();

        }
        else //First time, set a default account
        {
            $acc = new Accounts();
            $acc->user_id = $idUser;
            $acc->amount = 0;
            $acc->currency = $fixedCurr;
            $acc->save();
            $this->say('Default currency successfully set to '.$fixedCurr);
            $this->say('Remember that you can call for /help on any moment. I will answer depending on the context you are currently stranded.');

            $log = new Logs();
            $log->type = 'SET_DEFAULT_OK';
            $log->message = 'Account creation for user:'.$idUser;
            $log->save();
        };
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
            $this->setDefault();
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
