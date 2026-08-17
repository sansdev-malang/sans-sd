<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Riwayat Absensi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px; /* Very small to fit all dates */
            color: #333;
        }
        @page {
            margin: 1cm;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .subtitle {
            text-align: center;
            margin-bottom: 15px;
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed; /* Force fixed table layout */
        }
        th, td {
            border: 0.5px solid #aaa;
            padding: 3px 2px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
            font-size: 7px;
        }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* Specific Column Widths */
        .col-no { width: 3%; }
        .col-name { width: 14%; text-align: left; }
        .col-unit { width: 7%; }
        .col-date { width: auto; font-size: 7px; }
        
        .badge-green { color: #059669; font-weight: bold; }
        .badge-blue { color: #3b82f6; font-weight: bold; }
        .text-muted { color: #94a3b8; }
        .text-red { color: #ef4444; font-weight: bold; }
        .cell-content { font-size: 6.5px; line-height: 1.1; }
    </style>
</head>
<body>

    <h2>Laporan Data Riwayat Absensi</h2>
    <div class="subtitle">
        Periode: {{ $periodeStr }} <br>
        Unit: {{ strtoupper($schoolUnit ?? 'Unit') }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-name">Pegawai</th>
                <th class="col-unit">Unit</th>
                @foreach($dates as $date)
                    @php
                        $isSunday = $date->isSunday();
                        $colorClass = $isSunday ? 'text-red' : '';
                    @endphp
                    <th class="col-date {{ $colorClass }}">
                        {{ $date->translatedFormat('D') }}<br>
                        {{ $date->format('d/m') }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $report)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">
                        <div class="font-bold">{{ $report['employee']['name'] }}</div>
                        <div style="font-size: 6px; color: #666;">{{ $report['employee']['nuptk'] ?? '-' }}</div>
                    </td>
                    <td>{{ $report['employee']['unit']['name'] ?? ($report['employee']['unit_name'] ?? '-') }}</td>
                    
                    @foreach($dates as $date)
                        @php
                            $dateStr = $date->format('Y-m-d');
                            $detail = $report['daily_details'][$dateStr] ?? null;
                        @endphp
                        <td>
                            @if($detail)
                                @if($detail['status'] === 'Hadir')
                                    <div class="cell-content badge-green">{{ $detail['check_in'] ?? '-' }}</div>
                                    @if(!empty($detail['pending_leave']))
                                        @php
                                            $pCode = $detail['pending_leave']['leave_code'];
                                            $pdfStyleMap = [
                                                'S' => 'color: #d97706; opacity: 0.6; border: 0.5px dashed #f59e0b;',
                                                'I' => 'color: #7c3aed; opacity: 0.6; border: 0.5px dashed #8b5cf6;',
                                                'C' => 'color: #2563eb; opacity: 0.6; border: 0.5px dashed #3b82f6;',
                                                'H' => 'color: #059669; opacity: 0.6; border: 0.5px dashed #10b981;',
                                            ];
                                            $pStyle = $pdfStyleMap[$pCode] ?? 'color: #94a3b8; border: 0.5px dashed #94a3b8;';
                                        @endphp
                                        <div style="font-size: 5.5px; line-height: 1; margin: 1px 0;"><span style="{{ $pStyle }} padding: 0 1px;">{{ $pCode }}</span></div>
                                    @endif
                                    <div class="cell-content text-muted">{{ $detail['check_out'] ?? '-' }}</div>
                                @elseif($detail['status'] === 'Alfa')
                                    <span class="text-red">A</span>
                                    @if(!empty($detail['pending_leave']))
                                        @php
                                            $pCode = $detail['pending_leave']['leave_code'];
                                            $pdfStyleMap = [
                                                'S' => 'color: #d97706; opacity: 0.6; border: 0.5px dashed #f59e0b;',
                                                'I' => 'color: #7c3aed; opacity: 0.6; border: 0.5px dashed #8b5cf6;',
                                                'C' => 'color: #2563eb; opacity: 0.6; border: 0.5px dashed #3b82f6;',
                                                'H' => 'color: #059669; opacity: 0.6; border: 0.5px dashed #10b981;',
                                            ];
                                            $pStyle = $pdfStyleMap[$pCode] ?? 'color: #94a3b8; border: 0.5px dashed #94a3b8;';
                                        @endphp
                                        <div style="font-size: 5.5px; line-height: 1; margin: 1px 0;"><span style="{{ $pStyle }} padding: 0 1px;">{{ $pCode }}</span></div>
                                    @endif
                                @elseif($detail['status'] === 'Cuti/Izin')
                                    @php
                                        $leaveCode = $detail['leave_code'] ?? 'I';
                                        $isPending = !empty($detail['is_pending']);
                                        $pdfColorMap = [
                                            'S' => $isPending ? 'color: #d97706; opacity: 0.6; border: 0.5px dashed #f59e0b; padding: 0px 1px;' : 'color: #d97706; font-weight: bold;',
                                            'I' => $isPending ? 'color: #7c3aed; opacity: 0.6; border: 0.5px dashed #8b5cf6; padding: 0px 1px;' : 'color: #7c3aed; font-weight: bold;',
                                            'C' => $isPending ? 'color: #2563eb; opacity: 0.6; border: 0.5px dashed #3b82f6; padding: 0px 1px;' : 'color: #2563eb; font-weight: bold;',
                                            'H' => $isPending ? 'color: #059669; opacity: 0.6; border: 0.5px dashed #10b981; padding: 0px 1px;' : 'color: #059669; font-weight: bold;',
                                        ];
                                        $pdfStyle = $pdfColorMap[$leaveCode] ?? 'color: #3b82f6;';
                                    @endphp
                                    
                                    @if(!empty($detail['check_in']) || !empty($detail['check_out']))
                                        <div class="cell-content">
                                            @if(!empty($detail['check_in']))
                                                <div class="badge-green" style="font-size: 6.5px;">{{ $detail['check_in'] }}</div>
                                            @else
                                                <div class="text-muted">-</div>
                                            @endif
                                            
                                            <span style="{{ $pdfStyle }}; font-size: 6.5px;">{{ $leaveCode }}</span>
                                            
                                            @if(!empty($detail['check_out']))
                                                <div class="text-muted" style="font-size: 6.5px;">{{ $detail['check_out'] }}</div>
                                            @else
                                                <div class="text-muted">-</div>
                                            @endif
                                        </div>
                                    @else
                                        <span style="{{ $pdfStyle }}; font-size: 7.5px;">{{ $leaveCode }}</span>
                                        @if($isPending)
                                            <div class="text-muted" style="font-size: 6px; line-height: 0.8;">-</div>
                                        @endif
                                    @endif
                                @elseif($detail['status'] === 'Libur')
                                    <span class="text-red" style="font-size: 6px; font-weight: bold;">OFF</span>
                                @elseif($detail['status'] === 'Off')
                                    <span class="text-muted" style="font-size: 6px;">OFF</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            @else
                                @if($date->isSunday())
                                    <span class="text-red">-</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($dates) + 3 }}" class="text-center">Tidak ada data pegawai pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; width: 100%; font-size: 10px;">
        <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none; text-align: left; width: 50%;"></td>
                <td style="border: none; text-align: center; width: 50%;">
                    Mengetahui,<br><br><br><br><br>
                    <strong>(________________________)</strong><br>
                    Kepala Sekolah
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
