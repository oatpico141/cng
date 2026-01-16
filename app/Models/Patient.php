<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The "booted" method of the model.
     * Apply BranchScope to ensure data isolation by branch
     *
     * NOTE: Patients are now BRANCH-LOCALIZED (not global)
     * Use withoutGlobalScope(BranchScope::class) for cross-branch search
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        // Automatically set branch_id when creating new Patient
        static::creating(function (Patient $patient) {
            if (!$patient->branch_id) {
                // Use first_visit_branch_id as branch_id if set, otherwise use session
                $patient->branch_id = $patient->first_visit_branch_id ?? session('selected_branch_id');
            }
        });
    }

    protected $fillable = [
        'is_temporary',
        'hn_number',
        'converted_at',
        'phone',
        'name',
        'prefix',
        'first_name',
        'last_name',
        'first_name_en',
        'last_name_en',
        'id_card',
        'birth_date',
        'email',
        'date_of_birth',
        'age',
        'gender',
        'blood_group',
        'address',
        'occupation',
        'subdistrict',
        'district',
        'province',
        'line_id',
        'chronic_diseases',
        'drug_allergy',
        'food_allergy',
        'surgery_history',
        'chief_complaint',
        'insurance_type',
        'insurance_number',
        'booking_channel',
        'photo',
        'emergency_contact',
        'emergency_name',
        'notes',
        'branch_id',
        'first_visit_branch_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'birth_date' => 'date',
        'is_temporary' => 'boolean',
        'converted_at' => 'datetime',
    ];

    // Accessors for computed attributes
    public function getHnAttribute()
    {
        // Use hn_number if exists, otherwise generate from ID
        if ($this->hn_number) {
            return $this->hn_number;
        }
        return 'HN' . str_pad(substr($this->id, 0, 6), 6, '0', STR_PAD_LEFT);
    }

    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->age;
    }

    public function getLastVisitAttribute()
    {
        // Get most recent OPD record or appointment
        $lastOpd = $this->opdRecords()->latest('created_at')->first();
        $lastAppointment = $this->appointments()->latest('appointment_date')->first();

        if ($lastOpd && $lastAppointment) {
            return max($lastOpd->created_at, $lastAppointment->appointment_date);
        }

        return $lastOpd?->created_at ?? $lastAppointment?->appointment_date;
    }

    public function getStatusAttribute()
    {
        // Patient is active if they have any records created in last 12 months
        if ($this->created_at && $this->created_at->diffInMonths(now()) > 12) {
            $hasRecentActivity = $this->opdRecords()
                ->where('created_at', '>=', now()->subMonths(12))
                ->exists();

            return $hasRecentActivity ? 'active' : 'inactive';
        }

        return 'active'; // New patients are active by default
    }

    public function getTotalVisitsAttribute()
    {
        // Count actual appointments that were completed
        return $this->appointments()
            ->where('status', 'completed')
            ->count();
    }

    public function getTotalSpentAttribute()
    {
        // Sum of all paid invoices
        return $this->invoices()
            ->where('status', 'paid')
            ->sum('total_amount') ?? 0;
    }

    public function getNextAppointmentAttribute()
    {
        // Get future appointments
        return $this->appointments()
            ->where('appointment_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->first();
    }

    // Relationships
    public function firstVisitBranch()
    {
        return $this->belongsTo(Branch::class, 'first_visit_branch_id');
    }

    public function opdRecords()
    {
        return $this->hasMany(OpdRecord::class);
    }

    public function patientNotes()
    {
        return $this->hasMany(PatientNote::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function coursePurchases()
    {
        return $this->hasMany(CoursePurchase::class);
    }

    public function loyaltyPoints()
    {
        return $this->hasOne(LoyaltyPoint::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Course Sharing - Courses owned by this patient that are shared with others
    public function sharedCoursesOwned()
    {
        return $this->hasMany(CourseSharedUser::class, 'owner_patient_id');
    }

    // Course Sharing - Courses shared with this patient by others
    public function sharedCoursesReceived()
    {
        return $this->hasMany(CourseSharedUser::class, 'shared_patient_id');
    }

}
