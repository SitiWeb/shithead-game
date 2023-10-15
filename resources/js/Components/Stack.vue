<template>
  <div>
   
      <div class="card text-center card-ratio">
        <div class="card-body p-0">
          <div v-if="visible"  class="card-number" >
            <span class="card-icon">{{ suitIcon(card.card_suit) }}</span>
            <span class="card-value">{{ rankValue(card.card_rank) }}</span>
          </div>
        </div>
      </div>

  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    game: {
      type: Number,
      required: true,
    },
    
  },
  data() {
    return {
        visible: true,
        card: {},
    };
  },
  mounted() {
    // Fetch initial card data
    this.fetchCardData();

    // Listen for updates from the WebSocket channel
    Echo.channel('game.' + this.game).listen('GameUpdate', (data) => {
      // Assuming data contains the new card information

      this.card = data.cards[0];
      this.loading = false; // Set loading to false when data is loaded
    });
  },
  methods: {
    suitIcon(suit) {
      switch (suit) {
        case 'Hearts':
          return '♡';
        case 'Spades':
          return '♤';
        case 'Clubs':
          return '♧';
        case 'Diamonds':
          return '♢';
        default:
          return '?';
      }
    },
   
    rankValue(rank) {
      switch (rank) {
        case 'Queen':
          return 'Q';
        case 'Ace':
          return 'A';
        case 'Jack':
          return 'J';
        case 'King':
          return 'K';
        default:
          return rank;
      }
    },
    fetchCardData() {
      // Fetch initial card data from your Laravel backend
      axios
        .get(`http://localhost/shithead-game/public/games/${this.game}/data`)
        .then((response) => {
           if (response.data.cards[0] != null){
            if (response.data.cards[0].card_type == 'pile'){
              this.card = response.data.cards[0];
              
            }else{
              this.card = [];
            }
            
           }else{

           }
          

          this.loading = false; // Set loading to false when data is loaded
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
          this.loading = false; // Set loading to false even on error
        });
    },
  },
  

  components: {
    // Card, // You don't need to import the Card component here unless it's used
  },
};
</script>
