<template>
  <div class="d-flex gap-1 flex-wrap flex-row-reverse animation-container">
    <Card v-for="card in cards" :key="card.id" :card="card" :visible="true" />
  </div>
</template>

<script>
import axios from "axios";
import Card from './Card.vue'; // Adjust the import path as needed
export default {
  props: {
    game: {
      type: String,
      required: true,
    },
    
  },
  data() {
    return {
        visible: true,
        card: {},
        cards: {},
        apiUrl: window.appUrl ,
    };
  },
  mounted() {
    // Fetch initial card data
    this.fetchCardData();

    // Listen for updates from the WebSocket channel
    Echo.channel('game.' + this.game).listen('GameUpdate', (data) => {
      // Assuming data contains the new card information

             
        // Set up a callback function to run when the animation is complete
            this.setCards(data);
             var tl = this.playCardsAnimation();
           
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

    setCards(data){
      this.card = data.cards[0];
            // Use the filter method to keep only rows with card type "pile"
            var filteredCards =  data.cards.filter(function(card) {
                return card.card_type === "pile";
            });
            var limitedCards = filteredCards.slice(0, 4);
            this.cards = limitedCards;

        
            this.loading = false; // Set loading to false when data is loaded
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
    playCardsAnimation(){
      var tl = gsap.timeline();
      var container = jQuery(".animation-container");
      var cardCount = container.children.length;
      
      if (cardCount > 0){
        container.width = container.children().eq(0).width() * 4;
          tl.to(".animation-container .animation-box", {
              duration: 1,
              x: '-'+container.children().eq(0).width(),
              stagger: 0.2,
        });

      }
      return tl;
  },
    fetchCardData() {
      // Fetch initial card data from your Laravel backend
      axios
        .get(`${this.apiUrl}/games/${this.game}/data`)
        .then((response) => {


          this.card = response.data.cards[0];        
          var filteredCards =  response.data.cards.filter(function(card) {
            return card.card_type === "pile";
          });
          
          
          var limitedCards = filteredCards.slice(0, 4);
          this.cards = filteredCards;
          this.loading = false; // Set loading to false when data is loaded
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
          this.loading = false; // Set loading to false even on error
        });
    },
  },
  

  components: {
     Card, // You don't need to import the Card component here unless it's used
  },
};
</script>
