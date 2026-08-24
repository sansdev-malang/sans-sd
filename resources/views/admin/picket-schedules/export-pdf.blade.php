<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jadwal Piket Guru SD</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333333;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 5px 0;
        }
        .subtitle {
            font-size: 10px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0 0 10px 0;
        }
        .badge {
            display: inline-block;
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
        }
        td {
            padding: 8px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .area-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 10px;
            margin-bottom: 4px;
        }
        .duty-hours {
            font-size: 8px;
            color: #64748b;
        }
        .job-list {
            margin: 0;
            padding: 0 0 0 12px;
        }
        .job-item {
            margin-bottom: 4px;
            color: #475569;
        }
        .teacher-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px;
            margin-bottom: 5px;
        }
        .teacher-name {
            font-weight: bold;
            color: #1e293b;
            font-size: 9px;
            word-wrap: break-word;
        }
        .teacher-role {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
            word-wrap: break-word;
        }
        .footer {
            margin-top: 15px;
            width: 100%;
        }
        .notes-box {
            width: 60%;
            float: left;
            font-size: 9px;
            color: #475569;
        }
        .notes-title {
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .signature-box {
            width: 35%;
            float: right;
            text-align: right;
            font-size: 10px;
        }
        .signature-title {
            margin-bottom: 50px;
            font-weight: bold;
            color: #334155;
        }
        .signature-name {
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <h1 class="title">THE DUTY SCHEDULE FOR TEACHERS</h1>
        <p class="subtitle">APPLICABLE FOR AUGUST - DECEMBER 2026</p>
        <span class="badge">⏰ DUTY HOURS: 06.30 - 07.00</span>
    </div>

    <!-- MATRIX TABLE -->
    <table>
        <thead>
            <tr>
                <th style="width: 18%; text-align: left;">Area</th>
                <th style="width: 32%; text-align: left;">Jobs (Tupoksi)</th>
                @foreach($days as $idx => $day)
                    <th style="width: 8.3%; text-align: center;">{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($areas as $area)
            <tr>
                <!-- Area -->
                <td>
                    <div class="area-name">{{ $area->name }}</div>
                    <div class="duty-hours">Jam: {{ $area->duty_hours }}</div>
                </td>

                <!-- Jobs -->
                <td>
                    @if($area->jobs)
                        <ul class="job-list">
                            @foreach(explode("\n", $area->jobs) as $job)
                                @if(trim($job))
                                    <li class="job-item">{{ trim($job) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <span style="font-style: italic; color: #94a3b8;">Tidak ada tupoksi khusus.</span>
                    @endif
                </td>

                <!-- Days -->
                @foreach($days as $idx => $day)
                    <td style="text-align: center;">
                        @php
                            $daySchedules = $area->schedules->where('day_of_week', $idx);
                        @endphp
                        @forelse($daySchedules as $schedule)
                            <div class="teacher-card">
                                <div class="teacher-name">{{ $schedule->employee->name }}</div>
                                <div class="teacher-role">{{ $schedule->employee->position ?? $schedule->employee->employeeType->name ?? '-' }}</div>
                            </div>
                        @empty
                            <span style="color: #94a3b8; font-size: 8px;">-</span>
                        @endforelse
                    </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- FOOTER SIGNATURES & NOTES -->
    <div class="footer">
        <div class="notes-box">
            <div class="notes-title">NOTE:</div>
            <ol style="margin: 0; padding-left: 12px; line-height: 1.5;">
                <li>A duty is a compulsory (Piket bersifat wajib bagi seluruh guru).</li>
                <li>It is possible to change your day as far as you get agreement with another teacher and inform it to the headmaster or vice headmaster (Pergantian hari piket dimungkinkan dengan bertukar hari dengan guru lain dan dilaporkan ke Kepala Sekolah/Waka).</li>
            </ol>
        </div>
        <div class="signature-box">
            <div class="signature-title">Mengetahui,</div>
            <div>Kepala Sekolah</div>
            <div style="margin-top: 40px; font-weight: bold;">( ___________________________ )</div>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>
