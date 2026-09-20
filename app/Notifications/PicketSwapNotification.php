<?php

namespace App\Notifications;

use App\Models\PicketSwap;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PicketSwapNotification extends Notification
{
    use Queueable;

    public PicketSwap $swap;
    public string $type; // 'requested', 'approved_by_target', 'approved', 'rejected'

    /**
     * Create a new notification instance.
     */
    public function __construct(PicketSwap $swap, string $type = 'requested')
    {
        $this->swap = $swap;
        $this->type = $type;
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
        $reqName = $this->swap->requester->name ?? 'Guru Pengaju';
        $targetName = $this->swap->targetEmployee->name ?? 'Guru Target';
        $reqDateStr = $this->swap->requested_date ? $this->swap->requested_date->translatedFormat('d M Y') : '-';
        $targetDateStr = $this->swap->target_date ? $this->swap->target_date->translatedFormat('d M Y') : '-';

        switch ($this->type) {
            case 'requested':
                $title = 'Permohonan Tukar Piket Baru';
                $message = "{$reqName} mengajak Anda bertukar jadwal piket ({$reqDateStr} ↔️ {$targetDateStr}).";
                $url = route('picket-schedules.index');
                break;

            case 'approved_by_target':
                $title = 'Tukar Piket Butuh Verifikasi Waka/Kepsek';
                $message = "{$targetName} menyetujui ajakan tukar piket dari {$reqName}. Menunggu verifikasi akhir Admin/Waka.";
                $url = route('picket-schedules.admin');
                break;

            case 'approved':
                $title = 'Tukar Piket Resmi Disetujui';
                $message = "Permohonan tukar jadwal piket antara {$reqName} ({$reqDateStr}) dan {$targetName} ({$targetDateStr}) telah disetujui resmi oleh Waka/Kepsek.";
                $url = route('picket-schedules.index');
                break;

            case 'rejected':
            default:
                $title = 'Tukar Piket Ditolak / Dibatalkan';
                $message = "Permohonan tukar jadwal piket antara {$reqName} dan {$targetName} telah ditolak atau dibatalkan.";
                $url = route('picket-schedules.index');
                break;
        }

        return [
            'id' => $this->swap->id,
            'title' => $title,
            'category' => 'PicketSwap',
            'type' => $this->type,
            'target_audience' => 'Teacher',
            'message' => $message,
            'url' => $url,
        ];
    }
}
