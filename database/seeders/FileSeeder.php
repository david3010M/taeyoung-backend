<?php

namespace Database\Seeders;

use App\Models\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 50) as $id) {
            File::factory()->create([
                'spare_part_id' => $id,
            ]);
        }

    }
}
