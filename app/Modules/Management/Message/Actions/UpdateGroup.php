<?php

namespace App\Modules\Management\Message\Actions;

use Illuminate\Support\Facades\Auth;

class UpdateGroup
{
    static $conversationModel = \App\Modules\Management\Message\Models\ConversationModel::class;

    public static function execute($request, $id)
    {
        try {
            $validatedData = $request->validate([
                'group_name' => 'required|string|max:255'
            ]);

            $conversation = self::$conversationModel::find($id);
            
            if (!$conversation) {
                return messageResponse('Group not found', [], 404, 'error');
            }

            // Check if it's a group conversation
            if (!$conversation->is_group) {
                return messageResponse('This is not a group conversation', [], 400, 'error');
            }

            // Check if user is the creator of the group
            if ($conversation->creator !== Auth::id()) {
                return messageResponse('Only the group creator can update the group name', [], 403, 'error');
            }

            // Update group name
            $conversation->group_name = $validatedData['group_name'];
            $conversation->save();

            return messageResponse('Group name updated successfully', [
                'conversation' => $conversation
            ], 200, 'success');

        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'error');
        }
    }
}
