<?php

declare(strict_types=1);

namespace Services;

use Models\Task;
use Repositories\TaskRepository;
use \Exception;

class TaskService {
    
    private TaskRepository $repository;

    public function __construct() {
        $this->repository = new TaskRepository();
    }

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
}