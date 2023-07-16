<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\DB;

class VariantSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('variant_sizes')->delete();
        if (Env::get('DB_CONNECTION') == 'mysql')
        {
            DB::statement('ALTER TABLE variant_sizes AUTO_INCREMENT = 1');
        } elseif (Env::get('DB_CONNECTION') == 'pgsql')
        {
            DB::statement('ALTER SEQUENCE variant_sizes_id_seq RESTART WITH 1');
        } elseif (Env::get('DB_CONNECTION') == 'sqlite')
        {
            DB::insert('insert into sqlite_sequence (name, seq) values (?, ?)', ['variant_sizes', 1]);
        }
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        foreach ($sizes as $size)
        {
            DB::table('variant_sizes')->insert([
                'name' => $size,
            ]);
        }
    }
}
