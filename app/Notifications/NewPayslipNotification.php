<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPayslipNotification extends Notification
{
    use Queueable;

    public $period;
    public $fileUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($period, $fileUrl)
    {
        $this->period = $period;
        $this->fileUrl = $fileUrl;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        // Format period YYYY-MM to Indonesian, e.g. "Agustus 2026"
        $date = \Carbon\Carbon::createFromFormat('Y-m', $this->period);
        $monthName = $date->translatedFormat('F Y');

        return [
            'period' => $this->period,
            'message' => 'Slip gaji Anda untuk periode ' . $monthName . ' telah tersedia.',
            'url' => route('payslips.index') . '?month=' . $this->period,
        ];
    }
}
