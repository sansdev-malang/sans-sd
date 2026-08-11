<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\LeaveRequest;

class LeaveDecisionNotification extends Notification
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
        $statusLabel = 'Menunggu';
        if ($this->leave->status === 'Approved') {
            $statusLabel = 'Disetujui';
        } elseif ($this->leave->status === 'Rejected') {
            $statusLabel = 'Ditolak';
        }

        $message = "Pengajuan izin {$this->leave->type} Anda telah {$statusLabel} oleh HRD Pusat.";
        if ($this->leave->notes) {
            $message .= " Catatan: " . $this->leave->notes;
        }

        return [
            'id' => $this->leave->id,
            'title' => 'Keputusan Izin HRD',
            'category' => 'LeaveRequest',
            'target_audience' => 'Employee',
            'message' => $message,
            'url' => route('my-leaves.index'),
        ];
    }
}
