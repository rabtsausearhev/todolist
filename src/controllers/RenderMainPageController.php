<?php

namespace App\Controllers;

use App\Models\TasksModel;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class RenderMainPageController extends BaseController
{
    const START_LIST = 0;
    const DEFAULT_SORT_TYPE = 'createdAt';
    const DEFAULT_SORT_REVERS = 'ASC';

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * processing a home page request
     */
    public static function renderMainPage()
    {
        $isAdmin = false;
        $role = 'unknown';
        if(self::userVerification()){
                $isAdmin = true;
                $role = 'admin';
        }
        $tasksModel = new TasksModel();
        $tasks = $tasksModel->getTasksByPage(self::START_LIST, TasksController::PAGE_STEP, self::DEFAULT_SORT_TYPE,self::DEFAULT_SORT_REVERS);
        $pagesCount = $tasksModel->getPagesCount();
        $loader = new FilesystemLoader('../templates');
        $twig = new Environment($loader);
        echo $twig->render('main.twig', ['isAdmin' => $isAdmin, 'role' => $role, 'tasks' => $tasks,'pagesCount'=>$pagesCount]);
    }
}
