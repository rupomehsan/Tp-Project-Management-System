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
    public static $userModel = \App\Modules\Management\UserManagement\User\Models\Model::class;

    public function user()
    {
        return $this->belongsTo(self::$userModel, 'user_id', 'id');
    }
}
