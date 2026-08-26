<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Bonus Kehadiran</title>
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
        .total-row { background-color: #f9f9f9; }
        
        /* Specific Column Widths */
        .col-no { width: 3%; }
        .col-name { width: 14%; text-align: left; }
        .col-unit { width: 7%; }
        .col-total { width: 8%; text-align: right; }
        .col-date { width: auto; font-size: 7px; }
        
        .badge-green { color: #059669; font-weight: bold; }
        .text-muted { color: #94a3b8; }
        .text-red { color: #ef4444; }
    </style>
</head>
<body>

    <h2>Rekap Bonus Kehadiran</h2>
    <div class="subtitle">
        Periode: {{ $periodeStr }} <br>
        Unit: {{ count($reports) > 0 ? ($reports[0]['employee']['unit']['name'] ?? ($reports[0]['employee']['unit_name'] ?? 'Semua Unit')) : 'Semua Unit' }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-name">Pegawai</th>
                
                <th class="col-total">Total Bonus (Rp)</th>
                @foreach($dates as $date)
                    @php
                        $colorClass = $date->dayOfWeek == 0 ? 'text-red' : '';
                        $hari = [0 => 'MIN', 1 => 'SEN', 2 => 'SEL', 3 => 'RAB', 4 => 'KAM', 5 => 'JUM', 6 => 'SAB'];
                        $dayName = $hari[$date->dayOfWeek];
                    @endphp
                    <th class="col-date {{ $colorClass }}">
                        {{ $dayName }}<br>{{ $date->format('d/m') }}
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
                    
                    <td class="text-right font-bold" style="color: #059669;">
                        {{ $report['bonus_nominal'] > 0 ? number_format($report['bonus_nominal'], 0, ',', '.') : '0' }}
                    </td>
                    
                    @foreach($dates as $date)
                        @php
                            $dateStr = $date->format('Y-m-d');
                            $detail = $report['daily_details'][$dateStr] ?? null;
                        @endphp
                        <td>
                            @if($detail && !in_array($detail['status'] ?? '', ['Off', 'Libur']))
                                @if($detail['bonus_nominal'] > 0)
                                    @php 
                                        $nominal = $detail['bonus_nominal'];
                                        $shortNominal = ($nominal >= 1000) ? ($nominal / 1000) . 'k' : $nominal;
                                    @endphp
                                    <span class="badge-green">{{ $shortNominal }}</span>
                                @else
                                    <span style="color: #ef4444; font-weight: bold;">0K</span>
                                @endif
                            @else
                                <span class="text-muted" style="font-weight: bold; font-size: 7px;">OFF</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($dates) + 4 }}" class="text-center">Tidak ada data pegawai pada periode ini.</td>
                </tr>
            @endforelse
            
            @if(count($reports) > 0)
            <tr class="total-row font-bold">
                <td colspan="2" class="text-right">TOTAL KESELURUHAN:</td>
                <td class="text-right" style="color: #059669;">{{ number_format($totalSemuaBonus, 0, ',', '.') }}</td>
                <td colspan="{{ count($dates) }}"></td>
            </tr>
            @endif
        </tbody>
    </table>

    <div style="margin-top: 30px; width: 100%; font-size: 10px;">
        <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none; text-align: left; width: 50%;"></td>
                <td style="border: none; text-align: center; width: 50%;">
                    Mengetahui,<br><br><br><br><br>
                    <strong>(________________________)</strong><br>
                    HRD Manager
                </td>
            </tr>
        </table>
    </div>

</body>
</html>





