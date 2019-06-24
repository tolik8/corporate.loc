<?php

namespace Corp\Repositories;

use Corp\Comment;

class CommentsRepository extends Repository
{
    public function __construct(Comment $comments)
    {
        $this->model = $comments;
    }
}
