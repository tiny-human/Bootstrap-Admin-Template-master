<?php

namespace app\Controllers;
use Flight;
class IndexController   
{
    public static function showIndex()
    {
        Flight::render('dist-modern/home');
    }
}