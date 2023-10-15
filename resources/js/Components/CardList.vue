<template>
  <div class="d-flex gap-1 card-holder  flex-wrap">
    <Card v-for="card in cardData" :key="card.id" :card="card" :visible="shouldDisplayCard" />
  </div>
</template>

<script>
import Card from './Card.vue'; // Adjust the import path as needed

export default {
  props: {
    player: {
      type: String, // Assuming player is a string, adjust the type if needed
      required: true,
    },
    type: {
      type: String, // Assuming type is a string, adjust the type if needed
      required: true,
    },
    game: {
      type: String, // Assuming type is a string, adjust the type if needed
      required: true,
    },
    shouldDisplayCard: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      cardData: [], // Initialize cardData as an empty array
      loading: true, // Add a loading indicator
    };
  },
  mounted() {
    // Fetch initial card data
    this.fetchCardData();
    
    // Listen for updates from the WebSocket channel
    Echo.channel('game.' + this.game).listen('GameUpdate', (data) => {
      this.cardData = data.players[this.player][this.type];
      this.loading = false; // Set loading to false when data is loaded
      // Add click event listeners to each checkbox
      set_events();
      handCards();
   
    });
  },
  methods: {
    fetchCardData() {
      // Fetch initial card data from your Laravel backend
      axios
        .get(`http://localhost/shithead-game/public/games/${this.game}/data`)
        .then((response) => {

          this.cardData = response.data.players[this.player][this.type];

          this.loading = false; // Set loading to false when data is loaded
          set_events();
          handCards();
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
          this.loading = false; // Set loading to false even on error
        });
    },
  },
  components: {
    Card,
  },
};
</script>