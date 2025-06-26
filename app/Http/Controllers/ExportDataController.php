<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Beswan;
use App\Models\CalonBeswan;
use App\Models\BeasiswaPeriods;
use App\Models\BeasiswaApplication;
use App\Models\BeasiswaRecipients;
use App\Models\DocumentType;
use App\Models\Announcement;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ExportDataController extends Controller
{
    /**
     * Handle the export data request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */    public function export(Request $request)
    {
        // Get tables as comma-separated string and convert to array
        $tablesString = $request->query('tables');
        $tables = $tablesString ? explode(',', $tablesString) : [];
        
        // Validate request
        $validator = Validator::make([
            'tables' => $tables,
            'format' => $request->query('format'),
            'dateRange' => $request->query('dateRange', 'all'),
        ], [
            'tables' => 'required|array',
            'format' => 'required|string|in:csv,excel,json',
            'dateRange' => 'nullable|string|in:all,today,this_week,this_month,this_year',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        // Check permission (admin/superadmin only)
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'Unauthorized: Anda tidak memiliki akses untuk mengekspor data'], 403);
        }        try {
            // Get selected tables
            $tables = $tables;
            $format = $request->query('format');
            $dateRange = $request->query('dateRange', 'all');

            // Collect data from selected tables
            $exportData = $this->collectData($tables, $dateRange);

            // Generate export file based on format
            switch ($format) {
                case 'excel':
                    return $this->exportToExcel($exportData, $tables);
                case 'csv':
                    return $this->exportToCsv($exportData, $tables);
                case 'json':
                    return $this->exportToJson($exportData);
                default:
                    return response()->json(['message' => 'Format tidak didukung'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Export data error: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengekspor data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Collect data from selected tables based on date range
     *
     * @param array $tables
     * @param string $dateRange
     * @return array
     */
    private function collectData(array $tables, string $dateRange)
    {
        $exportData = [];
        $dateFilter = $this->getDateRangeFilter($dateRange);

        foreach ($tables as $table) {
            switch ($table) {
                case 'users':
                    $query = User::query();
                    if ($dateRange !== 'all') {
                        $query->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
                    }
                    $exportData[$table] = $query->select('id', 'name', 'email', 'role', 'created_at')->get();
                    break;

                case 'calon_beswans':
                    $query = CalonBeswan::query();
                    if ($dateRange !== 'all') {
                        $query->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
                    }
                    $exportData[$table] = $query->get();
                    break;

                case 'beswans':
                    $query = Beswan::query();
                    if ($dateRange !== 'all') {
                        $query->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
                    }
                    $exportData[$table] = $query->get();
                    break;

                case 'beasiswa_applications':
                    $query = BeasiswaApplication::query();
                    if ($dateRange !== 'all') {
                        $query->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
                    }
                    $exportData[$table] = $query->get();
                    break;

                case 'beasiswa_periods':
                    $query = BeasiswaPeriods::query();
                    if ($dateRange !== 'all') {
                        $query->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
                    }
                    $exportData[$table] = $query->get();
                    break;

                case 'documents':
                    $query = DocumentType::query();
                    if ($dateRange !== 'all') {
                        $query->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
                    }
                    $exportData[$table] = $query->get();
                    break;
                
                default:
                    // For any other table, we'll leave it empty to be safe
                    $exportData[$table] = [];
            }
        }

        return $exportData;
    }

    /**
     * Get date range filter based on selected option
     *
     * @param string $dateRange
     * @return array
     */
    private function getDateRangeFilter(string $dateRange)
    {
        $now = Carbon::now();

        switch ($dateRange) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                ];
            case 'this_week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek(),
                ];
            case 'this_month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                ];
            case 'this_year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear(),
                ];
            default:
                return [
                    'start' => Carbon::minValue(),
                    'end' => $now,
                ];
        }
    }

    /**
     * Export data to Excel format
     *
     * @param array $data
     * @param array $tables
     * @return \Illuminate\Http\Response
     */
    private function exportToExcel(array $data, array $tables)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Remove default sheet
        
        // Create a sheet for each table
        foreach ($tables as $index => $tableName) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($tableName);

            if (!empty($data[$tableName])) {
                // Add headers
                $headers = array_keys($data[$tableName][0]->toArray());
                $sheet->fromArray([$headers], NULL, 'A1');
                
                // Style headers
                $headerStyle = [
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'EEEEEE']
                    ]
                ];
                $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);
                
                // Add data rows
                $dataArray = [];
                foreach ($data[$tableName] as $row) {
                    $dataArray[] = array_values($row->toArray());
                }
                
                if (!empty($dataArray)) {
                    $sheet->fromArray($dataArray, NULL, 'A2');
                }
                
                // Auto size columns
                foreach (range('A', $sheet->getHighestColumn()) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            } else {
                $sheet->setCellValue('A1', 'No data available for this table');
            }
        }

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);
        
        // Create response
        $filename = 'bersekolah_export_' . date('Y-m-d') . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        
        $tempFile = tempnam(sys_get_temp_dir(), 'export');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ])->deleteFileAfterSend(true);
    }
    
    /**
     * Export data to CSV format
     *
     * @param array $data
     * @param array $tables
     * @return \Illuminate\Http\Response
     */
    private function exportToCsv(array $data, array $tables)
    {
        // Since CSV can only have one sheet, we'll combine all selected tables
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Export Data');
        
        $rowCount = 1;
        
        foreach ($tables as $tableName) {
            if (!empty($data[$tableName])) {
                // Add table name as header
                $sheet->setCellValue('A' . $rowCount, strtoupper($tableName));
                $sheet->getStyle('A' . $rowCount)->getFont()->setBold(true);
                $rowCount++;
                
                // Add headers
                $headers = array_keys($data[$tableName][0]->toArray());
                $colIndex = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($colIndex . $rowCount, $header);
                    $colIndex++;
                }
                $sheet->getStyle('A' . $rowCount . ':' . $sheet->getHighestColumn() . $rowCount)->getFont()->setBold(true);
                $rowCount++;
                
                // Add data
                foreach ($data[$tableName] as $row) {
                    $rowData = $row->toArray();
                    $colIndex = 'A';
                    foreach ($rowData as $value) {
                        $sheet->setCellValue($colIndex . $rowCount, $value);
                        $colIndex++;
                    }
                    $rowCount++;
                }
                
                // Add space between tables
                $rowCount++;
            }
        }
        
        // Create response
        $filename = 'bersekolah_export_' . date('Y-m-d') . '.csv';
        
        $writer = new Csv($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'export');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ])->deleteFileAfterSend(true);
    }
    
    /**
     * Export data to JSON format
     *
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    private function exportToJson(array $data)
    {
        // Format the data with metadata
        $exportData = [
            'meta' => [
                'exported_at' => Carbon::now()->toIso8601String(),
                'exported_by' => Auth::user()->name,
                'tables_count' => count($data),
                'tables' => array_keys($data),
            ],
            'data' => $data,
        ];
        
        // Create response
        $filename = 'bersekolah_export_' . date('Y-m-d') . '.json';
        
        return response()->json($exportData)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
