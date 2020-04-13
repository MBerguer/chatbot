<?php
use App\Http\Controllers\BotManController;
use BotMan\BotMan\BotManFactory;
use App\Logs;


$botman = resolve('botman');

$botman->hears('/login', BotManController::class.'@login');

$botman->hears('/register', BotManController::class.'@register');

$botman->hears('/logout', BotManController::class.'@logout');

$botman->hears('/help', BotManController::class.'@help');

$botman->hears('/set {curr}', BotManController::class.'@setDefault');

$botman->hears('/check', BotManController::class.'@checkBalance');

$botman->hears('/deposit (\d*\.?\d*?)', BotManController::class.'@deposit');

$botman->hears('/deposit (\d*\.?\d*?) {curr}', BotManController::class.'@depositCurr');

$botman->hears('/withdraw (\d*\.?\d*?)', BotManController::class.'@withdraw');

$botman->hears('/withdraw (\d*\.?\d*?) {curr}', BotManController::class.'@withdrawCurr');

$botman->hears('/exchange {from} {to}', BotManController::class.'@exchange');

$botman->hears('/random', BotManController::class.'@random');

$botman->fallback(function($bot) {

    $log = new Logs();
    $log->type = 'ACTION_COMMAND';
    $log->message = 'FALLBACK:: Invalid command';
    $log->save();

    $bot->reply('Invalid command Please use one of the commands below:');
    $bot->reply('/set <curr*> :: to set a default currency to your account');
    $bot->reply('/check :: to check the balance in your account');
    $bot->reply('/deposit <amuont*> <curr ?> :: to deposit a certain amount (Optional: you can also deposit different currencies, the bot will automatically convert the amount to your default currency)');
    $bot->reply('/withdraw <amuont*> <curr ?> :: to withdraw money from your account (Optional: you can also deposit different currencies, the bot will automatically convert the amount to your default currency)');
    $bot->reply('/exchange <curr*> <curr*> :: to see the real-time quote between the two currencies');
});

