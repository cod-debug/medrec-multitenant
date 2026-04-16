<?php

namespace Database\Seeders;

use App\Models\MedicinePreparation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicinePreparationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $medicine_preparations = ['bot', 'cap', 'MDI', 'nebule', 'pen', 'sachet', 'tab', 'tube'];

        $existing_preparations = MedicinePreparation::pluck('name')->toArray();

        $new_preparations = array_diff($medicine_preparations, $existing_preparations);

        if(!empty($new_preparations)){
            MedicinePreparation::insert(
                array_map(function ($name) {
                    return [
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $new_preparations)
            );
        }
    }
}
