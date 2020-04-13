<?php

namespace App\Conversations;

use Validator;
use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Conversations\HelpConversation;


class LogoutConversation extends Conversation
{

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {

        $this->bot->userStorage()->save([
            'name' => '',
            'email' => ''
        ]);

        Auth::logout();

        $this->say(trans('You are now logged out'));

    }
}
