<?php

namespace App\Conversations;

use Validator;
use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Logs;

class HelpConversation extends Conversation
{

    public function helpLoggedIn()
    {
        $this->say('/set <curr*> :: to set a default currency to your account');
        $this->say('/check :: to check the balance in your account');
        $this->say('/deposit <amuont*> <curr ?> :: to deposit a certain amount (Optional: you can also deposit different currencies, the bot will automatically convert the amount to your default currency)');
        $this->say('/withdraw <amuont*> <curr ?> :: to withdraw money from your account (Optional: you can also deposit different currencies, the bot will automatically convert the amount to your default currency)');
        $this->say('/exchange <curr> <curr> :: to see the real-time quote between the two currencies');
        $this->say('/logout :: to logout');
    }

    public function helpLoggedOut()
    {
        $this->say('/login :: to login using email/password');
        $this->say('/register:: to register with a new account');
        $this->say('/random:: to interact randomly with the bot');
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {

        // $logs = Logs::all();
        // foreach($logs as $log){
        //     $this->say('asd:'. $log->type);
        // };

        $email = $this->bot->userStorage()->get('email'); //from the user chat object.

        if ($email){
            $existingUser = User::where('email', $email)->first();
            $lgin = Auth::login($existingUser, true);
        }

        $this->say('This is the list of commands you are currently able to perform:');

        if(Auth::check())
        {
            $log = new Logs();
            $log->type = 'HELP_LOGGED_IN';
            $log->message = 'User:'.Auth::user()->id;
            $log->save();
            $this->helpLoggedIn();
        }
        else
        {
            $log = new Logs();
            $log->type = 'HELP_LOGGED_OUT';
            $log->message = '';
            $log->save();
            $this->helpLoggedOut();
        }
    }
}
