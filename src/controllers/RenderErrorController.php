<?php


namespace App\Controllers;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class RenderErrorController extends BaseController
{
    public static function render404Error()
    {
        $loader = new FilesystemLoader('../templates');
        $twig = new Environment($loader);
        echo $twig->render('404.twig');
    }
}