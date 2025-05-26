<?php
namespace App\Modules\Management\TaskManagement\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Model extends EloquentModel
{

    use HasFactory, Notifiable;

    use SoftDeletes;

    protected $table   = "tasks";
    protected $guarded = [];

    public static $userModel = \App\Modules\Management\UserManagement\User\Models\Model::class;

    protected static function booted()
    {
        static::created(function ($data) {
            $random_no  = random_int(100, 999) . $data->id . random_int(100, 999);
            $slug       = $data->title . " " . $random_no;
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

    public function user()
    {
        return $this->belongsTo(self::$userModel, 'assigned_to', 'id');
    }
}
