<?php

namespace App\Conversations;

use Validator;
use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Accounts;

class CheckBalanceConversation extends Conversation
{
    public function checkBalance()
    {
        $idUser = Auth::user()->id;
        $acc = Accounts::where('user_id' , $idUser)->first();; //if null, the user does not have an account

        if ($acc)
        {
            $balance = $acc->amount; //by default we will see if this changes below
            $this->say('You have $'.floatval($balance).' on your account');
        }
        else
        {
            $this->say('It seems like you dont have an account with us yet, let me help you with that!');
            $this->bot->startConversation(new StartConversation());
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
            $this->checkBalance();
        }
        else
        {
            $this->say('You need to login or register before using the bot.');
        }
    }
}
