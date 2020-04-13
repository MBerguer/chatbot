<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\RandomConversation;
use App\Conversations\LoginConversation;
use App\Conversations\LogoutConversation;
use App\Conversations\RegisterConversation;
use App\Conversations\StartConversation;
use App\Conversations\HelpConversation;
use App\Conversations\SetDefaultConversation;
use App\Conversations\CheckBalanceConversation;
use App\Conversations\DepositConversation;
use App\Conversations\DepositCurrencyConversation;
use App\Conversations\WithdrawConversation;
use App\Conversations\WithdrawCurrencyConversation;
use App\Conversations\ExchangeConversation;
use App\Logs;


class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function register(BotMan $bot)
    {
        // Logs::create([
        //     type => 'action_command',
        //     message => 'Register new user'
        // ]);

        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'Register new user';
        $log->save();

        $bot->startConversation(new RegisterConversation());
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function login(BotMan $bot)
    {
        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'User login';
        $log->save();
        $bot->startConversation(new LoginConversation());
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function logout(BotMan $bot)
    {
        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'User logout';
        $log->save();
        $bot->startConversation(new LogoutConversation());
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function start(BotMan $bot)
    {
        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'Start';
        $log->save();
        $bot->startConversation(new StartConversation());
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function help(BotMan $bot)
    {
        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'Call for help';
        $log->save();
        $bot->startConversation(new HelpConversation());
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function setDefault(BotMan $bot, $curr)
    {
        $params = [
            'curr' => strtoupper($curr)
        ];

        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'Set default currency';
        $log->save();
        $bot->startConversation(new SetDefaultConversation($params));
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function checkBalance(BotMan $bot)
    {
        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'Checking the balance';
        $log->save();
        $bot->startConversation(new CheckBalanceConversation());
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function deposit(BotMan $bot, $amt)
    {
        $params = [
            'amt' => $amt
        ];
        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'Deposit amount';
        $log->save();
        $bot->startConversation(new DepositConversation($params));
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function depositCurr(BotMan $bot, $curr, $amt)
    {
        $params = [
            'amt' => $amt,
            'curr' => strtoupper($curr)
        ];
        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'Deposit a specific currency';
        $log->save();
        $bot->startConversation(new DepositCurrencyConversation($params));
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function withdraw(BotMan $bot, $amt)
    {
        $params = [
            'amt' => $amt
        ];
        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'Withdrow amount';
        $log->save();
        $bot->startConversation(new WithdrawConversation($params));
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function withdrawCurr(BotMan $bot, $curr, $amt)
    {
        $params = [
            'amt' => $amt,
            'curr' => strtoupper($curr)
        ];
        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'Withdraw a specific currency';
        $log->save();
        $bot->startConversation(new WithdrawCurrencyConversation($params));
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function exchange(BotMan $bot, $valueFrom, $valueTo )
    {
        $params = [
            'valueFrom' => $valueFrom,
            'valueTo' => $valueTo
        ];
        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'Request for a exchange quote';
        $log->save();
        $bot->startConversation(new ExchangeConversation($params));
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function random(BotMan $bot)
    {
        $log = new Logs();
        $log->type = 'ACTION_COMMAND';
        $log->message = 'Random content';
        $log->save();
        $bot->startConversation(new RandomConversation());
    }

}
