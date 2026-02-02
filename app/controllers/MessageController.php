<?php

namespace app\controllers;
use Flight;

class MessageController   
{
    public static function showMessages()
    {
        Flight::render('dist-modern/messages');
    }
}