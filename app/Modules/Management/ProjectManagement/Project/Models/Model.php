<?php

namespace App\Modules\Management\ProjectManagement\Project\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
class Model extends EloquentModel
{
    use SoftDeletes;
    protected $table = "projects";
    protected $guarded = [];
    protected $casts = [
        'project_document_files' => 'array',
    ];  


    public static $userModel = \App\Modules\Management\UserManagement\User\Models\Model::class;
    public static $projectGroupIdsModel = \App\Modules\Management\ProjectManagement\ProjectGroup\Models\Model::class;
    public static $tasksIdsModel = \App\Modules\Management\TasksManagement\Tasks\Models\Model::class;

    protected static function booted()
    {
        static::created(function ($data) {
            $random_no = random_int(100, 999) . $data->id . random_int(100, 999);
            $slug = $data->title . " " . $random_no;
            $data->slug = Str::slug($slug); //use Illuminate\Support\Str;
            if (strlen($data->slug) > 50) {
                $data->slug = substr($data->slug, strlen($data->slug) - 50, strlen($data->slug));
            }
            if (auth()->check()) {
                $data->creator = auth()->user()->id;
            }
            $data->save();
        });
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    public function scopeInactive($q)
    {
        return $q->where('status', 'inactive');
    }
    public function scopeTrased($q)
    {
        return $q->onlyTrashed();
    }

    public function project_users()
    {
        return $this->belongsToMany(self::$userModel, 'project_users', 'project_id', 'user_id');
    }

    public function projectGroupId()
    {
        return $this->belongsTo(self::$projectGroupIdsModel, 'project_group_id', 'id');
    }
    public function tasks()
    {
        return $this->hasMany(self::$tasksIdsModel,'project_id' );
    }
}