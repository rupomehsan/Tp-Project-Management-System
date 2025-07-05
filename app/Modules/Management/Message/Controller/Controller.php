<?php

namespace App\Modules\Management\Message\Controller;

use App\Modules\Management\Message\Actions\GetAllConversations;
use App\Modules\Management\Message\Actions\GetConversationMessages;
use App\Modules\Management\Message\Actions\StartConversation;
use App\Modules\Management\Message\Actions\SendMessage;
use App\Http\Controllers\Controller as ControllersController;
use Illuminate\Http\Request;

class Controller extends ControllersController
{

    public function GetAllConversations()
    {

        $data = GetAllConversations::execute();
        return $data;
    }
    public function GetConversationMessages($id)
    {

        $data = GetConversationMessages::execute($id);
        return $data;
    }
    public function StartConversation(Request $request)
    {
        $data = StartConversation::execute($request);
        return $data;
    }
    public function SendMessage(Request $request)
    {
        $data = SendMessage::execute($request);
        return $data;
    }
}
