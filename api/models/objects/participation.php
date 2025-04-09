<?php

namespace storyteller\objects;

class Participation{
    public int $id;
    public int $userId;
    public int $storyId;
    public string $content;
    public string $createdAt;
    public string $updatedAt;

    function __construct($id, $userId, $storyId, $content, $createdAt) {
        $this->id = $id;
        $this->userId = $userId;
        $this->storyId = $storyId;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }
}