<?php

namespace App\Modules\Management\TasksManagement\tasks\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
class TaskReviewModel extends EloquentModel
{
    use SoftDeletes;
    protected $table = "task_comments";
    protected $guarded = [];


  

}