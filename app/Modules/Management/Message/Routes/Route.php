<?php

use App\Modules\Management\Message\Controller\Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('messages')->middleware('auth:api')->group(function () {
        Route::get('get-all-conversations', [Controller::class, 'GetAllConversations']);
        Route::get('get-conversation-messages/{id}', [Controller::class, 'GetConversationMessages']);
        Route::post('start-conversation', [Controller::class, 'StartConversation']);
        Route::post('send', [Controller::class, 'SendMessage']);
    });
});
