<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'Password', 'code' => 'password'],
            ['name' => 'Texto', 'code' => 'text'],
            ['name' => 'Numero', 'code' => 'number'],
        ];

        if (Type::count() == 0) {
            foreach($types as $item) {
                Type::create([
                    'name' => $item['name'],
                    'code' => $item['code'],
                ]);
            }
        }
    }
}
