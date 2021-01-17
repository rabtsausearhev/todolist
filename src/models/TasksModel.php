<?php


namespace App\Models;


use App\Controllers\TasksController;
use App\Services\DbService;
use PDO;

class TasksModel
{
    public function createNewTask(string $username, string $email, string $createAt, string $text, string $status, string $edited )
    {
        $db = DbService::getConnection();
        $sql = "insert into task (user, email, text, createdAt, status, edited) values (:a, :b, :c, :d, :e, :f)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('a', $username, PDO::PARAM_STR);
        $stmt->bindParam('b', $email, PDO::PARAM_STR);
        $stmt->bindParam('c', $text, PDO::PARAM_STR);
        $stmt->bindParam('d', $createAt, PDO::PARAM_STR);
        $stmt->bindParam('e', $status, PDO::PARAM_STR);
        $stmt->bindParam('f', $edited, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getTasksByPage(int $offset, int $limit, string $sortType, string $sortRevers)
    {
        $db = DbService::getConnection();
        $sql = "select * from (select * from task order by $sortType $sortRevers) as allrow limit :a offset :b";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('a', $limit, PDO::PARAM_INT);
        $stmt->bindParam('b', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPagesCount()
    {
        $db = DbService::getConnection();
        $sql = "select count(*) from task";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = (int)$stmt->fetch()[0];
        return ceil($result / TasksController::PAGE_STEP);
    }

    public function deleteTask(int $id)
    {
        $db = DbService::getConnection();
        $sql = "delete from task where id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateTaskText(int $id, string $text,string $edited)
    {
        $db = DbService::getConnection();
        $sql = "update task set text = :text, edited = :edited where id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->bindParam('text', $text, PDO::PARAM_STR);
        $stmt->bindParam('edited', $edited, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateTaskStatus(int $id, string $status)
    {
        $db = DbService::getConnection();
        $sql = "update task set status = :status where id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->bindParam('status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }
}
