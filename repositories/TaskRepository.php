<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;
use Models\Task;
use \PDO;
use \PDOException;
use \Exception;

/* The TaskRepository class provides methods for saving, fetching, updating, and deleting tasks in a
database using PDO in PHP. */
class TaskRepository {

    /* The line `private PDO ;` in the TaskRepository class is declaring a private property
    named `` of type `PDO`. This property is used to store the connection to the database
    using PDO (PHP Data Objects), which is a PHP extension for interacting with databases. */
    private PDO $connection;

    public function __construct() {
        $this->connection = Database::getConnection();
    }

    /**
     * The function saves a Task object to a database table and returns the saved Task with its
     * generated ID.
     * 
     * @param Task task The `save` function you provided is responsible for saving a `Task` object
     * into a database table named `tasks`. Here's a breakdown of the function:
     * 
     * @return Task An instance of the Task class is being returned after saving the task data to the
     * database. The returned Task object contains the id, title, description, and status of the task
     * that was just saved.
     */
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

    /**
     * This PHP function retrieves all tasks from a database table and returns them as an associative
     * array.
     * 
     * @return array An array of associative arrays containing all the rows from the "tasks" table
     * fetched from the database. Each associative array represents a row with column names as keys and
     * corresponding values.
     */
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

    /**
     * The function updates a task in a database based on the provided ID and data, handling exceptions
     * for task not found or deletion failure.
     * 
     * @param int id The `id` parameter in the `update` function represents the unique identifier of
     * the task that you want to update in the database. It is used to locate the specific task record
     * that needs to be updated based on this identifier.
     * @param array data The `update` function you provided is used to update a task in a database. It
     * takes two parameters: `` which is the ID of the task to be updated, and `` which is an
     * array containing the new data for the task.
     */
    public function update(int $id, array $data): void
    {
        try {
            $sql = "UPDATE tasks SET title = :title, description = :description, status = :status WHERE id = :id";
            $stmt = $this->connection->prepare($sql);

            $task = $this->find($id);

            if (!$task) {
                throw new Exception("Task with ID $id not found.");
            }

            $stmt->execute([
                'id' => $id,
                'title' => $data['title'] ?? $task['title'],
                'description' => $data['description'] ?? $task['description'],
                'status' => $data['status']
            ]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("No task was deleted. Task with ID $id may not exist.");
            }
        } catch (Exception $e) {
            throw new Exception("Failed to delete task from database: " . $e->getMessage());
        }
    }

    /**
     * This PHP function deletes a task from the database by ID and throws an exception if the task
     * does not exist or if deletion fails.
     * 
     * @param int id The `delete` function you provided is a PHP method that deletes a task from a
     * database table based on the given `id`. The `id` parameter is an integer that represents the
     * unique identifier of the task to be deleted.
     */
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

    /**
     * This PHP function finds a task by its ID in a database and returns the task data as an
     * associative array or an empty array if not found.
     * 
     * @param int id The `find` function you provided is a PHP method that retrieves a task from a
     * database table named `tasks` based on the provided `id`. The function takes an integer parameter
     * `` representing the unique identifier of the task to be retrieved.
     * 
     * @return ?array The `find` function is returning an associative array representing a single row
     * from the `tasks` table where the `id` column matches the provided ``. If a row is found, it
     * returns the row as an associative array using `PDO::FETCH_ASSOC`. If no row is found, it returns
     * an empty array `[]`.
     */
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