<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Name fields
            $table->string('prefix', 20)->nullable()->after('phone');
            $table->string('first_name', 100)->nullable()->after('prefix');
            $table->string('last_name', 100)->nullable()->after('first_name');
            $table->string('first_name_en', 100)->nullable()->after('last_name');
            $table->string('last_name_en', 100)->nullable()->after('first_name_en');

            // ID and Birth
            $table->string('id_card', 13)->nullable()->unique()->after('last_name_en');
            $table->date('birth_date')->nullable()->after('date_of_birth');

            // Personal info
            $table->string('blood_group', 5)->nullable()->after('gender');
            $table->string('line_id', 100)->nullable()->after('email');

            // Address fields
            $table->string('subdistrict', 100)->nullable()->after('address');
            $table->string('district', 100)->nullable()->after('subdistrict');
            $table->string('province', 100)->nullable()->after('district');

            // Medical history
            $table->text('chronic_diseases')->nullable()->after('notes');
            $table->text('drug_allergy')->nullable()->after('chronic_diseases');
            $table->text('food_allergy')->nullable()->after('drug_allergy');
            $table->text('surgery_history')->nullable()->after('food_allergy');
            $table->text('chief_complaint')->nullable()->after('surgery_history');

            // Insurance
            $table->string('insurance_type', 50)->nullable()->after('chief_complaint');
            $table->string('insurance_number', 100)->nullable()->after('insurance_type');

            // Misc
            $table->string('booking_channel', 50)->nullable()->after('insurance_number');
            $table->string('photo')->nullable()->after('booking_channel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'prefix', 'first_name', 'last_name', 'first_name_en', 'last_name_en',
                'id_card', 'birth_date', 'blood_group', 'line_id',
                'subdistrict', 'district', 'province',
                'chronic_diseases', 'drug_allergy', 'food_allergy', 'surgery_history', 'chief_complaint',
                'insurance_type', 'insurance_number', 'booking_channel', 'photo'
            ]);
        });
    }
};
