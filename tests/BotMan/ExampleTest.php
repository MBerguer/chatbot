<?php

namespace Tests\BotMan;

use Illuminate\Foundation\Inspiring;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    //this tests depends if the user was logged in or not, so testing just for a few commands

    /**
     * A basic test example.
     *
     * @return void
     */
    public function LoginTest()
    {
        $this->bot
            ->receives('/login')
            ->assertReply('What is your E-mail Address?');
    }

    /**
     * A conversation test example.
     *
     * @return void
     */
    public function HelpTest()
    {
        $this->bot
            ->receives('/help')
            ->assertReply('This is the list of commands you are currently able to perform:')
            ->assertReply('/login :: to login using email/password')
            ->assertReply('/register:: to register with a new account')
            ->assertReply('/random:: to interact randomly with the bot');
    }

    /**
     * A conversation test example. for the RANDOM command
     *
     * @return void
     */
    public function testConversation()
    {
        $quotes = [
            'When there is no desire, all things are at peace. - Laozi',
            'Simplicity is the ultimate sophistication. - Leonardo da Vinci',
            'Simplicity is the essence of happiness. - Cedric Bledsoe',
            'Smile, breathe, and go slowly. - Thich Nhat Hanh',
            'Simplicity is an acquired taste. - Katharine Gerould',
            'Well begun is half done. - Aristotle',
            'He who is contented is rich. - Laozi',
            'Very little is needed to make a happy life. - Marcus Antoninus',
            'It is quality rather than quantity that matters. - Lucius Annaeus Seneca',
            'Genius is one percent inspiration and ninety-nine percent perspiration. - Thomas Edison',
            'Computer science is no more about computers than astronomy is about telescopes. - Edsger Dijkstra',
        ];

        $this->bot
            ->receives('Start Conversation')
            ->assertQuestion('Huh - you woke me up. What do you need?')
            ->receivesInteractiveMessage('quote')
            ->assertReplyIn($quotes);
    }
}
