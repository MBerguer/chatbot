<?php

namespace App\Conversations;

use Validator;
use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Conversations\StartConversation;
use App\Logs;

class RegisterConversation extends Conversation
{
    public function askName()
    {
        $this->ask(trans('Hello! What is your Name?'), function (Answer $answer) {
            $this->bot->userStorage()->save([
                'name' => $answer->getText(),
            ]);

            $this->say(trans('Nice to meet you ').$answer->getText());
            $this->askEmail();
        });

    }

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

            $name = $this->bot->userStorage()->get('name');
            $email = $this->bot->userStorage()->get('email');
            $password = $this->bot->userStorage()->get('password');

            $user_validator = Validator::make(['name' => $name, 'email' => $email, 'password' => $password], [
                'email' => 'email', 'name' => 'required', 'password' => 'required',
            ]);

            if ($user_validator->fails()) {
                return $this->repeat(trans("Wrong data, start the Register process again"));
            }

            $existingUser = User::where('email', $email)->first();
			if (isset($existingUser) && $existingUser->name) {
                $this->say('That email is already used');
                $this->say('Try to login using the /login command');
                $this->bot->userStorage()->save([
                    'password' => '',
                    'email' => '',
                    'name' => '',
                ]);

                $log = new Logs();
                $log->type = 'USER_CREATED_FAIL';
                $log->message = 'Email user already taken:'.$email;
                $log->save();

			} else {
                $user_created =User::create(['name' => $name, 'email' => $email, 'password' => $password]);
                Auth::login($user_created, true);
			}

            if(Auth::check())
            {
                $this->say('Your user account was successfully created, you can start to use the bot now!');
                $this->bot->startConversation(new StartConversation());

                $log = new Logs();
                $log->type = 'USER_CREATED_OK';
                $log->message = 'User:'.$email;
                $log->save();
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
            $this->say('You are currently loggedin. Please, log out to create a new account');
        }
        else
        {
            $this->askName();
        }
    }
}
