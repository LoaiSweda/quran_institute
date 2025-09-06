<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Persent;
use Carbon\Carbon;

class PersentSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Persent::create([
                'date' => Carbon::now()->subDays(rand(1, 7))->toDateString(),
            ]);
        }
    }
}
