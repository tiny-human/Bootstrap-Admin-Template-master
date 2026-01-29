<?php


class IndexController   
{
    public static function showIndex()
    {
        Flight::render('dist-modern/home');
    }
}