<?php

declare(strict_types=1);

namespace Services;

use Models\Task;
use Repositories\TaskRepository;
use \Exception;

/* The TaskService class provides methods for managing tasks, including saving, retrieving, updating,
and deleting tasks. */
class TaskService {
    
    /* `private TaskRepository ;` is declaring a private property named `` of
    type `TaskRepository` in the `TaskService` class. This property is used to store an instance of
    the `TaskRepository` class, which is responsible for handling database operations related to
    tasks, such as saving, retrieving, updating, and deleting tasks. By declaring the property with
    a specific type (`TaskRepository`), it helps enforce type safety and ensures that only objects
    of type `TaskRepository` can be assigned to this property. */
    private TaskRepository $repository;

    public function __construct() {
        $this->repository = new TaskRepository();
    }

    /**
     * The function `save` creates a new Task object with data provided, saves it using a repository,
     * and returns the saved Task object or throws an exception if there is an error.
     * 
     * @param array data The `save` function takes an array `` as a parameter. This array should
     * contain the following keys:
     * 
     * @return Task The `save` function is returning a `Task` object.
     */
    public function save(array $data): Task
    {
        try {
            $task = new Task([
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => $data['status']
            ]);
            $savedTask = $this->repository->save($task);
            return $savedTask;
        } catch (Exception $e) {
            throw new Exception("Failed to register task: " . $e->getMessage());
        }
    }

    /**
     * This PHP function retrieves all tasks from a repository and returns them as an array of Task
     * objects.
     * 
     * @return array An array of Task objects is being returned. Each Task object contains properties
     * such as id, title, description, and status, which are populated from the data retrieved from the
     * repository.
     */
    public function all(): array
    {
        try {
            $tasksData = $this->repository->all();
            $tasks = [];
            foreach ($tasksData as $taskData) {
                $task = new Task([
                    'id' => $taskData['id'],
                    'title' => $taskData['title'],
                    'description' => $taskData['description'],
                    'status' => $taskData['status']
                ]);
                $tasks[] = $task;
            }
            return $tasks;
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve tasks: " . $e->getMessage());
        }
    }

    /**
     * The function `update` takes an ID and data array, finds the existing task, updates it with the
     * new data, and returns the updated task.
     * 
     * @param int id The `id` parameter in the `update` function represents the unique identifier of
     * the task that you want to update. It is used to locate the specific task in the repository based
     * on its ID.
     * @param array data The `update` function you provided takes an integer `` and an array ``
     * as parameters. The `` array is expected to contain keys for 'title', 'description', and
     * 'status' which are used to update the corresponding fields of a Task entity.
     * 
     * @return Task An instance of the `Task` class with the updated data (id, title, description,
     * status) is being returned after successfully updating the task in the database.
     */
    public function update(int $id, array $data): Task
    {
        try {
            $existingTask = $this->repository->find($id);
            if (!$existingTask) {
                throw new Exception("Task with ID $id not found.");
            }

            $this->repository->update($id, $data);
            
            return new Task([
                'id' => $id,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => $data['status']
            ]);
        } catch (Exception $e) {
            throw new Exception("Failed to update task: " . $e->getMessage());
        }
    }

    /**
     * The function `delete` deletes a task by ID after checking its existence in the repository and
     * handles exceptions accordingly.
     * 
     * @param int id The `delete` function takes an integer parameter `` which represents the ID of
     * the task that needs to be deleted from the repository.
     */
    public function delete(int $id): void
    {
        try {
            $existingTask = $this->repository->find($id);
            if (!$existingTask) {
                throw new Exception("Task with ID $id not found.");
            }

            $this->repository->delete($id);
        } catch (Exception $e) {
            throw new Exception("Failed to delete task: " . $e->getMessage());
        }
    }

    /**
     * This PHP function retrieves a task by its ID from a repository and handles exceptions.
     * 
     * @param int id The `getById` function you provided takes an integer parameter `` which
     * represents the ID of the task you want to retrieve. This function attempts to find a task in the
     * repository based on the provided ID. If the task is found, it returns an array representing the
     * task. If the task
     * 
     * @return ?array The `getById` function is returning an array containing the task with the
     * specified ID if it is found in the repository. If the task is not found, it throws an exception
     * with a message indicating that the task with the given ID was not found.
     */
    public function getById(int $id): ?array
    {
        try {
            $existingTask = $this->repository->find($id);
            if (!$existingTask) {
                throw new Exception("Task with ID $id not found.");
            }
            return $existingTask;
        } catch (Exception $e) {
            throw new Exception("Failed to get by id task: " . $e->getMessage());
        }
    }

    /**
     * The function `updateTaskStatus` updates the status of a task identified by its ID with a new
     * status value.
     * 
     * @param int id The `id` parameter in the `updateTaskStatus` function is an integer that
     * represents the unique identifier of the task that needs to be updated. This ID is used to locate
     * the specific task in the repository and update its status.
     * @param string status The `updateTaskStatus` function takes two parameters: `` of type integer
     * and `` of type string. The function updates the status of a task with the given ID to the
     * new status provided.
     */
    public function updateTaskStatus(int $id, string $status): void
    {
        if (!in_array($status, ['pendente', 'concluída'])) {
            throw new Exception("Status invalid");
        }

        $task = $this->repository->find($id);

        if (!$task) {
            throw new Exception("Task with ID $id not found.");
        }

        $this->repository->update($id, [
            'title' => $task['title'],
            'description' => $task['description'],
            'status' => $status
        ]);
    }
}