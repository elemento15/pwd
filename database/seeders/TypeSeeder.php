<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;
use PhpParser\Node\Stmt\Foreach_;

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
            ['name' => 'Password', 'code' => 'PASS'],
            ['name' => 'Input', 'code' => 'STR'],
            ['name' => 'Number', 'code' => 'NUM'],
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
