<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateCommissionData extends Command
{
    protected $signature = 'update:commission';
    protected $description = 'Update commission and DF data from CSV';

    public function handle()
    {
        $this->info('=== อัพเดทข้อมูลค่าคอมมิชชั่นและค่ามือ ===');
        $this->newLine();

        // Services - update ค่ามือ
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

        $this->info('--- อัพเดทบริการรายครั้ง ---');
        $svcCount = 0;
        foreach ($services as $s) {
            $updated = DB::table('services')->where('name', $s['name'])->update([
                'default_price' => $s['price'],
                'default_df_rate' => $s['df_rate'],
                'default_duration_minutes' => $s['duration'],
                'updated_at' => now(),
            ]);
            if ($updated) {
                $this->line("✓ {$s['name']} - ราคา {$s['price']}, ค่ามือ {$s['df_rate']}/ครั้ง");
                $svcCount++;
            } else {
                $this->warn("⚠ ไม่พบ: {$s['name']}");
            }
        }

        $this->newLine();
        $this->info('--- อัพเดทแพ็คเกจคอร์ส ---');

        // Course Packages
        $packages = [
            ['name' => 'กายเริ่มต้น M (5+1)', 'price' => 6000, 'df_amount' => 30, 'commission_rate' => 380, 'commission_installment' => 330, 'paid' => 5, 'bonus' => 1],
            ['name' => 'กายเริ่มต้น L (5+1)', 'price' => 8000, 'df_amount' => 35, 'commission_rate' => 460, 'commission_installment' => 390, 'paid' => 5, 'bonus' => 1],
            ['name' => 'กายฟื้นฟู PMS M (5+1)', 'price' => 9000, 'df_amount' => 40, 'commission_rate' => 540, 'commission_installment' => 465, 'paid' => 5, 'bonus' => 1],
            ['name' => 'กายคูสต์ Radial M (5+1)', 'price' => 9950, 'df_amount' => 40, 'commission_rate' => 540, 'commission_installment' => 465, 'paid' => 5, 'bonus' => 1],
            ['name' => 'กายฟื้นฟู PMS L (5+1)', 'price' => 11000, 'df_amount' => 40, 'commission_rate' => 590, 'commission_installment' => 510, 'paid' => 5, 'bonus' => 1],
            ['name' => 'กายพรีเมียม M (5+1)', 'price' => 12500, 'df_amount' => 40, 'commission_rate' => 640, 'commission_installment' => 540, 'paid' => 5, 'bonus' => 1],
            ['name' => 'กายทีย์ออนโฟ Focus M (5+1)', 'price' => 14500, 'df_amount' => 50, 'commission_rate' => 750, 'commission_installment' => 630, 'paid' => 5, 'bonus' => 1],
            ['name' => 'กายทีย์ออนโฟ Focus L (5+1)', 'price' => 16500, 'df_amount' => 50, 'commission_rate' => 800, 'commission_installment' => 660, 'paid' => 5, 'bonus' => 1],
            ['name' => 'กายเริ่มต้น M (10+3)', 'price' => 12000, 'df_amount' => 30, 'commission_rate' => 790, 'commission_installment' => 690, 'paid' => 10, 'bonus' => 3],
            ['name' => 'กายเริ่มต้น L (10+3)', 'price' => 16000, 'df_amount' => 35, 'commission_rate' => 955, 'commission_installment' => 815, 'paid' => 10, 'bonus' => 3],
            ['name' => 'กายฟื้นฟู PMS M (10+3)', 'price' => 18000, 'df_amount' => 40, 'commission_rate' => 1070, 'commission_installment' => 940, 'paid' => 10, 'bonus' => 3],
            ['name' => 'กายคูสต์ Radial M (10+3)', 'price' => 19900, 'df_amount' => 40, 'commission_rate' => 1120, 'commission_installment' => 970, 'paid' => 10, 'bonus' => 3],
            ['name' => 'กายฟื้นฟู PMS L (10+3)', 'price' => 22000, 'df_amount' => 40, 'commission_rate' => 1220, 'commission_installment' => 1030, 'paid' => 10, 'bonus' => 3],
            ['name' => 'กายพรีเมียม M (10+3)', 'price' => 25000, 'df_amount' => 40, 'commission_rate' => 1320, 'commission_installment' => 1120, 'paid' => 10, 'bonus' => 3],
            ['name' => 'กายทีย์ออนโฟ Focus M (10+3)', 'price' => 29000, 'df_amount' => 50, 'commission_rate' => 1550, 'commission_installment' => 1310, 'paid' => 10, 'bonus' => 3],
            ['name' => 'กายทีย์ออนโฟ Focus L (10+3)', 'price' => 33000, 'df_amount' => 50, 'commission_rate' => 1650, 'commission_installment' => 1400, 'paid' => 10, 'bonus' => 3],
        ];

        $pkgCount = 0;
        foreach ($packages as $p) {
            $updated = DB::table('course_packages')->where('name', $p['name'])->update([
                'price' => $p['price'],
                'df_amount' => $p['df_amount'],
                'commission_rate' => $p['commission_rate'],
                'commission_installment' => $p['commission_installment'],
                'paid_sessions' => $p['paid'],
                'bonus_sessions' => $p['bonus'],
                'total_sessions' => $p['paid'] + $p['bonus'],
                'updated_at' => now(),
            ]);
            if ($updated) {
                $this->line("✓ {$p['name']} - คอม(สด) {$p['commission_rate']}, คอม(ผ่อน) {$p['commission_installment']}, ค่ามือ {$p['df_amount']}/ครั้ง");
                $pkgCount++;
            } else {
                $this->warn("⚠ ไม่พบ: {$p['name']}");
            }
        }

        $this->newLine();
        $this->info("=== สรุป ===");
        $this->info("อัพเดทบริการ: {$svcCount} รายการ");
        $this->info("อัพเดทแพ็คเกจ: {$pkgCount} รายการ");

        return 0;
    }
}