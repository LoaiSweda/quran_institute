<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('father_job')->nullable()->after('father_name');
            $table->string('mother_job')->nullable()->after('father_job');
            $table->string('school_name')->nullable()->after('mother_job');
            $table->enum('financial_status', ['ممتاز', 'متوسط', 'ضعيف'])->nullable()->after('school_name');
            $table->text('health_status')->nullable()->after('financial_status');
            $table->integer('memorized_parts')->default(0)->after('health_status');
            $table->boolean('has_sibling')->default(false)->after('memorized_parts');
            $table->integer('siblings_count')->default(0)->after('has_sibling');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'father_job',
                'mother_job',
                'school_name',
                'financial_status',
                'health_status',
                'memorized_parts',
                'has_sibling',
                'siblings_count'
            ]);
        });
    }
};
