<?php

namespace Database\Seeders;

use App\Models\GenericMedicine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenericMedicinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $generic_medicines = [
            "Acetylcisteine",
            "Aciclovir",
            "allerzet",
            "Ambroxol",
            "Amoxicillin",
            "Anthraquinone",
            "Azithromycin",
            "Bacitracin",
            "Beclomethasone",
            "Beclomethasone + Ofloxacin",
            "Benzydamine",
            "Betahistine",
            "Betamethasone",
            "Bilastine",
            "Budesonide",
            "Butamirate",
            "Ca Carbonate",
            "Carbamazepine",
            "CarbenoxoIone Na",
            "Cefalexin",
            "Cefixime",
            "Cefuroxime",
            "Cefuroxime + Clavulanic",
            "Celecoxib",
            "Ceterizine",
            "cetirizine",
            "Chamomille",
            "Chlorhexidine",
            "Cinnarizine",
            "Cipro + Dexa",
            "Ciprofloxacin",
            "Clarithromycin",
            "Clindamycin",
            "Clobetasol",
            "Clotrimazole",
            "Cloxacillin",
            "Co amoxiclav",
            "Co-amoxidav",
            "Dermatix",
            "Dexketoprofen",
            "Dextran",
            "Dextromethorphan",
            "Doxycycline",
            "Ebastine",
            "Empaglifozin, metformin",
            "Eperisone",
            "Eterocoxib",
            "Floucinolone",
            "Flucloxacillin",
            "Fluconazole",
            "Fluticasone",
            "Fluticusoue",
            "Gingko biloba",
            "Ibuprofen",
            "Ipratropium+Salbutamol",
            "Irbesartan",
            "Levocetirizine",
            "Levocetirizine + Betamethasone",
            "Levocetirizine + Montelukast",
            "Levodropropizine",
            "Levofloxacin",
            "Levothyroxine",
            "Meclizine",
            "Mefenamic Acid",
            "Methylprednisolone",
            "Mometasone",
            "Montelukast",
            "Multivatamins + Fe",
            "Multivitamins",
            "Mupirocin",
            "NaCl",
            "Omeprazole",
            "Orphenadrine + Paracetamol",
            "otofungic",
            "Otoquad",
            "Paracetamol",
            "Phenypropanolamine",
            "Polidocanol",
            "Polymyxin",
            "Potassium Chloride",
            "Prednisone",
            "Pregabalin",
            "Propolis Extract",
            "Rosuvastatin",
            "Sambucus",
            "Sea water",
            "Sebclair",
            "Semaglutide",
            "Sulodexide",
            "Sultamicillin",
            "Tobramycin",
            "Tramadol",
            "Tranexamic Acid",
            "Vit B complex"
        ];

        // Get existing medicine names to avoid duplicates
        $existing_names = GenericMedicine::pluck('name')->toArray();
        
        // Filter out duplicates
        $new_medicines = array_diff($generic_medicines, $existing_names);
        
        // Insert only new medicines
        if (!empty($new_medicines)) {
            GenericMedicine::insert(
                array_map(function ($name) {
                    return [
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $new_medicines)
            );
        }
    }
}
