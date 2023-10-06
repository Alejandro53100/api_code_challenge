<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ConsentIdType;

class ConsentIdTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConsentIdType::insert([
            ['type' => 'Card level'],
            ['type' => 'Account Level'],
            ['type' => 'Account Level'],
        ]
    );
    }
}
