<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PahaniMasterSeeder extends Seeder
{
    // ── Village master data ────────────────────────────────────────────────────
    private array $villageData = [
        'Hayathnagar'   => ['Hayathnagar','Saroornagar','Vanasthalipuram','Bandlaguda Jagir','Pedda Amberpet'],
        'Ibrahimpatnam' => ['Ibrahimpatnam','Manchal','Pedda Gollagudem','Yacharam','Turkapally'],
        'Keesara'       => ['Keesara','Cherlapally','Kushaiguda','Ghatkesar','Boduppal'],
        'Malkajgiri'    => ['Malkajgiri','Uppal','Kapra','Neredmet','Medipally'],
        'Medchal'       => ['Medchal','Kompally','Nagaram','Bollaram','Dundigal'],
        'Rajendranagar' => ['Rajendranagar','Budwel','Jilakarragudem','Gandipet','Narsingi'],
        'Shamshabad'    => ['Shamshabad','Kothur','Farooqnagar','Chevella Road','Shadnagar'],
        'Shankarpally'  => ['Shankarpally','Mokila','Ameenpur','Sultanpur','Gummadidala'],
        'Chevella'      => ['Chevella','Marpally','Pudur','Vikarabad','Donthanpally'],
        'Vikarabad'     => ['Vikarabad','Tandur','Kodangal','Marpally','Nawabpet'],
        'Sangareddy'    => [
            'Arutla','Byathole','Cheriyal','Chidruppa','Chintalpalle','Edthanur',
            'Fasalwadi','Indrakaran','Irigipalle','Ismailkhanpet','Julkal','Kalabgoor',
            'Kalvemula','Kandi','Kasipur','Kothlapur','Koulampet','Makthaalloor',
            'Mamidipalle','Mohd.Shapur','Nagapur','Tadlapalle','Topgonda','Utharpalle',
        ],
    ];

    public function run(): void
    {
        // ── 1. Mandals & Villages ──────────────────────────────────────────────
        foreach ($this->villageData as $mandalName => $villages) {
            $mandalId = DB::table('mandals')->insertGetId([
                'name'       => $mandalName,
                'slug'       => Str::slug($mandalName),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $villageRows = array_map(fn($v) => [
                'mandal_id'  => $mandalId,
                'name'       => $v,
                'slug'       => Str::slug($v),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], $villages);

            DB::table('villages')->insert($villageRows);
        }

        // ── 2. Core pahani document types ─────────────────────────────────────
        $coreDocuments = [
            [
                'value'       => 'sethwar',
                'label'       => 'Sethwar Pahani',
                'type'        => 'core',
                'description' => 'Survey Wise Area Statement (పట్టా పహాణి)',
                'sort_order'  => 1,
            ],
            [
                'value'       => 'kasra',
                'label'       => 'Kasra Pahani',
                'type'        => 'core',
                'description' => 'Crop Survey Register (కాసరా పహాణి)',
                'sort_order'  => 2,
            ],
            [
                'value'       => 'sessala',
                'label'       => 'Sessala Pahani',
                'type'        => 'core',
                'description' => 'Detailed Field Measurement Record (సెస్సాల పహాణి)',
                'sort_order'  => 3,
            ],
        ];

        // ── 3. Year-wise pahani document types (1960-61 to 2024-25) ──────────
        $yearDocuments = [];
        $sort = 4;
        for ($y = 1960; $y <= 2024; $y++) {
            $next = $y + 1;
            $yearDocuments[] = [
                'value'       => 'yr_' . $y,
                'label'       => $y . '-' . substr($next, -2),   // e.g. "1960-61"
                'type'        => 'year',
                'description' => null,
                'sort_order'  => $sort++,
            ];
        }

        $allDocuments = array_map(fn($d) => array_merge($d, [
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]), array_merge($coreDocuments, $yearDocuments));

        // Insert in chunks to avoid DB packet size limits
        foreach (array_chunk($allDocuments, 50) as $chunk) {
            DB::table('pahani_documents')->insert($chunk);
        }
    }
}
