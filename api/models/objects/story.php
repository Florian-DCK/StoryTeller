<?php

namespace storyteller\objects;

class Story{
    public int $id;
    public string $title;
    public string $createdAt;
    public int $authorId;
    public int $likes;

    function __construct($id, $title, $createdAt, $authorId, $likes = 0) {
        $this->likes = $likes;
        $this->id = $id;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->userId = $authorId;
    }
}