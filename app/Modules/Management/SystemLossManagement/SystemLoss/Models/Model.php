<?php

namespace App\Modules\Management\SystemLossManagement\SystemLoss\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
class Model extends EloquentModel
{
    use SoftDeletes;
    protected $table = "system_losses";
    protected $guarded = [];


    
    public static $projectsModel = \App\Modules\Management\ProjectManagement\Project\Models\Model::class;
    public static $systemLossCategoryModel = \App\Modules\Management\SystemLossManagement\SystemLossCategory\Models\Model::class;
    
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

    
    public function projectId()
    {
        return $this->belongsTo(self::$projectsModel, 'project_id','id');
    }
    public function lossCategoryId()
    {
        return $this->belongsTo(self::$systemLossCategoryModel, 'loss_category_id', 'id');
    }
}