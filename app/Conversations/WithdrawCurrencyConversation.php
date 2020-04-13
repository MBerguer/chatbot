<?php

namespace App\Conversations;

use Validator;
use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Accounts;
use App\Currency;
use App\Logs;

class WithdrawCurrencyConversation extends Conversation
{

    public function withdraw()
    {
        $amt = $this->params['amt'];
        $curr = strtoupper($this->params['curr']);

        $idUser = Auth::user()->id;
        $acc = Accounts::where('user_id' , $idUser)->first();; //if null, the user does not have an account
        $fixedCurr = Currency::where('currency_str' , $curr)->first(); //if null, the user does not have an account

        if (!$fixedCurr)
        {

            //just in case the user entered the values in the wrong order
            $amt = $this->params['curr'];
            $curr = strtoupper($this->params['amt']);

            $idUser = Auth::user()->id;
            $acc = Accounts::where('user_id' , $idUser)->first();; //if null, the user does not have an account
            $fixedCurr = Currency::where('currency_str' , $curr)->first(); //if null, the user does not have an account
            // $this->say('[Deposit - backwords] amt:'.$amt.' fixedCurr:'.$fixedCurr);

            if (!$fixedCurr)
            {
                $this->say('That currency id is invalid, please try again:');
                return;
            }
        }
        $fixedCurr = $fixedCurr->currency_str;


        if ($acc)
        {
            $oldAmt = $acc->amount; //by default we will see if this changes below


            if($amt>0)
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
                    $convAmt = ($amt * $rate);
                }
                else
                {
                    $this->say('ATTENTION: There was an error with the currency API, so (JUST TO TEST) updating the amount a constant rate ($1 old => $2.1 new) ');
                    $this->say('API_ERROR: '. $error_message);
                    $convAmt = ($amt * 2.1);
                }

                $balance = $acc->amount;
                if($amt<=$balance){
                    $newAmt = $oldAmt - $convAmt;
                    $acc->update(array('amount' => $newAmt));
                    $this->say('You have successfully withdrawn '.$acc->currency.' '.floatval($convAmt).' ('.$fixedCurr.' '.$amt.') from your account');
                    $log = new Logs();
                    $log->type = 'UPDATE_COMMAND';
                    $log->message = 'Withdraw from the account: '.$acc->id;
                    $log->save();
                }else{
                    $this->say('Insufficient funds: you dont have that amount currently on your account.');
                    $log = new Logs();
                    $log->type = 'UPDATE_COMMAND';
                    $log->message = 'Withdraw attempt failed from the account: '.$acc->id;
                    $log->save();
                }

            }


        }
        else
        {
            $this->say('It seems like you dont have an account with us yet, let me help you with that!');
            $this->bot->startConversation(new StartConversation());
            $log = new Logs();
            $log->type = 'UPDATE_COMMAND';
            $log->message = 'Withdraw from an unexisting.';
            $log->save();
        }


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
            $this->withdraw();
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
