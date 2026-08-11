<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\LeaveRequest;

class NewLeaveRequestNotification extends Notification
{
    use Queueable;

    public $leave;

    /**
     * Create a new notification instance.
     */
    public function __construct(LeaveRequest $leave)
    {
        $this->leave = $leave;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $employeeName = $this->leave->employee->name ?? 'Pegawai';
        return [
            'id' => $this->leave->id,
            'title' => 'Pengajuan Izin Baru',
            'category' => 'LeaveRequest',
            'target_audience' => 'Admin',
            'message' => "Pegawai {$employeeName} mengajukan izin {$this->leave->type}.",
            'url' => route('leave-approvals.index'),
        ];
    }
}
