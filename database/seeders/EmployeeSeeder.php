<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\EmployeeRole;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeeSeeder extends Seeder
{   
    private array $roles = ['coach', 'staff'];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // EmployeeRole::createOrFirst([
        //     'role_code' => 'coach',
        // ]);

        // EmployeeRole::createOrFirst([
        //     'role_code' => 'staff',
        // ]);

        Employee::createOrFirst([
            'user_id' => 1,
            'first_name' => 'コーチ',
            'last_name' => '監物',
            'employment_date' => Carbon::today(),
        ]);
    }
}
