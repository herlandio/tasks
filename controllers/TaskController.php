<?php

declare(strict_types=1);

namespace Controllers;

use Services\TaskService;
use \Exception;

/* The TaskController class in PHP handles CRUD operations for tasks, including saving, listing,
updating, deleting, and updating task status. */
class TaskController
{
    /* The line `private TaskService ;` in the TaskController class is declaring a private
    property named `` of type `TaskService`. This property is used to store an instance of
    the TaskService class, which is responsible for handling tasks-related operations such as
    saving, listing, updating, and deleting tasks. By declaring the property with the type hint
    `TaskService`, it enforces that only objects of the TaskService class (or its subclasses) can be
    assigned to this property. This helps in maintaining type safety and clarity in the codebase. */
    private TaskService $service;

    public function __construct()
    {
        $this->service = new TaskService();
    }

    /**
     * The index function includes and displays the index.html file located in the views directory.
     */
    public function index(): void
    {
        include __DIR__ . '/../views/index.html';
    }

    /**
     * The function `saveTasks` in PHP saves task data received from an HTTP request, validates
     * required fields, saves the task, and returns a JSON response with the saved task details or an
     * error message.
     */
    public function saveTasks(): void
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            $requiredFields = ['title', 'description', 'status'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    http_response_code(400);
                    throw new Exception("The field '$field' is required.");
                }
            }

            $task = $this->service->save($data);

            http_response_code(201);
            echo json_encode([
                "message" => "Task registered successfully!",
                "task" => [
                    "id" => $task->getId(),
                    "title" => $task->getTitle(),
                    "description" => $task->getDescription(),
                    "status" => $task->getStatus()
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    /**
     * The function `listTasks` retrieves tasks from a service and returns them as JSON with
     * appropriate status codes.
     * 
     * @return void The `listTasks` function returns a JSON response containing a message indicating
     * the success or failure of retrieving tasks, along with an array of task objects. Each task
     * object in the array includes the task's ID, title, description, and status. If the retrieval is
     * successful, a 200 HTTP response code is set, and the JSON response includes the message and
     * tasks array. If an exception occurs during
     */
    public function listTasks(): void
    {
        try {
            $tasks = $this->service->all();
    
            http_response_code(200);
            echo json_encode([
                "message" => "Tasks retrieved successfully!",
                "tasks" => array_map(function ($task) {
                    return [
                        "id" => $task->getId(),
                        "title" => $task->getTitle(),
                        "description" => $task->getDescription(),
                        "status" => $task->getStatus()
                    ];
                }, $tasks)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to retrieve tasks: " . $e->getMessage()]);
        }
    }

    /**
     * The function `updateTask` in PHP updates a task with required fields and handles exceptions.
     * 
     * @param int id The `updateTask` function you provided is responsible for updating a task based on
     * the given ID. It expects an integer ID as a parameter to identify the task that needs to be
     * updated.
     */
    public function updateTask(int $id): void
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            $requiredFields = ['title', 'description', 'status'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    http_response_code(400);
                    throw new Exception("The field '$field' is required.");
                }
            }
            
            $task = $this->service->update($id, $data);
            
            http_response_code(200);
            echo json_encode([
                "message" => "Task updated successfully!",
                "task" => [
                    "id" => $task->getId(),
                    "title" => $task->getTitle(),
                    "description" => $task->getDescription(),
                    "status" => $task->getStatus()
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    /**
     * The function `deleteTask` deletes a task by its ID and returns a success message or an error
     * message in JSON format.
     * 
     * @param int id The `deleteTask` function is a method that takes an integer parameter ``
     * representing the ID of the task to be deleted. Inside the function, it attempts to delete the
     * task using a service, and if successful, it returns a JSON response with a success message and
     * HTTP status code 200
     */
    public function deleteTask(int $id): void
    {
        try {
            $this->service->delete($id);
            http_response_code(200);
            echo json_encode(["message" => "Task deleted successfully!"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    /**
     * This PHP function retrieves a task by its ID and returns a JSON response with the task details
     * or an error message.
     * 
     * @param int id The `getById` function is designed to retrieve a task by its ID. When this
     * function is called, it attempts to fetch the task with the specified ID from the service. If the
     * task is found, it responds with a success message and the task details in JSON format. If an
     * exception occurs
     */
    public function getById(int $id): void
    {
        try {
            $task = $this->service->getById($id);

            http_response_code(200);
            echo json_encode([
                "message" => "Task liste successfully!",
                "task" => $task ?? [],
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    /**
     * This PHP function updates the status of a task based on the input data provided.
     * 
     * @param int id The `updateStatus` function is responsible for updating the status of a task
     * identified by the provided ``. The function expects an integer value for the `` parameter.
     * 
     * @return void If the status field is missing or empty, an error response with HTTP status code
     * 400 and a message indicating that the "status" field is required is being returned.
     */
    public function updateStatus(int $id): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $status = $data['status'];

            if (!$status) {
                http_response_code(400);
                echo json_encode(['error' => 'The field "status" is required.']);
                return;
            }

            $this->service->updateTaskStatus($id, $status);

            http_response_code(200);
            echo json_encode(['message' => 'Status updated with success!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}