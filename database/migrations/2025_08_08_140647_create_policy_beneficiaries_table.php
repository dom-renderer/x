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
        Schema::create('policy_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('insured_life_id')->nullable();
            $table->unsignedBigInteger('policy_id')->nullable();
            $table->string('name')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->enum('status', ['single', 'married', 'divorced', 'separated'])->nullable();
            $table->enum('smoker_status', ['smoker', 'non-smoker'])->nullable();
            $table->double('beneficiary_death_benefit_allocation')->default(0);
            $table->enum('designation_of_beneficiary', ['revocable', 'irrevocable'])->default('revocable');
            $table->string('nationality')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('country_of_legal_residence')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('country_of_issuance')->nullable();
            $table->string('relationship_to_policyholder')->nullable();
            $table->string('email')->nullable();
            $table->boolean('in_draft')->default(0);
            $table->boolean('silent_save')->default(1);
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_beneficiaries');
    }
};
