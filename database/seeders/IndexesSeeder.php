<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndexesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('indexes')->truncate();

        DB::table('indexes')->insert([
            ['id' => 1, 'name' => 'Lainnya'],

            ['id' => 2, 'name' => 'SINTA 1'],
            ['id' => 3, 'name' => 'SINTA 2'],
            ['id' => 4, 'name' => 'SINTA 3'],
            ['id' => 5, 'name' => 'SINTA 4'],
            ['id' => 6, 'name' => 'SINTA 5'],
            ['id' => 7, 'name' => 'SINTA 6'],

            ['id' => 8, 'name' => 'Scopus Q1'],
            ['id' => 9, 'name' => 'Scopus Q2'],
            ['id' => 10, 'name' => 'Scopus Q3'],
            ['id' => 11, 'name' => 'Scopus Q4'],

            ['id' => 12, 'name' => 'Web of Science Q1'],
            ['id' => 13, 'name' => 'Web of Science Q2'],
            ['id' => 14, 'name' => 'Web of Science Q3'],
            ['id' => 15, 'name' => 'Web of Science Q4'],
            ['id' => 16, 'name' => 'Web of Science ESCI'],

            ['id' => 17, 'name' => 'DOAJ'],
            ['id' => 18, 'name' => 'Google Scholar'],
        ]);
    }
}
