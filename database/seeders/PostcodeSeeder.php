<?php

namespace Database\Seeders;

use App\Models\Postcode;
use Illuminate\Database\Seeder;

class PostcodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('migrations/source/data/csv/postcodes.csv');

        if (!file_exists($csvPath)) {
            $this->command->error('Postcode CSV file not found at: ' . $csvPath);
            return;
        }

        $file = fopen($csvPath, 'r');
        $headers = fgetcsv($file);

        $postcodes = [];
        while (($row = fgetcsv($file)) !== false) {
            $postcodes[] = [
                'postcode' => $row[0],
                'city' => $row[1],
                'state' => $row[2],
                'state_code' => $row[3],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($file);

        Postcode::insert($postcodes);

        $this->command->info('Seeded ' . count($postcodes) . ' postcodes into the database.');
    }
}
