<?php

namespace Database\Seeders;

use App\Models\TypeUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessSeeder extends Seeder
{
    public function run()
    {
        TypeUser::find(1)->setAccess(1, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]);
        TypeUser::find(2)->setAccess(2, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
    }
}
