<?php

declare(strict_types=1);

namespace Models;

class Task
{
    private $id;
    private $title;
    private $description;
    private $status;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->status = $data['status'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}