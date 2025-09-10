# Task Review API Endpoints

This document outlines the API endpoints needed to support the Task Review Modal functionality with a single reply per task.

## Required Endpoints

### 1. Get Task Reviews
**Endpoint:** `GET /task/task-reviews-by-task-id/{task_id}`
**Description:** Retrieve all reviews for a specific task

**Response Format:**
```json
{
  "status": "success",
  "message": "Reviews retrieved successfully",
  "data": [
    {
      "id": 1,
      "task_id": 123,
      "reviewer_id": 456,
      "reviewer_name": "John Manager",
      "rating": 4,
      "comment": "Good work overall, but needs some improvements in the documentation.",
      "created_at": "2025-01-15T10:00:00Z",
      "updated_at": "2025-01-15T10:00:00Z"
    }
  ]
}
```

### 2. Get Task Reply
**Endpoint:** `GET /task/task-reply-by-task-id/{task_id}`
**Description:** Get the single reply for a task (if exists)

**Response Format:**
```json
{
  "status": "success",
  "message": "Task reply retrieved successfully",
  "data": {
    "id": 1,
    "task_id": 123,
    "reply": "Thank you for all the feedback. I'll address the documentation issues mentioned.",
    "reply_date": "2025-01-15T14:30:00Z",
    "created_at": "2025-01-15T14:30:00Z",
    "updated_at": "2025-01-15T14:30:00Z"
  }
}
```

**Error Response (No reply found):**
```json
{
  "status": "error",
  "message": "No reply found for this task",
  "data": null
}
```

### 3. Store Task Reply
**Endpoint:** `POST /task/store-task-reply`
**Description:** Submit a new reply for a task

**Request Body:**
```json
{
  "task_id": 123,
  "reply": "Thank you for all the feedback. I'll address the issues mentioned in the reviews."
}
```

**Response Format:**
```json
{
  "status": "success",
  "message": "Reply submitted successfully",
  "data": {
    "id": 1,
    "task_id": 123,
    "reply": "Thank you for all the feedback. I'll address the issues mentioned in the reviews.",
    "reply_date": "2025-01-15T14:30:00Z"
  }
}
```

### 4. Update Task Reply
**Endpoint:** `POST /task/update-task-reply/{reply_id}`
**Description:** Update an existing reply for a task

**Request Body:**
```json
{
  "reply": "Updated reply: Thank you for all the feedback. I've now addressed all the issues mentioned."
}
```

**Response Format:**
```json
{
  "status": "success",
  "message": "Reply updated successfully",
  "data": {
    "id": 1,
    "task_id": 123,
    "reply": "Updated reply: Thank you for all the feedback. I've now addressed all the issues mentioned.",
    "reply_date": "2025-01-15T15:30:00Z"
  }
}
```

## Database Schema Suggestions

### Task Reviews Table
```sql
CREATE TABLE task_reviews (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT UNSIGNED NOT NULL,
    reviewer_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED DEFAULT 0,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Task Replies Table (New)
```sql
CREATE TABLE task_replies (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    reply TEXT NOT NULL,
    reply_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_task_reply (task_id) -- Only one reply per task
);
```

## Implementation Notes

1. **Single Reply Concept:** Each task can only have one reply from the assignee, addressing all reviews collectively
2. **Authentication:** Ensure that only task assignees can create/update replies
3. **Permissions:** Validate that the user has permission to view reviews for the task
4. **Validation:** Ensure reply text is not empty and within reasonable length limits
5. **Unique Constraint:** Ensure only one reply per task exists
6. **Edit Functionality:** Allow users to edit their existing reply

## Frontend Integration

The Task Review Modal now includes:

- Loading states while fetching reviews and replies
- Error handling for failed requests
- Single reply section at the bottom of all reviews
- Edit functionality for existing replies
- Responsive design for mobile devices
- Dark mode support

## Example Laravel Controller Methods

```php
// Get task reviews
public function getTaskReviewsByTaskId($taskId)
{
    try {
        $reviews = TaskReview::where('task_id', $taskId)
            ->with('reviewer:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'task_id' => $review->task_id,
                    'reviewer_id' => $review->reviewer_id,
                    'reviewer_name' => $review->reviewer->name ?? 'Anonymous',
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Reviews retrieved successfully',
            'data' => $reviews
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to retrieve reviews',
            'data' => []
        ], 500);
    }
}

// Get task reply
public function getTaskReplyByTaskId($taskId)
{
    try {
        $reply = TaskReply::where('task_id', $taskId)->first();
        
        if ($reply) {
            return response()->json([
                'status' => 'success',
                'message' => 'Task reply retrieved successfully',
                'data' => [
                    'id' => $reply->id,
                    'task_id' => $reply->task_id,
                    'reply' => $reply->reply,
                    'reply_date' => $reply->reply_date,
                    'created_at' => $reply->created_at,
                    'updated_at' => $reply->updated_at,
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No reply found for this task',
                'data' => null
            ], 404);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to retrieve task reply',
            'data' => null
        ], 500);
    }
}

// Store task reply
public function storeTaskReply(Request $request)
{
    $request->validate([
        'task_id' => 'required|exists:tasks,id',
        'reply' => 'required|string|max:2000'
    ]);

    try {
        // Check if user has permission to reply (task assignee)
        $task = Task::findOrFail($request->task_id);
        if ($task->assigned_to !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to reply to this task'
            ], 403);
        }

        // Check if reply already exists
        $existingReply = TaskReply::where('task_id', $request->task_id)->first();
        if ($existingReply) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reply already exists for this task. Use update endpoint to modify.'
            ], 400);
        }

        $reply = TaskReply::create([
            'task_id' => $request->task_id,
            'user_id' => auth()->id(),
            'reply' => $request->reply,
            'reply_date' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Reply submitted successfully',
            'data' => [
                'id' => $reply->id,
                'task_id' => $reply->task_id,
                'reply' => $reply->reply,
                'reply_date' => $reply->reply_date
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to submit reply',
            'data' => null
        ], 500);
    }
}

// Update task reply
public function updateTaskReply(Request $request, $replyId)
{
    $request->validate([
        'reply' => 'required|string|max:2000'
    ]);

    try {
        $reply = TaskReply::findOrFail($replyId);
        
        // Check if user has permission to update (reply owner)
        if ($reply->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to update this reply'
            ], 403);
        }

        $reply->update([
            'reply' => $request->reply,
            'reply_date' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Reply updated successfully',
            'data' => [
                'id' => $reply->id,
                'task_id' => $reply->task_id,
                'reply' => $reply->reply,
                'reply_date' => $reply->reply_date
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update reply',
            'data' => null
        ], 500);
    }
}
```
