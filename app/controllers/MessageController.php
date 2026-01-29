<?php

class MessageController   
{
    public static function showMessages()
    {
        Flight::render('dist-modern/messages');
    }
}