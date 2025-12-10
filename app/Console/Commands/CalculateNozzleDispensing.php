<?php

namespace App\Console\Commands;

use App\Models\Nozzle;
use App\Models\NozzleReading;
use App\Models\Pump;
use App\Models\Tank;
use App\Models\Transaction;
use App\Models\Shift;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateNozzleDispensing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nozzles:calculate {shift_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'حساب اللترات المسحوبة من كل المسدسات وتسجيلها في العمليات وخصمها من الخزانات';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $shiftId = $this->argument('shift_id');
        
        // إذا لم يتم تحديد shift_id، نستخدم آخر شيفت مفتوح
        if (!$shiftId) {
            $shift = Shift::whereNull('end_time')->latest()->first();
            
            if (!$shift) {
                $this->error('❌ لا يوجد شيفت مفتوح حالياً');
                return 1;
            }
            
            $shiftId = $shift->id;
        } else {
            $shift = Shift::find($shiftId);
            
            if (!$shift) {
                $this->error('❌ الشيفت غير موجود');
                return 1;
            }
        }

        $this->info("🔄 بدء حساب اللترات للشيفت رقم: {$shiftId}");
        $this->info("👤 المستخدم: {$shift->user->name}");
        $this->newLine();

        DB::beginTransaction();

        try {
            // جلب كل الطلمبات
            $pumps = Pump::with(['nozzles', 'tank.fuel'])->get();
            
            $totalLitersDispensed = 0;
            $transactionsCreated = 0;

            foreach ($pumps as $pump) {
                $this->info("⛽ الطلمبة: {$pump->name}");
                
                $pumpTotalLiters = 0;

                foreach ($pump->nozzles as $nozzle) {
                    // القراءة الحالية للمسدس
                    $currentReading = $nozzle->meter_reading;
                    
                    // جلب آخر قراءة مسجلة لهذا المسدس
                    $lastReading = NozzleReading::where('nozzle_id', $nozzle->id)
                        ->latest()
                        ->first();
                    
                    $previousReading = $lastReading ? $lastReading->current_reading : 0;
                    
                    // حساب اللترات المسحوبة
                    $litersDispensed = $currentReading - $previousReading;
                    
                    if ($litersDispensed > 0) {
                        // حفظ القراءة الجديدة
                        NozzleReading::create([
                            'nozzle_id' => $nozzle->id,
                            'shift_id' => $shiftId,
                            'previous_reading' => $previousReading,
                            'current_reading' => $currentReading,
                            'liters_dispensed' => $litersDispensed,
                            'reading_date' => now(),
                        ]);
                        
                        $pumpTotalLiters += $litersDispensed;
                        
                        $this->line("  🔫 {$nozzle->name}: {$litersDispensed} لتر (من {$previousReading} إلى {$currentReading})");
                    } else {
                        $this->line("  🔫 {$nozzle->name}: لا توجد لترات جديدة");
                    }
                }

                // إذا كان هناك لترات مسحوبة من هذه الطلمبة
                if ($pumpTotalLiters > 0) {
                    $fuelPrice = $pump->tank->fuel->price_per_liter ?? 0;
                    $totalAmount = $pumpTotalLiters * $fuelPrice;
                    
                    // إنشاء عملية (Transaction)
                    $transaction = Transaction::create([
                        'shift_id' => $shiftId,
                        'pump_id' => $pump->id,
                        'client_id' => null,
                        'credit_liters' => 0,
                        'cash_liters' => $pumpTotalLiters,
                        'total_amount' => $totalAmount,
                        'notes' => "حساب تلقائي من المسدسات - {$pump->name}",
                    ]);
                    
                    // خصم اللترات من الخزان
                    $tank = $pump->tank;
                    $tank->current_level -= $pumpTotalLiters;
                    $tank->liters_drawn += $pumpTotalLiters;
                    $tank->save();
                    
                    $this->info("  ✅ تم إنشاء عملية: {$pumpTotalLiters} لتر - {$totalAmount} جنيه");
                    $this->info("  📉 تم خصم {$pumpTotalLiters} لتر من الخزان {$tank->name}");
                    $this->info("  📊 المخزون الحالي: {$tank->current_level} لتر");
                    
                    $totalLitersDispensed += $pumpTotalLiters;
                    $transactionsCreated++;
                }

                $this->newLine();
            }

            DB::commit();

            $this->newLine();
            $this->info("✅ تم الانتهاء بنجاح!");
            $this->info("📊 إجمالي اللترات المسحوبة: {$totalLitersDispensed} لتر");
            $this->info("📝 عدد العمليات المسجلة: {$transactionsCreated}");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ حدث خطأ: " . $e->getMessage());
            return 1;
        }
    }
}
