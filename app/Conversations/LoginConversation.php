<?php

namespace App\Conversations;

use Validator;
use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Conversations\HelpConversation;
use App\Logs;

class LoginConversation extends Conversation
{
    public function askEmail()
    {
        $this->ask(trans('What is your E-mail Address?'), function (Answer $answer) {
            $validator = Validator::make(['email' => $answer->getText()], [
                'email' => 'email',
            ]);

            if ($validator->fails()) {
                return $this->repeat(trans("That doesn't look like a valid email. Please enter a valid email."));
            }

            $this->bot->userStorage()->save([
                'email' => $answer->getText(),
            ]);

            $this->askPassword();

        });
    }

    public function askPassword()
    {
        $this->ask(trans('Enter Password'), function (Answer $answer) {


            $this->bot->userStorage()->save([
                'password' => $answer->getText(),
            ]);

            $email = $this->bot->userStorage()->get('email');
            $password = $this->bot->userStorage()->get('password');

            $user_validator = Validator::make(['email' => $email, 'password' => $password], [
                'email' => 'email', 'password' => 'required',
            ]);

            if ($user_validator->fails()) {
                return $this->repeat(trans("Wrong data, start the login again"));
            }


            $existingUser = User::where(array('email' => $email, 'password' => $password))->first();
			if (isset($existingUser) && $existingUser->name) {
                Auth::login($existingUser, true);
                $log = new Logs();
                $log->type = 'LOGIN_OK';
                $log->message = 'User:'.Auth::user()->id;
                $log->save();
			} else {
                Auth::logout();
                $this->say('Invalid email/password, try again');
                $log = new Logs();
                $log->type = 'LOGIN_FAIL';
                $log->message = 'User:'.Auth::user()->id;
                $log->save();
			}


            if(Auth::check())
            {
                $this->bot->userStorage()->save([
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ]);
                $this->say(trans('You are currently logged in').' '.Auth::user()->name.'! ');
                $this->bot->startConversation(new HelpConversation());
            }else{
                $this->bot->userStorage()->save([
                    'name' => '',
                    'email' => ''
                ]);
            }
        });
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
            $this->bot->userStorage()->save([
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ]);
            $this->say(trans('You are currently logged in').' '.Auth::user()->name.'! ');
            $this->bot->startConversation(new HelpConversation());
        }
        else
        {
            $this->askEmail();
        }
    }
}
