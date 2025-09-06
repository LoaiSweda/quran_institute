<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Institute;
use App\Models\User;

class InstituteUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutes  = Institute::all();
        $managers    = User::where('role_id', 2)->pluck('id')->toArray();
        $supervisors = User::where('role_id', 3)->pluck('id')->toArray();

        foreach ($institutes as $institute) {
            if (empty($managers)) {
                continue;
            }

            $managerId = $managers[array_rand($managers)];
            DB::table('institute_user')->insert([
                'institute_id'    => $institute->id,
                'user_id'         => $managerId,
                'role_institute'  => 'manager', 
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            $shuffled = $supervisors;
            shuffle($shuffled);
            $take = min(2, count($shuffled));
            foreach (array_slice($shuffled, 0, $take) as $supId) {
                DB::table('institute_user')->insert([
                    'institute_id'    => $institute->id,
                    'user_id'         => $supId,
                    'role_institute'  => 'supervisor',   
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
    }
}
