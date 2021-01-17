<?php

namespace App\Controllers;

use App\Models\TasksModel;
use DateTime;

class TasksController extends BaseController
{
    const PAGE_STEP = 3;

    const TASK_STATUS_PROCESS = "process";
    const TASK_STATUS_COMPLETED = "completed";

    const TASK_EDITED_NO = '';
    const TASK_EDITED_YES = 'edited';

    public static function createNewTask()
    {
        $email = (string)$_REQUEST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['code' => -1, 'message' => "Incorrect email form."]);
            return;
        }
        $username = (string)$_REQUEST['username'];
        $text = (string)$_REQUEST['text'];
        $date = new DateTime();
        $createdAt = $date->format('Y-m-d H:i:s');
        $model = new TasksModel();
        $model->createNewTask($username, $email, $createdAt, $text, self::TASK_STATUS_PROCESS, self::TASK_EDITED_NO);
        echo json_encode(['code' => 0, 'message' => "Task was created."]);
    }

    public static function getTasksByPage($pageNumber, $sortType, $sortRevers)
    {
        $desiredPage = max($pageNumber - 1, 0);

        if ($sortRevers == 1) {
            $sortRevers = 'DESC';
        } else {
            $sortRevers = 'ASC';
        }
        $sortType = self::getSortTypeByIndex($sortType);
        $first = $desiredPage * self::PAGE_STEP;
        $tasksModel = new TasksModel();
        $tasks = $tasksModel->getTasksByPage($first, self::PAGE_STEP, $sortType, $sortRevers);
        $pagesCount = $tasksModel->getPagesCount();
        echo json_encode(['code' => 0, 'message' => 'success', 'tasks' => $tasks, 'pagesCount' => $pagesCount]);
    }

    public static function deleteTask(int $id)
    {
        if (self::userVerification()) {
            $tasksModel = new TasksModel();
            $result = $tasksModel->deleteTask($id);
            echo json_encode(['code' => 0, 'message' => $result]);
            return;
        }
        echo json_encode(['code' => -1]);
    }

    public static function updateTaskText()
    {
        if (self::userVerification()) {
            $id = (int)$_REQUEST['id'];
            $text = (string)$_REQUEST['text'];
            $tasksModel = new TasksModel();
            $result = $tasksModel->updateTaskText($id, $text, self::TASK_EDITED_YES);
            echo json_encode(['code' => 0, 'message' => $result]);
            return;
        }
        echo json_encode(['code' => -1]);
    }

    public static function completedTask()
    {
        if (self::userVerification()) {
            $id = (int)$_REQUEST['id'];
            $tasksModel = new TasksModel();
            $result = $tasksModel->updateTaskStatus($id, self::TASK_STATUS_COMPLETED);
            echo json_encode(['code' => 0, 'message' => $result]);
            return;
        }
        echo json_encode(['code' => -1]);
    }

    private static function getSortTypeByIndex($index)
    {
        switch ($index) {
            case 1:
            {
                return 'email';
            }
            case 2:
            {
                return 'user';
            }
            case 3:
            {
                return 'status';
            }
            default:
            {
                return 'createdAt';
            }
        }
    }
}
