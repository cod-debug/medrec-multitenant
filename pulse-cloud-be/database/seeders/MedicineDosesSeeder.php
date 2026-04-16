<?php

namespace Database\Seeders;

use App\Models\MedicineDose;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicineDosesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $medicine_doses = [
            "0.65%",
            "1 gm",
            "1 spray/nostril",
            "10 mg",
            "100 mcg",
            "100 mg",
            "100 mg/ml",
            "100/5",
            "10mg",
            "12.5 mg",
            "12.5mg/1gm",
            "120 mg",
            "125mg/5ml",
            "150 mg",
            "16mg",
            "1mg/0.74ml/3ml",
            "1spary/nostril once daily",
            "2 gtts",
            "2 spays/nostril",
            "2 sprays",
            "2.3%",
            "200/5",
            "200mg",
            "20mg",
            "228.5/5",
            "24 mg",
            "25 mcg",
            "25/250",
            "250 mcg",
            "250 mg",
            "250/5",
            "25mg",
            "3-4 gtts",
            "300mg",
            "30mg/5ml",
            "400mg",
            "40mg",
            "457/5",
            "4mg",
            "50 mcg",
            "50 mg",
            "500 mg",
            "50mg",
            "5mg",
            "60 mg",
            "600mg",
            "625mg",
            "642/5",
            "75 mg",
            "75/5",
            "750mg",
            "75mg",
            "8 mg",
            "800 mg",
            "90 mg",
            "cap",
            "cream",
            "dragee",
            "eye drops",
            "inhaler",
            "lozenge",
            "nasal spray",
            "nebule",
            "ointment",
            "oraI geI",
            "oral solution",
            "otic",
            "paste",
            "Pulmoneb soln",
            "sachet",
            "soln",
            "syrup",
            "tab",
            "throat spray"
        ];

        $existing_doses = MedicineDose::pluck('name')->toArray();

        // Filter out duplicates
        $new_doses = array_diff($medicine_doses, $existing_doses);

        // Insert only new doses
        if (!empty($new_doses)) {
            MedicineDose::insert(
                array_map(function ($name) {
                    return [
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $new_doses)
            );
        }
    }
}
