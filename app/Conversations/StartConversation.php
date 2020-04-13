<?php

namespace App\Conversations;

use Validator;
use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class StartConversation extends Conversation
{
    public function start()
    {
        // $this->say('Hello again!...');
        $this->say('Your next step now is to set a default currency for your account. you can find more information typing /help');
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
            $this->start();
        }
        else
        {
            $this->say('You need to login or register before using the bot.');
        }
    }
}
