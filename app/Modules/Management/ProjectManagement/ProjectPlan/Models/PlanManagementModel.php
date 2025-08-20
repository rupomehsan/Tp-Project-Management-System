<?php

namespace App\Modules\Management\ProjectManagement\ProjectPlan\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanManagementModel extends EloquentModel
{
    use SoftDeletes;
    protected $table = "project_plans_management";
    protected $guarded = [];
}
