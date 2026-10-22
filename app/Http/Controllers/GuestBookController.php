<?php

namespace App\Http\Controllers;

use App\Models\GuestBook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class GuestBookController extends Controller
{
    /**
     * Display the Admin Guest Book Dashboard.
     */
    public function index(Request $request)
    {
        $selectedMonth = (int) $request->get('month', Carbon::now()->month);
        $selectedYear = (int) $request->get('year', 2026);
        if ($selectedYear < 2026 || $selectedYear > 2030) {
            $selectedYear = 2026;
        }
        $selectedCategory = $request->get('category', 'all');
        $search = $request->get('search');

        // Query
        $query = GuestBook::whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth);

        if ($selectedCategory && $selectedCategory !== 'all') {
            if (in_array($selectedCategory, ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes'])) {
                $query->whereIn('category', ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes']);
            } else {
                $query->where('category', $selectedCategory);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('host_recipient', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $perPageCount = ($perPage === 'all' || $perPage == 99999 || $perPage === '99999') ? 99999 : (int) $perPage;
        if ($perPageCount <= 0) {
            $perPageCount = 15;
        }

        $guests = $query->orderBy('date', 'desc')->orderBy('time', 'desc')->paginate($perPageCount)->withQueryString();

        // Monthly Stats (all categories in selected month/year)
        $monthQuery = GuestBook::whereYear('date', $selectedYear)->whereMonth('date', $selectedMonth);
        $allMonthGuests = (clone $monthQuery)->get();

        $stats = [
            'total' => $allMonthGuests->count(),
            'spmb' => $allMonthGuests->where('category', 'SPMB')->count(),
            'dinas' => $allMonthGuests->where('category', 'Dinas')->count(),
            'puskesmas' => $allMonthGuests->filter(fn($g) => in_array($g->category, ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes']))->count(),
            'umum' => $allMonthGuests->where('category', 'Umum')->count(),
        ];

        // Month Names
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        // Rentang Tahun: 2026 sampai 2030
        $yearsList = range(2026, 2030);

        $categories = ['Umum', 'Dinas', 'Puskesmas/Dinkes', 'SPMB'];

        return view('admin.guest-books.index', compact(
            'guests',
            'selectedMonth',
            'selectedYear',
            'selectedCategory',
            'search',
            'stats',
            'monthNames',
            'yearsList',
            'categories',
            'perPage'
        ));
    }

    /**
     * Store a new Guest Book entry (Admin).
     */
    public function store(Request $request)
    {
        $validated = $this->validateGuestBook($request);

        try {
            $validated['recorded_by'] = auth()->id();
            GuestBook::create($validated);

            $targetCategory = $request->get('current_category', $validated['category']);
            if (empty($targetCategory)) {
                $targetCategory = $validated['category'];
            }

            return redirect()->route('guest-books.index', [
                'month' => Carbon::parse($validated['date'])->month,
                'year' => Carbon::parse($validated['date'])->year,
                'category' => $targetCategory,
                'per_page' => $request->get('per_page', 15),
            ])->with('success', "Buku tamu atas nama {$validated['name']} berhasil dicatat.");
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Terjadi kesalahan sistem saat menyimpan data buku tamu: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing Guest Book entry.
     */
    public function update(Request $request, $id)
    {
        $guest = GuestBook::findOrFail($id);
        $validated = $this->validateGuestBook($request);

        try {
            $guest->update($validated);

            $targetCategory = $request->get('current_category', $validated['category']);
            if (empty($targetCategory)) {
                $targetCategory = $validated['category'];
            }

            return redirect()->route('guest-books.index', [
                'month' => Carbon::parse($validated['date'])->month,
                'year' => Carbon::parse($validated['date'])->year,
                'category' => $targetCategory,
                'per_page' => $request->get('per_page', 15),
            ])->with('success', "Data buku tamu {$guest->name} berhasil diperbarui.");
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Terjadi kesalahan sistem saat memperbarui data buku tamu: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a Guest Book entry.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $guest = GuestBook::findOrFail($id);
            $month = Carbon::parse($guest->date)->month;
            $year = Carbon::parse($guest->date)->year;
            $cat = $request->get('current_category', $request->get('category', $guest->category));

            $guest->delete();

            return redirect()->route('guest-books.index', [
                'month' => $month,
                'year' => $year,
                'category' => $cat,
                'per_page' => $request->get('per_page', 15),
            ])->with('success', 'Catatan buku tamu berhasil dihapus.');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->with('error', 'Gagal menghapus data buku tamu: ' . $e->getMessage());
        }
    }

    /**
     * Public Guest Book Kiosk Form (accessible at /buku-tamu).
     */
    public function publicForm()
    {
        $categories = ['Umum', 'Dinas', 'Puskesmas/Dinkes', 'SPMB'];
        return view('guest-book-public', compact('categories'));
    }

    /**
     * Public Guest Book Submit.
     */
    public function publicSubmit(Request $request)
    {
        $validated = $this->validateGuestBook($request);

        try {
            GuestBook::create($validated);

            return redirect()->route('buku-tamu.public')
                ->with('success_public', "Terima kasih Bapak/Ibu {$validated['name']}! Kunjungan Anda telah berhasil dicatat di buku tamu kami.");
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Terjadi kendala saat mencatat kunjungan Anda. Silakan periksa kembali dan kirim ulang.']);
        }
    }

    /**
     * Export Guest Book to Excel.
     */
    public function exportExcel(Request $request)
    {
        $selectedMonth = (int) $request->get('month', Carbon::now()->month);
        $selectedYear = (int) $request->get('year', 2026);
        if ($selectedYear < 2026 || $selectedYear > 2030) {
            $selectedYear = 2026;
        }
        $selectedCategory = $request->get('category', 'all');

        $query = GuestBook::whereYear('date', $selectedYear)->whereMonth('date', $selectedMonth);
        if ($selectedCategory && $selectedCategory !== 'all') {
            if (in_array($selectedCategory, ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes'])) {
                $query->whereIn('category', ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes']);
            } else {
                $query->where('category', $selectedCategory);
            }
        }
        $guests = $query->orderBy('date', 'asc')->orderBy('time', 'asc')->get();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $monthName = $monthNames[$selectedMonth] ?? $selectedMonth;

        $principal = \App\Models\Employee::where(function($q) {
            $q->where('position', 'like', '%Kepala Sekolah%')
              ->orWhere('position', 'like', '%Kepsek%');
        })->first();
        if (!$principal) {
            $ksUser = \App\Models\User::where('role', 'kepala_sekolah')->with('employee')->first();
            $principal = $ksUser?->employee;
        }
        $headmasterName = $principal?->name ?? setting('headmaster_name', setting('school_principal_name', 'Andreas Setiyono, S.Pd.Gr., M.Kom.'));
        $headmasterNip = $principal?->niy ?? $principal?->nuptk ?? $principal?->nik ?? setting('headmaster_nip', setting('school_principal_nip', '-'));
        $schoolCity = setting('school_city', 'Malang');
        $appName = setting('unit_name', setting('app_name', 'SD Anak Saleh'));
        $appAddress = setting('app_address', 'Jl. Danau Ranau, Sawojajar, Kota Malang, Jawa Timur 65139');
        $appPhone = setting('app_phone', '+62 812-3456-7890');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Buku Tamu ' . substr($monthName, 0, 3));

        // Header Title
        $sheet->setCellValue('A1', 'BUKU REGISTER KUNJUNGAN TAMU & KEDINASAN');
        $sheet->setCellValue('A2', strtoupper($appName));
        $sheet->setCellValue('A3', "{$appAddress} | Telp: {$appPhone}");
        $catLabel = $selectedCategory !== 'all' ? "Kategori: {$selectedCategory} | " : "";
        $sheet->setCellValue('A4', "{$catLabel}Periode: {$monthName} {$selectedYear} | Dicetak: " . date('d/m/Y H:i') . ' WIB');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FF0F766E');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A3')->getFont()->setSize(9)->getColor()->setARGB('FF475569');
        $sheet->getStyle('A4')->getFont()->setItalic(true)->setSize(9)->getColor()->setARGB('FF64748B');

        // Table Headers
        $headers = [
            'No', 'Tanggal', 'Waktu', 'Nama Tamu / Ortu', 'No. HP', 'Kategori',
            'Institusi / Lembaga', 'Jabatan', 'Pekerjaan', 'Kota / Kab',
            'Provinsi', 'Alamat Lengkap', 'Keperluan / Kegiatan',
            'Nama Calon Murid (SPMB)', 'Tgl Lahir Murid', 'Riwayat Terapi', 'Diagnosa Terapi',
            'Penerima Tamu', 'Kesan & Pesan'
        ];
        $startRow = 6;

        foreach ($headers as $colIndex => $h) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . $startRow, $h);
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headers));

        // Merge Title rows across table width to prevent bloating Column A
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->mergeCells("A4:{$lastCol}4");
        $sheet->getStyle("A1:A4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $headerRange = "A{$startRow}:{$lastCol}{$startRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F766E'); // Teal 700
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($startRow)->setRowHeight(24);

        $currentRow = $startRow + 1;
        $no = 1;

        foreach ($guests as $g) {
            $sheet->setCellValue("A{$currentRow}", $no++);
            $sheet->setCellValue("B{$currentRow}", Carbon::parse($g->date)->format('d/m/Y'));
            $sheet->setCellValue("C{$currentRow}", $g->formatted_time);
            $sheet->setCellValue("D{$currentRow}", $g->name);
            $sheet->setCellValueExplicit("E{$currentRow}", $g->phone ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("F{$currentRow}", $g->category);
            $sheet->setCellValue("G{$currentRow}", $g->institution ?: '-');
            $sheet->setCellValue("H{$currentRow}", $g->position ?: '-');
            $sheet->setCellValue("I{$currentRow}", $g->occupation ?: '-');
            $sheet->setCellValue("J{$currentRow}", $g->city ?: '-');
            $sheet->setCellValue("K{$currentRow}", $g->province ?: '-');
            $sheet->setCellValue("L{$currentRow}", $g->address ?: '-');
            $sheet->setCellValue("M{$currentRow}", $g->purpose);
            $sheet->setCellValue("N{$currentRow}", $g->student_name ?: '-');
            $sheet->setCellValue("O{$currentRow}", $g->student_birth_date ? Carbon::parse($g->student_birth_date)->format('d/m/Y') : '-');
            $sheet->setCellValue("P{$currentRow}", $g->therapy_history ?: '-');
            $sheet->setCellValue("Q{$currentRow}", $g->therapy_diagnosis ?: '-');
            $sheet->setCellValue("R{$currentRow}", $g->host_recipient ?: '-');
            $sheet->setCellValue("S{$currentRow}", $g->feedback ?: '-');

            $sheet->getStyle("A{$currentRow}:C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$currentRow}:F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("O{$currentRow}:P{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($no % 2 === 0) {
                $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $currentRow++;
        }

        // Borders
        if ($currentRow > $startRow + 1) {
            $sheet->getStyle("A{$startRow}:{$lastCol}" . ($currentRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFCBD5E1'));
        }

        // AutoSize columns
        foreach (range(1, count($headers)) as $colIndex) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex))->setAutoSize(true);
        }

        // Fix column A ('No') to compact neat width
        $sheet->getColumnDimension('A')->setAutoSize(false);
        $sheet->getColumnDimension('A')->setWidth(6);

        // Dynamic Signatures in Excel (merged across multiple columns to avoid column stretching)
        $signRow = $currentRow + 2;
        $sheet->mergeCells("B{$signRow}:E{$signRow}");
        $sheet->setCellValue("B{$signRow}", "Petugas Penerima Tamu / Piket,");
        $sheet->getStyle("B{$signRow}")->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("N{$signRow}:S{$signRow}");
        $sheet->setCellValue("N{$signRow}", "{$schoolCity}, " . Carbon::now()->translatedFormat('d F Y') . "\nMengetahui, Kepala Sekolah");
        $sheet->getStyle("N{$signRow}")->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("B" . ($signRow + 4) . ":E" . ($signRow + 4));
        $sheet->setCellValue("B" . ($signRow + 4), "( ................................................ )");
        $sheet->getStyle("B" . ($signRow + 4))->getFont()->setBold(true);
        $sheet->getStyle("B" . ($signRow + 4))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("B" . ($signRow + 5) . ":E" . ($signRow + 5));
        $sheet->setCellValue("B" . ($signRow + 5), "NIP/NIY: ................................................");
        $sheet->getStyle("B" . ($signRow + 5))->getFont()->setSize(9);
        $sheet->getStyle("B" . ($signRow + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("N" . ($signRow + 4) . ":S" . ($signRow + 4));
        $sheet->setCellValue("N" . ($signRow + 4), $headmasterName);
        $sheet->getStyle("N" . ($signRow + 4))->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("N" . ($signRow + 4))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("N" . ($signRow + 5) . ":S" . ($signRow + 5));
        $sheet->setCellValue("N" . ($signRow + 5), "NIP: " . $headmasterNip);
        $sheet->getStyle("N" . ($signRow + 5))->getFont()->setSize(9);
        $sheet->getStyle("N" . ($signRow + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($request->filled('download_token')) {
            if (!headers_sent()) {
                @setcookie('download_token', $request->query('download_token'), time() + 60, '/', '', false, false);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "Buku_Tamu_{$monthName}_{$selectedYear}.xlsx";

        $response = response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);

        if ($request->filled('download_token')) {
            $response->headers->setCookie(cookie('download_token', $request->query('download_token'), 1, '/', null, false, false));
        }

        return $response;
    }

    /**
     * Direct download PDF for Guest Book.
     */
    public function downloadPdf(Request $request)
    {
        ini_set('memory_limit', '512M');
        $selectedMonth = (int) $request->get('month', Carbon::now()->month);
        $selectedYear = (int) $request->get('year', 2026);
        if ($selectedYear < 2026 || $selectedYear > 2030) {
            $selectedYear = 2026;
        }
        $selectedCategory = $request->get('category', 'all');

        $query = GuestBook::whereYear('date', $selectedYear)->whereMonth('date', $selectedMonth);
        if ($selectedCategory && $selectedCategory !== 'all') {
            if (in_array($selectedCategory, ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes'])) {
                $query->whereIn('category', ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes']);
            } else {
                $query->where('category', $selectedCategory);
            }
        }
        $guests = $query->orderBy('date', 'asc')->orderBy('time', 'asc')->get();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $monthName = $monthNames[$selectedMonth] ?? $selectedMonth;

        $principal = \App\Models\Employee::where(function($q) {
            $q->where('position', 'like', '%Kepala Sekolah%')
              ->orWhere('position', 'like', '%Kepsek%');
        })->first();
        if (!$principal) {
            $ksUser = \App\Models\User::where('role', 'kepala_sekolah')->with('employee')->first();
            $principal = $ksUser?->employee;
        }
        $headmasterName = $principal?->name ?? setting('headmaster_name', setting('school_principal_name', 'Andreas Setiyono, S.Pd.Gr., M.Kom.'));
        $headmasterNip = $principal?->niy ?? $principal?->nuptk ?? $principal?->nik ?? setting('headmaster_nip', setting('school_principal_nip', '-'));
        $schoolCity = setting('school_city', 'Malang');
        $appName = setting('unit_name', setting('app_name', 'SD Anak Saleh'));
        $appAddress = setting('app_address', 'Jl. Danau Ranau, Sawojajar, Kota Malang, Jawa Timur 65139');
        $appPhone = setting('app_phone', '+62 812-3456-7890');

        $fileName = "Buku_Tamu_{$monthName}_{$selectedYear}.pdf";

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.guest-books.pdf', [
            'guests' => $guests,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedCategory' => $selectedCategory,
            'monthName' => $monthName,
            'schoolCity' => $schoolCity,
            'headmasterName' => $headmasterName,
            'headmasterNip' => $headmasterNip,
            'appName' => $appName,
            'appAddress' => $appAddress,
            'appPhone' => $appPhone,
        ])->setPaper('a4', 'landscape');

        $response = $pdf->download($fileName);
        if ($request->filled('download_token')) {
            $response->headers->setCookie(cookie('download_token', $request->query('download_token'), 1, '/', null, false, false));
        }
        return $response;
    }

    /**
     * Printable view of Guest Book.
     */
    public function print(Request $request)
    {
        $selectedMonth = (int) $request->get('month', Carbon::now()->month);
        $selectedYear = (int) $request->get('year', 2026);
        if ($selectedYear < 2026 || $selectedYear > 2030) {
            $selectedYear = 2026;
        }
        $selectedCategory = $request->get('category', 'all');

        $query = GuestBook::whereYear('date', $selectedYear)->whereMonth('date', $selectedMonth);
        if ($selectedCategory && $selectedCategory !== 'all') {
            if (in_array($selectedCategory, ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes'])) {
                $query->whereIn('category', ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes']);
            } else {
                $query->where('category', $selectedCategory);
            }
        }
        $guests = $query->orderBy('date', 'asc')->orderBy('time', 'asc')->get();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $monthName = $monthNames[$selectedMonth] ?? $selectedMonth;

        $principal = \App\Models\Employee::where(function($q) {
            $q->where('position', 'like', '%Kepala Sekolah%')
              ->orWhere('position', 'like', '%Kepsek%');
        })->first();
        if (!$principal) {
            $ksUser = \App\Models\User::where('role', 'kepala_sekolah')->with('employee')->first();
            $principal = $ksUser?->employee;
        }
        $headmasterName = $principal?->name ?? setting('headmaster_name', setting('school_principal_name', 'Andreas Setiyono, S.Pd.Gr., M.Kom.'));
        $headmasterNip = $principal?->niy ?? $principal?->nuptk ?? $principal?->nik ?? setting('headmaster_nip', setting('school_principal_nip', '-'));
        $schoolCity = setting('school_city', 'Malang');
        $appName = setting('unit_name', setting('app_name', 'SD Anak Saleh'));
        $appAddress = setting('app_address', 'Jl. Danau Ranau, Sawojajar, Kota Malang, Jawa Timur 65139');
        $appPhone = setting('app_phone', '+62 812-3456-7890');

        return view('admin.guest-books.print', compact(
            'guests',
            'selectedMonth',
            'selectedYear',
            'selectedCategory',
            'monthName',
            'schoolCity',
            'headmasterName',
            'headmasterNip',
            'appName',
            'appAddress',
            'appPhone'
        ));
    }

    /**
     * Validate common guestbook input fields.
     */
    private function validateGuestBook(Request $request): array
    {
        $category = $request->input('category', 'Umum');

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'category' => 'required|in:Umum,Dinas,Puskesmas/Dinkes,SPMB',
            'institution' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'purpose' => $category === 'SPMB' ? 'nullable|string' : 'required|string',
            'host_recipient' => 'nullable|string|max:255',
            'feedback' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'nullable|string|max:10',

            // Fields khusus SPMB
            'student_name' => $category === 'SPMB' ? 'required|string|max:255' : 'nullable|string|max:255',
            'student_birth_date' => 'nullable|date',
            'therapy_history' => 'nullable|string|in:Ya,Tidak',
            'therapy_diagnosis' => ($category === 'SPMB' && $request->input('therapy_history') === 'Ya') ? 'required|string|max:255' : 'nullable|string|max:255',
        ];

        $messages = [
            'name.required' => match($category) {
                'SPMB' => 'Nama Orang Tua / Wali wajib diisi.',
                'Dinas' => 'Nama Pejabat / Tamu wajib diisi.',
                'Puskesmas/Dinkes' => 'Nama Petugas Kesehatan wajib diisi.',
                default => 'Nama lengkap tamu wajib diisi.',
            },
            'phone.required' => 'Nomor Handphone / WhatsApp wajib diisi.',
            'category.required' => 'Kategori kunjungan wajib dipilih.',
            'category.in' => 'Kategori kunjungan yang dipilih tidak valid.',
            'date.required' => 'Tanggal kunjungan wajib diisi.',
            'date.date' => 'Format tanggal kunjungan tidak valid.',
            'student_name.required' => 'Nama calon murid wajib diisi untuk kategori SPMB.',
            'student_birth_date.date' => 'Format tanggal lahir calon murid tidak valid.',
            'therapy_diagnosis.required' => 'Diagnosa terapi wajib diisi jika anak pernah menjalani terapi.',
            'purpose.required' => match($category) {
                'Puskesmas/Dinkes' => 'Kegiatan pelayanan kesehatan wajib diisi.',
                'Dinas' => 'Keperluan / agenda kedinasan wajib diisi.',
                default => 'Keperluan kunjungan wajib diisi.',
            },
        ];

        $validated = $request->validate($rules, $messages);

        // Fallback waktu jika dikosongkan
        if (empty($validated['time'])) {
            $validated['time'] = Carbon::now()->format('H:i');
        }

        // Jika SPMB dan purpose kosong, isi otomatis agar konsisten
        if ($category === 'SPMB' && empty($validated['purpose'])) {
            $childInfo = !empty($validated['student_name']) ? " (Anak: {$validated['student_name']})" : "";
            $validated['purpose'] = "Konsultasi SPMB / Calon Murid{$childInfo}";
        }

        // Data hygiene berdasarkan kategori yang dipilih
        if ($category !== 'SPMB') {
            $validated['student_name'] = null;
            $validated['student_birth_date'] = null;
            $validated['therapy_history'] = null;
            $validated['therapy_diagnosis'] = null;
        } else {
            if (empty($validated['therapy_history'])) {
                $validated['therapy_history'] = 'Tidak';
            }
            if ($validated['therapy_history'] !== 'Ya') {
                $validated['therapy_diagnosis'] = null;
            }
        }

        return $validated;
    }
}
