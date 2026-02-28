<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndexesSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('indexes')->insert([
            ['id_index' => 1, 'name' => 'Lainnya'],

            ['id_index' => 2, 'name' => 'SINTA 1'],
            ['id_index' => 3, 'name' => 'SINTA 2'],
            ['id_index' => 4, 'name' => 'SINTA 3'],
            ['id_index' => 5, 'name' => 'SINTA 4'],
            ['id_index' => 6, 'name' => 'SINTA 5'],
            ['id_index' => 7, 'name' => 'SINTA 6'],

            ['id_index' => 8, 'name' => 'Scopus Q1'],
            ['id_index' => 9, 'name' => 'Scopus Q2'],
            ['id_index' => 10, 'name' => 'Scopus Q3'],
            ['id_index' => 11, 'name' => 'Scopus Q4'],

            ['id_index' => 12, 'name' => 'Web of Science Q1'],
            ['id_index' => 13, 'name' => 'Web of Science Q2'],
            ['id_index' => 14, 'name' => 'Web of Science Q3'],
            ['id_index' => 15, 'name' => 'Web of Science Q4'],
            ['id_index' => 16, 'name' => 'Web of Science ESCI'],

            ['id_index' => 17, 'name' => 'DOAJ'],
            ['id_index' => 18, 'name' => 'Google Scholar'],
        ]);
    }
}
