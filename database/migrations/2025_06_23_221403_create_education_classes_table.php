    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up()
        {
            Schema::create('classes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('students_count')->default(0);
                //$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->integer('session_count')->default(0);
                $table->string('qr')->nullable();
                $table->float('present_percentage')->default(0);
                $table->timestamps();
            });
        }


        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('education_classes');
        }
    };
