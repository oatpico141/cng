<?php
/**
 * Update Commission and DF data from CSV
 * Run via browser: http://localhost/cng/public/update-commission-data.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<pre style='font-family: Tahoma, sans-serif; font-size: 14px;'>";
echo "=== อัพเดทข้อมูลค่าคอมมิชชั่นและค่ามือ ===\n\n";

// ========================================
// 1. อัพเดท Services (รายครั้ง) - ค่ามือ
// ========================================
echo "--- อัพเดทบริการรายครั้ง (ค่ามือ) ---\n";

$services = [
    ['name' => 'กายเริ่มต้น M', 'price' => 1200, 'df_rate' => 30, 'duration' => 60],
    ['name' => 'กายเริ่มต้น L', 'price' => 1600, 'df_rate' => 35, 'duration' => 90],
    ['name' => 'กายฟื้นฟู PMS M', 'price' => 1800, 'df_rate' => 40, 'duration' => 75],
    ['name' => 'กายฟื้นฟู PMS L', 'price' => 2200, 'df_rate' => 40, 'duration' => 90],
    ['name' => 'กายทีย์ออนโฟ Focus M', 'price' => 2900, 'df_rate' => 50, 'duration' => 75],
    ['name' => 'กายทีย์ออนโฟ Focus L', 'price' => 3300, 'df_rate' => 50, 'duration' => 90],
    ['name' => 'กายคูสต์ Radial M', 'price' => 1990, 'df_rate' => 40, 'duration' => 60],
    ['name' => 'กายคูสต์ Radial L', 'price' => 2500, 'df_rate' => 40, 'duration' => 75],
    ['name' => 'กายพรีเมียม M', 'price' => 2500, 'df_rate' => 40, 'duration' => 75],
    ['name' => 'กายพรีเมียม L', 'price' => 2900, 'df_rate' => 50, 'duration' => 90],
];

$serviceUpdated = 0;
foreach ($services as $service) {
    $updated = DB::table('services')
        ->where('name', $service['name'])
        ->update([
            'default_price' => $service['price'],
            'default_df_rate' => $service['df_rate'],
            'default_duration_minutes' => $service['duration'],
            'updated_at' => now(),
        ]);

    if ($updated) {
        echo "✓ อัพเดท: {$service['name']} - ราคา {$service['price']}, ค่ามือ {$service['df_rate']}, {$service['duration']} นาที\n";
        $serviceUpdated++;
    } else {
        echo "⚠ ไม่พบ: {$service['name']}\n";
    }
}

echo "\nอัพเดทบริการรายครั้ง: {$serviceUpdated} รายการ\n\n";

// ========================================
// 2. อัพเดท Course Packages - ค่าคอมและค่ามือ
// ========================================
echo "--- อัพเดทแพ็คเกจคอร์ส (ค่าคอมและค่ามือ) ---\n";

$packages = [
    // 5+1 packages
    ['name' => 'กายเริ่มต้น M (5+1)', 'price' => 6000, 'df_amount' => 30, 'commission_rate' => 380, 'commission_installment' => 330, 'paid' => 5, 'bonus' => 1],
    ['name' => 'กายเริ่มต้น L (5+1)', 'price' => 8000, 'df_amount' => 35, 'commission_rate' => 460, 'commission_installment' => 390, 'paid' => 5, 'bonus' => 1],
    ['name' => 'กายฟื้นฟู PMS M (5+1)', 'price' => 9000, 'df_amount' => 40, 'commission_rate' => 540, 'commission_installment' => 465, 'paid' => 5, 'bonus' => 1],
    ['name' => 'กายคูสต์ Radial M (5+1)', 'price' => 9950, 'df_amount' => 40, 'commission_rate' => 540, 'commission_installment' => 465, 'paid' => 5, 'bonus' => 1],
    ['name' => 'กายฟื้นฟู PMS L (5+1)', 'price' => 11000, 'df_amount' => 40, 'commission_rate' => 590, 'commission_installment' => 510, 'paid' => 5, 'bonus' => 1],
    ['name' => 'กายพรีเมียม M (5+1)', 'price' => 12500, 'df_amount' => 40, 'commission_rate' => 640, 'commission_installment' => 540, 'paid' => 5, 'bonus' => 1],
    ['name' => 'กายทีย์ออนโฟ Focus M (5+1)', 'price' => 14500, 'df_amount' => 50, 'commission_rate' => 750, 'commission_installment' => 630, 'paid' => 5, 'bonus' => 1],
    ['name' => 'กายทีย์ออนโฟ Focus L (5+1)', 'price' => 16500, 'df_amount' => 50, 'commission_rate' => 800, 'commission_installment' => 660, 'paid' => 5, 'bonus' => 1],

    // 10+3 packages
    ['name' => 'กายเริ่มต้น M (10+3)', 'price' => 12000, 'df_amount' => 30, 'commission_rate' => 790, 'commission_installment' => 690, 'paid' => 10, 'bonus' => 3],
    ['name' => 'กายเริ่มต้น L (10+3)', 'price' => 16000, 'df_amount' => 35, 'commission_rate' => 955, 'commission_installment' => 815, 'paid' => 10, 'bonus' => 3],
    ['name' => 'กายฟื้นฟู PMS M (10+3)', 'price' => 18000, 'df_amount' => 40, 'commission_rate' => 1070, 'commission_installment' => 940, 'paid' => 10, 'bonus' => 3],
    ['name' => 'กายคูสต์ Radial M (10+3)', 'price' => 19900, 'df_amount' => 40, 'commission_rate' => 1120, 'commission_installment' => 970, 'paid' => 10, 'bonus' => 3],
    ['name' => 'กายฟื้นฟู PMS L (10+3)', 'price' => 22000, 'df_amount' => 40, 'commission_rate' => 1220, 'commission_installment' => 1030, 'paid' => 10, 'bonus' => 3],
    ['name' => 'กายพรีเมียม M (10+3)', 'price' => 25000, 'df_amount' => 40, 'commission_rate' => 1320, 'commission_installment' => 1120, 'paid' => 10, 'bonus' => 3],
    ['name' => 'กายทีย์ออนโฟ Focus M (10+3)', 'price' => 29000, 'df_amount' => 50, 'commission_rate' => 1550, 'commission_installment' => 1310, 'paid' => 10, 'bonus' => 3],
    ['name' => 'กายทีย์ออนโฟ Focus L (10+3)', 'price' => 33000, 'df_amount' => 50, 'commission_rate' => 1650, 'commission_installment' => 1400, 'paid' => 10, 'bonus' => 3],
];

$packageUpdated = 0;
foreach ($packages as $pkg) {
    $updated = DB::table('course_packages')
        ->where('name', $pkg['name'])
        ->update([
            'price' => $pkg['price'],
            'df_amount' => $pkg['df_amount'],
            'commission_rate' => $pkg['commission_rate'],
            'commission_installment' => $pkg['commission_installment'],
            'paid_sessions' => $pkg['paid'],
            'bonus_sessions' => $pkg['bonus'],
            'total_sessions' => $pkg['paid'] + $pkg['bonus'],
            'updated_at' => now(),
        ]);

    if ($updated) {
        echo "✓ อัพเดท: {$pkg['name']} - ราคา {$pkg['price']}, ค่ามือ {$pkg['df_amount']}, คอม(สด) {$pkg['commission_rate']}, คอม(ผ่อน) {$pkg['commission_installment']}\n";
        $packageUpdated++;
    } else {
        echo "⚠ ไม่พบ: {$pkg['name']}\n";
    }
}

echo "\nอัพเดทแพ็คเกจคอร์ส: {$packageUpdated} รายการ\n\n";

// ========================================
// สรุป
// ========================================
echo "===========================================\n";
echo "สรุป:\n";
echo "- บริการรายครั้งที่อัพเดท: {$serviceUpdated} รายการ\n";
echo "- แพ็คเกจคอร์สที่อัพเดท: {$packageUpdated} รายการ\n";
echo "===========================================\n";

echo "</pre>";
