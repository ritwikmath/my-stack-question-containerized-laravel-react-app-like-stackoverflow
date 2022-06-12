<?php

namespace Tests\Helper;

use App\Models\Question as ModelsQuestion;
use App\Models\User;

class Question {
    public function __construct()
    {
        $this->model = ModelsQuestion;
        $this->dependsOn = User;
    }

    public function create()
    {
        return $this;
    }
}
