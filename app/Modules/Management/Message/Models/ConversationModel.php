<?php

namespace App\Modules\Management\Message\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConversationModel extends EloquentModel
{
    use SoftDeletes;
    protected $table = "conversation";
    protected $guarded = [];


    public function participantUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'participant', 'id');
    }


    public function creatorUser()
    {
        return $this->belongsTo(\App\Modules\Management\UserManagement\User\Models\Model::class, 'creator');
    }

    public function messages()
    {
        return $this->hasMany(\App\Modules\Management\Message\Models\Model::class, 'conversation_id');
    }
}
