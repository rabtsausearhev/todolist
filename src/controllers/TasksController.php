<?php

namespace App\Controllers;

use App\Models\TasksModel;
use DateTime;

class TasksController extends BaseController
{
    /** @var int - number of tasks per page */
    const PAGE_STEP = 3;

    /** @var string  - the status of a task that is still in progress */
    const TASK_STATUS_PROCESS = "process";
    /** @var string  - the status of a task that has already been completed */
    const TASK_STATUS_COMPLETED = "completed";

    /** @var string - the status of the task that has not been edited */
    const TASK_EDITED_NO = '';
    /** @var string - the status of the task that was edited */
    const TASK_EDITED_YES = 'edited';


    /**
     * processing a request to create a new task
     */
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

    /**
     * @param $pageNumber - desired page number
     * @param $sortType - requested sort type
     * @param $sortRevers - requested sort sequence
     *
     * processing a request for tasks that are within the sorting conditions
     */
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

    /**
     * @param int $id - id tasks to delete
     *
     * processing a request to delete a task by ID
     */
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

    /**
     * processing a request to update text in task by ID
     */
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

    /**
     * processing a request to assign a task the status "complete"
     */
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

    /**
     * @param int $index
     * @return string
     *
     * selection of the sort type depending on the provided index
     */
    private static function getSortTypeByIndex(int $index)
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
