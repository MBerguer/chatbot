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

class DepositConversation extends Conversation
{

    public function deposit()
    {

        $amt = $this->params['amt'];
        $idUser = Auth::user()->id;
        $acc = Accounts::where('user_id' , $idUser)->first();; //if null, the user does not have an account

        if ($acc)
        {
            $oldAmt = $acc->amount; //by default we will see if this changes below
            $newAmt = $oldAmt + $amt;
            $acc->update(array('amount' => $newAmt));
            $this->say('You have successfully deposited $'.floatval($amt).' to your account');
            $log = new Logs();
            $log->type = 'UPDATE_COMMAND';
            $log->message = 'Deposit successfully to account:'. $acc->id;
            $log->save();
        }
        else
        {
            $this->say('It seems like you dont have an account with us yet, let me help you with that!');
            $this->bot->startConversation(new StartConversation());
            $log = new Logs();
            $log->type = 'UPDATE_COMMAND';
            $log->message = 'Deposit from an unexisting.';
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
            $this->deposit();
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
