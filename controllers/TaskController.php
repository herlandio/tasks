<?php

declare(strict_types=1);

namespace Controllers;

use Services\TaskService;
use \Exception;

class TaskController
{
    private TaskService $service;

    public function __construct()
    {
        $this->service = new TaskService();
    }

    public function index(): void
    {
        include __DIR__ . '/../views/index.html';
    }

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

    public function updateTask(int $id): void
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
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
}