<?php

namespace Database\Seeders;

use App\Models\Salesman;
use Illuminate\Database\Seeder;

class SalesmenSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        //csv header
        $header = [];

        //assuming .csv is in /public folder
        $salesmenCsv = fopen(public_path('salesmen.csv'), 'rb');

        while (($row = fgetcsv($salesmenCsv, null, ';')) !== false) {

            if (empty($header)) {
                $header = array_map('trim', $row);
            } else {
                $data = array_combine($header, $row);

                Salesman::create([
                    'first_name'     => $data['first_name'],
                    'last_name'      => $data['last_name'],
                    'titles_before'  => $data['titles_before'],
                    'titles_after'   => $data['titles_after'],
                    'prosight_id'    => $data['prosight_id'],
                    'email'          => $data['email'],
                    'phone'          => $data['phone'],
                    'gender'         => $data['gender'],
                    'marital_status' => $data['marital_status']
                ]);
            }
        }
    }
}
