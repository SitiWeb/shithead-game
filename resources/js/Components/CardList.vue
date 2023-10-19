<template>
  <div class="d-flex gap-1 card-holder justify-content-center flex-wrap"  :class="cardType">
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
      apiUrl: window.appUrl ,
      cardType: '',
    };
  },
  mounted() {
    // Fetch initial card data
    this.fetchCardData();
    
    // Listen for updates from the WebSocket channel
    Echo.channel('game.' + this.game).listen('GameUpdate', (data) => {
      console.log(data)
      this.cardData = data.players[this.player][this.type];
      this.cardType = this.type
      this.loading = false; // Set loading to false when data is loaded
      
      // Add click event listeners to each checkbox
    this.$nextTick((response) => {
             set_events();
          handCards();
          handleCheck(data.game);
            });
      
   
    });
  },
  methods: {
    fetchCardData() {
      // Fetch initial card data from your Laravel backend
      axios
        .get(`${this.apiUrl}/games/${this.game}/data`)
        .then((response) => {

          this.cardData = response.data.players[this.player][this.type];
        this.cardType = this.type
          this.loading = false; // Set loading to false when data is loaded
          
               // Add click event listeners to each checkbox
    this.$nextTick(() => {
             set_events();
          handCards();
          handleCheck(response.data.game);
            });
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