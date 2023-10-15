<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    // Define the table name if it's different from the model name
    protected $table = 'game_cards';

    // Define the fillable attributes (columns)
    protected $fillable = [
        'suit',
        'rank',
        'game_player_id',
        'card_rank',
        'card_suit',
        'card_type',
        'is_played',
        'played_at'
        // Add any other columns you need
    ];

    // Define a relationship if cards are associated with a deck
    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }

    
}
