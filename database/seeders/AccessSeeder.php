<?php

namespace Database\Seeders;

use App\Models\TypeUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        TypeUser::find(1)->setAccess(1, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]);
        TypeUser::find(2)->setAccess(2, [1, 2]);
    }
}
