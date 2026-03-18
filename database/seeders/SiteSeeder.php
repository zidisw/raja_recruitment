<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        $sites = [
            ['name' => 'Site Morowali', 'location' => 'Morowali, Sulawesi Tengah', 'description' => 'Nickel mining site in Central Sulawesi.'],
            ['name' => 'Site Kolaka', 'location' => 'Kolaka, Sulawesi Tenggara', 'description' => 'Mining and hauling operations in Southeast Sulawesi.'],
            ['name' => 'Site Konawe', 'location' => 'Konawe, Sulawesi Tenggara', 'description' => 'Nickel ore mining and processing site.'],
            ['name' => 'Kantor Pusat Makassar', 'location' => 'Makassar, Sulawesi Selatan', 'description' => 'Head office and administrative center.'],
            ['name' => 'Site Luwu Timur', 'location' => 'Luwu Timur, Sulawesi Selatan', 'description' => 'Construction and land clearing operations.'],
        ];

        foreach ($sites as $site) {
            Site::firstOrCreate(['name' => $site['name']], $site);
        }
    }
}
