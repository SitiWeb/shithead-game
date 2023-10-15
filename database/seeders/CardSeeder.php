<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
        $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King', 'Ace'];

        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                DB::table('cards')->insert([
                    'suit' => $suit,
                    'rank' => $rank,
                    // Add any other columns you need to populate
                ]);
            }
        }
    }
}
