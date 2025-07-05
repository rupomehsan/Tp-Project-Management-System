<?php

namespace App\Modules\Management\Message\Actions;

class SendMessage
{
    static $messageModel = \App\Modules\Management\Message\Models\Model::class;
    static $conversationModel = \App\Modules\Management\Message\Models\ConversationModel::class;

    public static function execute($request)
    {
        try {

            $conversationId = $request['conversation_id'] ?? $request->conversation_id ?? null;
            $text = $request['text'] ?? $request->text ?? null;

            if (!$conversationId || !$text) {
                return messageResponse('Missing conversation_id or text', [], 422, 'validation_error');
            }

            $conversation = self::$conversationModel::findOrFail($conversationId);

            $authId = auth()->id();
            if (!in_array($authId, [$conversation->creator, $conversation->participant])) {
                return messageResponse('You are not part of this conversation', [], 403);
            }

            $receiverId = $conversation->creator === $authId
                ? $conversation->participant
                : $conversation->creator;

            $message = self::$messageModel::create([
                'conversation_id' => $conversationId,
                'sender' => $authId,
                'receiver' => $receiverId,
                'text' => $text,
                'date_time' => now(),
            ]);

            $conversation->update(['last_updated' => now()]);

            return entityResponse($message);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500);
        }
    }
}
