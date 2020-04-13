<?php

namespace App\Conversations;

use Validator;
use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Accounts;
use App\Logs;

class WithdrawConversation extends Conversation
{

    public function withdraw()
    {

        $amt = $this->params['amt'];
        $idUser = Auth::user()->id;
        $acc = Accounts::where('user_id' , $idUser)->first();; //if null, the user does not have an account

        if ($acc)
        {
            $balance = $acc->amount;
            if($amt<=$balance){
                $oldAmt = $acc->amount; //by default we will see if this changes below
                $newAmt = $oldAmt - $amt;
                $acc->update(array('amount' => $newAmt));
                $this->say('You have successfully withdrawn $'.floatval($amt).' from your account');
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
