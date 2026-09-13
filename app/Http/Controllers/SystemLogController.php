<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemLogController extends Controller
{
    /**
     * Display a listing of log entries and files.
     */
    public function index(Request $request)
    {
        $logDir = storage_path('logs');
        $files = [];

        if (File::exists($logDir)) {
            $rawFiles = File::files($logDir);
            foreach ($rawFiles as $file) {
                if ($file->getExtension() === 'log') {
                    $files[] = [
                        'name' => $file->getFilename(),
                        'path' => $file->getPathname(),
                        'size' => $this->formatBytes($file->getSize()),
                        'raw_size' => $file->getSize(),
                        'modified_at' => \Carbon\Carbon::createFromTimestamp($file->getMTime())->diffForHumans(),
                        'modified_timestamp' => $file->getMTime(),
                    ];
                }
            }
        }

        // Sort files by modified timestamp descending
        usort($files, function ($a, $b) {
            return $b['modified_timestamp'] <=> $a['modified_timestamp'];
        });

        // Determine current selected file
        $selectedFileName = $request->get('file', $files[0]['name'] ?? 'laravel.log');
        // Prevent path traversal
        $selectedFileName = basename($selectedFileName);
        $selectedFilePath = $logDir . DIRECTORY_SEPARATOR . $selectedFileName;

        $selectedFile = null;
        foreach ($files as $f) {
            if ($f['name'] === $selectedFileName) {
                $selectedFile = $f;
                break;
            }
        }

        $allEntries = [];
        $levelStats = [
            'total' => 0,
            'emergency' => 0,
            'alert' => 0,
            'critical' => 0,
            'error' => 0,
            'warning' => 0,
            'notice' => 0,
            'info' => 0,
            'debug' => 0,
        ];

        if (File::exists($selectedFilePath)) {
            $allEntries = $this->parseLogFile($selectedFilePath);
            
            foreach ($allEntries as $entry) {
                $levelKey = strtolower($entry['level']);
                if (isset($levelStats[$levelKey])) {
                    $levelStats[$levelKey]++;
                }
                $levelStats['total']++;
            }
        }

        // Filter by Level
        $filterLevel = strtolower((string) $request->get('level', 'all'));
        $searchKeyword = trim((string) $request->get('search', ''));

        $filteredEntries = array_filter($allEntries, function ($entry) use ($filterLevel, $searchKeyword) {
            $matchLevel = true;
            if ($filterLevel !== 'all') {
                if ($filterLevel === 'error_critical') {
                    $matchLevel = in_array(strtolower($entry['level']), ['error', 'critical', 'emergency', 'alert']);
                } else {
                    $matchLevel = strtolower($entry['level']) === $filterLevel;
                }
            }

            $matchSearch = true;
            if ($searchKeyword !== '') {
                $keywordLower = strtolower($searchKeyword);
                $matchSearch = (
                    stripos($entry['message'], $keywordLower) !== false ||
                    stripos($entry['timestamp'], $keywordLower) !== false ||
                    stripos($entry['stack_trace'], $keywordLower) !== false
                );
            }

            return $matchLevel && $matchSearch;
        });

        // Paginate results
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 25;
        $currentItems = array_slice($filteredEntries, ($currentPage - 1) * $perPage, $perPage);
        $paginatedEntries = new LengthAwarePaginator(
            $currentItems,
            count($filteredEntries),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.system-logs.index', compact(
            'files',
            'selectedFileName',
            'selectedFile',
            'paginatedEntries',
            'levelStats',
            'filterLevel',
            'searchKeyword'
        ));
    }

    /**
     * Download the selected log file.
     */
    public function download(Request $request): BinaryFileResponse
    {
        $fileName = basename((string) $request->get('file', 'laravel.log'));
        $filePath = storage_path('logs/' . $fileName);

        if (!File::exists($filePath)) {
            abort(404, 'File log tidak ditemukan.');
        }

        return response()->download($filePath, $fileName);
    }

    /**
     * Clear / empty the selected log file.
     */
    public function clear(Request $request)
    {
        $fileName = basename((string) $request->get('file', 'laravel.log'));
        $filePath = storage_path('logs/' . $fileName);

        if (File::exists($filePath)) {
            File::put($filePath, '');
            return back()->with('success', "File log {$fileName} berhasil dikosongkan.");
        }

        return back()->with('error', "File log {$fileName} tidak ditemukan.");
    }

    /**
     * Delete a log file.
     */
    public function destroy(Request $request)
    {
        $fileName = basename((string) $request->get('file', ''));
        if (empty($fileName) || $fileName === 'laravel.log') {
            return back()->with('error', 'File log utama tidak boleh dihapus, silakan gunakan fitur Bersihkan Log.');
        }

        $filePath = storage_path('logs/' . $fileName);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->route('system-logs.index')->with('success', "File log {$fileName} berhasil dihapus.");
        }

        return back()->with('error', "File log {$fileName} tidak ditemukan.");
    }

    /**
     * Parse the log file into structured array.
     */
    protected function parseLogFile(string $filePath): array
    {
        // If file is very large (> 20MB), read the last 10MB to avoid memory exhaustion
        $maxBytes = 10 * 1024 * 1024;
        $fileSize = filesize($filePath);
        $content = '';

        if ($fileSize > $maxBytes) {
            $fp = fopen($filePath, 'r');
            fseek($fp, -$maxBytes, SEEK_END);
            $content = fread($fp, $maxBytes);
            fclose($fp);
            // discard partial first line
            $firstNewline = strpos($content, "\n");
            if ($firstNewline !== false) {
                $content = substr($content, $firstNewline + 1);
            }
        } else {
            $content = File::get($filePath);
        }

        if (empty(trim($content))) {
            return [];
        }

        // Monolog standard pattern:
        // [YYYY-MM-DD HH:MM:SS] environment.LEVEL: Message {"context":...} [extra]
        $pattern = '/^\[(\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:[\+\-]\d{2}:\d{2})?)\]\s+(?:([a-zA-Z0-9_\-]+)\.)?([a-zA-Z]+):\s+(.*?)(?=\n\[\d{4}-\d{2}-\d{2}|\Z)/sm';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $entries = [];

        foreach ($matches as $match) {
            $timestamp = $match[1];
            $environment = $match[2] ?: 'local';
            $level = strtoupper($match[3]);
            $body = trim($match[4]);

            // Separate main error header from stack trace if present
            $lines = explode("\n", $body);
            $mainMessage = $lines[0] ?? '';
            $stackTrace = count($lines) > 1 ? trim(implode("\n", array_slice($lines, 1))) : '';

            $entries[] = [
                'timestamp' => $timestamp,
                'environment' => $environment,
                'level' => $level,
                'message' => $mainMessage,
                'stack_trace' => $stackTrace,
                'raw' => $body,
            ];
        }

        // Reverse to show newest entries first
        return array_reverse($entries);
    }

    /**
     * Helper to format file bytes to human readable format.
     */
    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
