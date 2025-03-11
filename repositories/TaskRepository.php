<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;
use Models\Task;
use \PDO;
use \PDOException;
use \Exception;

class TaskRepository {

    private PDO $connection;

    public function __construct() {
        $this->connection = Database::getConnection();
    }

    public function save(Task $task): Task
    {
        try {
            $sql = "INSERT INTO tasks (title, description, status) VALUES (:title, :description, :status)";
            $stmt = $this->connection->prepare($sql);

            $stmt->execute([
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'status' => $task->getStatus()
            ]);

            $taskId = $this->connection->lastInsertId();

            $sql = "SELECT * FROM tasks WHERE id = :id";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(['id' => $taskId]);
            $taskData = $stmt->fetch(PDO::FETCH_ASSOC);

            return new Task([
                'id' => $taskData['id'],
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'status' => $taskData['status']
            ]);
        } catch (Exception $e) {
            throw new Exception("Failed to save task: " . $e->getMessage());
        }
    }

    public function all(): array
    {
        try {
            $sql = "SELECT * FROM tasks";
            $stmt = $this->connection->query($sql);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Failed to fetch tasks from database: " . $e->getMessage());
        }
    }

    public function update(int $id, array $data): void
    {
        try {
            $sql = "UPDATE tasks SET title = :title, description = :description, status = :status WHERE id = :id";
            $stmt = $this->connection->prepare($sql);

            $task = $this->find($id);
            
            $stmt->execute([
                'id' => $id,
                'title' => $data['title'] ?? $task['title'],
                'description' => $data['description'] ?? $task['description'],
                'status' => $data['status']
            ]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("No task was updated. Task with ID $id may not exist.");
            }
        } catch (Exception $e) {
            throw new Exception("Failed to update task in database: " . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        try {
            $sql = "DELETE FROM tasks WHERE id = :id";
            $stmt = $this->connection->prepare($sql);

            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("No task was deleted. Task with ID $id may not exist.");
            }
        } catch (Exception $e) {
            throw new Exception("Failed to delete task from database: " . $e->getMessage());
        }
    }

    public function find(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM tasks WHERE id = :id";
            $stmt = $this->connection->prepare($sql);

            $stmt->execute(['id' => $id]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [] ;
        } catch (Exception $e) {
            throw new Exception("Failed to find task: " . $e->getMessage());
        }
    }
}