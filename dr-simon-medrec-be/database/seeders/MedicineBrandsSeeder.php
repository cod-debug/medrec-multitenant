<?php

namespace Database\Seeders;

use App\Models\MedicineBrand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicineBrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $medicine_brands = [
            "1-6-12",
            "Acifre",
            "Actranex",
            "Advil",
            "Aerosporex",
            "Algesia",
            "Allerkast",
            "Allerkid",
            "Allerzet",
            "Alnix",
            "Amoclav",
            "Ampimax",
            "Aplosyn",
            "Appebon + Iron",
            "Aprovel",
            "Arcoxia",
            "Avamys",
            "Azithro-Natrapharm",
            "Bactiv",
            "Bactroban",
            "Betnovate",
            "Bilaxten",
            "Bonamine",
            "Breecort",
            "Calpol 6+",
            "Calvit",
            "Candibec",
            "Candiva",
            "Cefisal-200",
            "Cefuclav",
            "Cefurex",
            "Centrum",
            "Ceporex",
            "Cetadol",
            "Cipro + Dexa",
            "Ciprobay",
            "Ciprofloxacin",
            "Clarithrocid",
            "Clindal",
            "Cloxacillin",
            "Co Aleva",
            "Combiderm",
            "Coxidia",
            "Dalacin C",
            "Dermatix",
            "Dermovate",
            "Dexamon",
            "Difflam",
            "Diflucan",
            "Dimezine",
            "Dolan",
            "Dolcet",
            "Doxin",
            "Duavent",
            "Elica",
            "Essprin",
            "Euthyrox",
            "Exflem",
            "Exigo",
            "Extusive",
            "Faspic",
            "Flo Sinus Care",
            "Fluimucil",
            "Forexine",
            "Foskina - B",
            "Hemarate",
            "Hemostan",
            "Herpex",
            "Himox",
            "Humer - Hypertonic",
            "Humer - Isotonic",
            "Jardiance Dou",
            "Kamillosan",
            "Ketesse",
            "Klaz OD",
            "Klindex",
            "Loxeva",
            "Lyrica",
            "Medrol",
            "Mepraz",
            "Mepresone",
            "Montemax",
            "Mosaspray",
            "Myelax",
            "Nafarin-A",
            "Nasoclear",
            "Nasoflo",
            "Nasonex",
            "Natravox",
            "Norgesic",
            "Norprexia",
            "Norprexia Forte",
            "Nutruvox",
            "Orthroat",
            "Otobiotic",
            "Otofungic",
            "Otoquad",
            "Overt",
            "Ozempic",
            "Peldacyn",
            "PND",
            "Pneumotyl",
            "Pneumozith",
            "Ponstan SF",
            "Pred 10",
            "Pred 20",
            "Prolix",
            "Pyralvex",
            "Robitussin DM",
            "Rosalta",
            "RowageI",
            "Sebclair",
            "Serc",
            "Seretide",
            "Silgram",
            "Sinecod Forte",
            "Sinupret Forte",
            "Skudexa",
            "Sniff",
            "Solcoseryl",
            "Stafloxin",
            "Stelix",
            "Stugeron",
            "Synactiv",
            "Syntemax",
            "Tears Naturale",
            "Tebonin Forte",
            "Tegretol",
            "Tergecef",
            "Thyrax",
            "Tobradex",
            "Tobrex",
            "Trimycin",
            "Trimycin H",
            "Triocef",
            "Ultima",
            "Vessel Due - F",
            "Virest",
            "Virlix",
            "Xyzal",
            "Zenith",
            "Zinnat",
            "Zithromax",
            "Zobrixol",
            "Zovirax",
            "Zykast",
            "Zylovir",
            "Zymocort"
        ];

        $existing_names = MedicineBrand::pluck('name')->toArray();

        // Filter out duplicates
        $new_brands = array_diff($medicine_brands, $existing_names);
        
        // Insert only new brands
        if (!empty($new_brands)) {
            MedicineBrand::insert(
                array_map(function ($name) {
                    return [
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $new_brands)
            );
        }
    }
}
