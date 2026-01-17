<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function createForAllStudents(string $type, string $title, string $message, array $data = [])
    {
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
        }
    }

    public static function createForStudent(int $userId, string $type, string $title, string $message, array $data = [])
    {
        Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function markAsRead(int $notificationId, int $userId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
        }
    }

    public static function getUnreadCount(int $userId)
    {
        return Notification::forUser($userId)->unread()->count();
    }
}
