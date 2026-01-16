<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Treatment;
use App\Models\Branch;

class TreatmentHistoryImportSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting treatment history import...');

        // Get branch "สาขาลำลูกกา"
        $branch = Branch::where('name', 'สาขาลำลูกกา')->first();
        if (!$branch) {
            $this->command->error('Branch "สาขาลำลูกกา" not found!');
            return;
        }

        $this->command->info("Using branch: {$branch->name} (ID: {$branch->id})");

        // CSV files directory
        $csvDir = 'C:/Users/nutch/Downloads/รวม/';

        // CSV files with their format type
        // Format A: มค-มิย (old format)
        // Format B: กค-ธค (new format with phone number column)
        $csvFiles = [
            // Format A files
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - มกราคม 68.csv', 'format' => 'A'],
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - กุมภาพันธ์ 68.csv', 'format' => 'A'],
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - มีนาคม 68.csv', 'format' => 'A'],
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - เมษายน 68.csv', 'format' => 'A'],
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - พฤษภาคม  68.csv', 'format' => 'A'],
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - มิถุนายน 68.csv', 'format' => 'A'],
            // Format B files
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - กรกฏาคม 68.csv', 'format' => 'B'],
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - สิงหาคม 68.csv', 'format' => 'B'],
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - กันยายน 68.csv', 'format' => 'B'],
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - ตุลาคม 68.csv', 'format' => 'B'],
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - พฤศจิกายน 68.csv', 'format' => 'B'],
            ['file' => '__รายชื่อคนไข้ซื้อคอร์ส _ รายครั้ง ปี 68  สาขาลำลูกกา - ธันวาคม 68.csv', 'format' => 'B'],
        ];

        $totalImported = 0;
        $totalSkipped = 0;
        $patientNotFound = [];

        // Cache patients by HN for faster lookup
        $patients = Patient::withoutGlobalScopes()
            ->whereNotNull('hn_number')
            ->pluck('id', 'hn_number')
            ->toArray();

        $this->command->info("Found " . count($patients) . " patients in database");

        foreach ($csvFiles as $csvInfo) {
            $csvFile = $csvInfo['file'];
            $format = $csvInfo['format'];
            $filePath = $csvDir . $csvFile;

            if (!file_exists($filePath)) {
                $this->command->warn("File not found: {$csvFile}");
                continue;
            }

            $this->command->info("Processing ({$format}): {$csvFile}");

            $handle = fopen($filePath, 'r');
            if (!$handle) {
                $this->command->error("Cannot open file: {$csvFile}");
                continue;
            }

            // Skip header row
            fgetcsv($handle);

            $fileImported = 0;
            $fileSkipped = 0;
            $lastDate = null;

            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty rows or rows without HN
                if (empty($row[2]) || !is_numeric($row[2])) {
                    continue;
                }

                $data = $this->parseRow($row, $format);

                if (!$data) {
                    $fileSkipped++;
                    continue;
                }

                // Use last valid date if current row has no date
                if (!empty($data['date'])) {
                    $lastDate = $this->parseDate($data['date']);
                }

                // Find patient by HN
                $patientId = $patients[$data['hn']] ?? null;

                if (!$patientId) {
                    if (!in_array($data['hn'], $patientNotFound)) {
                        $patientNotFound[] = $data['hn'];
                    }
                    $fileSkipped++;
                    continue;
                }

                // Build treatment notes
                $treatmentNotes = [];
                if ($data['course_detail']) {
                    $treatmentNotes[] = "จำนวน: {$data['course_detail']}";
                }
                if ($data['package']) {
                    $treatmentNotes[] = "แพคเกจ: {$data['package']}";
                }
                if ($data['price']) {
                    $treatmentNotes[] = "ราคา: {$data['price']}";
                }
                if ($data['note']) {
                    $treatmentNotes[] = "หมายเหตุ: {$data['note']}";
                }
                if ($data['payment']) {
                    $treatmentNotes[] = "ชำระ: {$data['payment']}";
                }
                if ($data['bill_number']) {
                    $treatmentNotes[] = "เลขบิล: {$data['bill_number']}";
                }

                // Create treatment record
                Treatment::withoutGlobalScopes()->create([
                    'patient_id' => $patientId,
                    'branch_id' => $branch->id,
                    'chief_complaint' => $data['course_name'] ?: ($data['symptom'] ?: 'ไม่ระบุ'),
                    'treatment_notes' => implode("\n", $treatmentNotes),
                    'started_at' => $lastDate,
                    'completed_at' => $lastDate,
                    'billing_status' => 'paid',
                ]);

                $fileImported++;
            }

            fclose($handle);

            $this->command->info("  - Imported: {$fileImported}, Skipped: {$fileSkipped}");
            $totalImported += $fileImported;
            $totalSkipped += $fileSkipped;
        }

        $this->command->info('');
        $this->command->info('=== Import Summary ===');
        $this->command->info("Total imported: {$totalImported}");
        $this->command->info("Total skipped: {$totalSkipped}");

        if (!empty($patientNotFound)) {
            $this->command->warn("Patients not found (HN): " . implode(', ', array_slice($patientNotFound, 0, 20)));
            if (count($patientNotFound) > 20) {
                $this->command->warn("... and " . (count($patientNotFound) - 20) . " more");
            }
        }
    }

    private function parseRow(array $row, string $format): ?array
    {
        // Format A (มค-มิย): No phone column
        // 0: วันที่, 2: H.N., 9: คอร์ส, 10: จำนวนครั้ง, 11: ราคา, 12: หมายเหตุ, 13: ชำระ, 14: เลขบิล

        // Format B (กค-ธค): Has phone column
        // 0: วันที่, 2: H.N., 10: อาการ, 11: คอร์ส, 12: แพคเกจ, 13: ครั้งที่, 14: การชำระเงิน, 15: เลขบิล, 16: ราคา

        if ($format === 'A') {
            return [
                'date' => trim($row[0] ?? ''),
                'hn' => trim($row[2] ?? ''),
                'course_name' => $this->cleanCourseName($row[9] ?? ''),
                'course_detail' => trim($row[10] ?? ''),
                'package' => '',
                'price' => trim($row[11] ?? ''),
                'note' => trim($row[12] ?? ''),
                'payment' => trim($row[13] ?? ''),
                'bill_number' => trim($row[14] ?? ''),
                'symptom' => '',
            ];
        } else {
            // Format B (กค-ธค): Has phone column, symptom column
            // 0: วันที่, 2: H.N., 10: อาการ, 11: คอร์ส(จำนวนครั้ง), 12: แพคเกจ, 13: ครั้งที่, 14: การชำระ, 15: เลขบิล, 16: ราคา
            $symptom = trim($row[10] ?? '');
            $courseCount = trim($row[11] ?? '');
            $package = $this->cleanCourseName($row[12] ?? '');

            return [
                'date' => trim($row[0] ?? ''),
                'hn' => trim($row[2] ?? ''),
                'symptom' => $symptom,
                'course_name' => $symptom ?: $courseCount, // ใช้อาการเป็นหลัก ถ้าไม่มีใช้ชื่อคอร์ส
                'course_detail' => $courseCount,
                'package' => $package,
                'price' => trim($row[16] ?? ''),
                'note' => trim($row[13] ?? ''), // ครั้งที่
                'payment' => trim($row[14] ?? ''),
                'bill_number' => trim($row[15] ?? ''),
            ];
        }
    }

    private function cleanCourseName(string $name): string
    {
        // Remove quotes and trim
        $name = trim($name, '" ');
        $name = str_replace('"""', '', $name);
        $name = str_replace('""', '', $name);
        return trim($name);
    }

    private function parseDate(string $dateStr): ?string
    {
        $dateStr = trim($dateStr);

        if (empty($dateStr)) {
            return null;
        }

        // Try d/m/Y format
        $parts = explode('/', $dateStr);
        if (count($parts) === 3) {
            $day = (int)$parts[0];
            $month = (int)$parts[1];
            $year = (int)$parts[2];

            // Convert Buddhist year to Christian year if needed
            if ($year > 2500) {
                $year -= 543;
            } elseif ($year < 100) {
                $year = 2025;
            }

            // Validate year
            if ($year < 2024 || $year > 2030) {
                return null;
            }

            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        return null;
    }
}
