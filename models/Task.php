<?php

declare(strict_types=1);

namespace Models;

/* The Task class in PHP represents a task entity with properties like id, title, description, and
status, along with getter and setter methods. */
class Task
{
    private int $id;
    private string $title;
    private string $description;
    private string $status;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->status = $data['status'];
    }

    /**
     * This PHP function returns the ID of an object, which is an integer or null.
     * 
     * @return ?int the value of the `` property of the object. The return type is nullable integer
     * (`?int`), which means it can return an integer value or `null`.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * This PHP function getTitle() returns the title property of the object as a string.
     * 
     * @return string The `getTitle()` function is returning the value of the `title` property of the
     * object.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * This PHP function returns the description of an object as a string.
     * 
     * @return string The `getDescription()` method is returning the description property of the
     * object.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * The getStatus function in PHP returns the status of the object as a string.
     * 
     * @return string The `getStatus` function is returning the value of the `status` property of the
     * object, which is a string.
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * The function setId sets the id property of an object to the provided integer value.
     * 
     * @param int id The `setId` function takes an integer parameter `` and sets the value of the
     * object's `id` property to the provided integer value.
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}