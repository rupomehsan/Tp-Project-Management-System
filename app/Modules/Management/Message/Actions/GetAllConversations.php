<?php

namespace App\Modules\Management\Message\Actions;

class GetAllConversations
{
    static $model = \App\Modules\Management\Message\Models\ConversationModel::class;

    public static function execute()
    {
        try {
            $data = self::$model::with(['creatorUser', 'participantUser'])
                ->where(function ($q) {
                    $q->where('creator', auth()->id())
                        ->orWhere('participant', auth()->id());
                })
                ->get()
                ->map(function ($conversation) {
                    // determine who the "other user" is
                    $conversation->participant = auth()->id() == $conversation->creator
                        ? $conversation->participantUser
                        : $conversation->creatorUser;

                    unset($conversation->creatorUser);
                    unset($conversation->participantUser);

                    return $conversation;
                });

            // ✅ Return the final data as response
            return entityResponse($data);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
