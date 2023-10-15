<template>
<div class="d-flex gap-3 justify-content-center mt-4">
    <div class="">
        <i class="fa-solid fa-hand"></i>
        {{this.count_hand}}
    </div>
    <div class="">
        <i class="fa-solid fa-eye-slash"></i>
        {{this.count_closed}}
    </div>
</div>
</template>

<script>

export default {
  props: {
    player: {
      type: String, // Assuming player is a string, adjust the type if needed
      required: true,
    },
    type: {
      type: String, // Assuming type is a string, adjust the type if needed
      required: false,
    },
    game: {
      type: String, // Assuming type is a string, adjust the type if needed
      required: true,
    },
  },
  data() {
    return {
      cardData: [], // Initialize cardData as an empty array
      loading: true, // Add a loading indicator
      count_hand: 0,
      count_closed: 0,
      apiUrl: window.appUrl ,
    };
  },
  mounted() {
    // Fetch initial card data
    this.fetchCardData();
    
    // Listen for updates from the WebSocket channel
    Echo.channel('game.' + this.game).listen('GameUpdate', (data) => {
      var cardData = data.players[this.player];
   if (typeof cardData['hand'] !== 'undefined') {
        this.count_hand = cardData['hand'].length;
    } else {
        this.count_hand = 0;
    }
    if (typeof cardData['closed'] !== 'undefined') {
        this.count_closed = cardData['closed'].length;
    } else {
        this.count_closed = 0;
    }

    
    console.log(jQuery('#check-'+ this.player).hide());
      set_events();
      makeItemsDraggable();
   
    });
  },
  methods: {
    fetchCardData() {
      // Fetch initial card data from your Laravel backend
      axios
        .get(`${this.apiUrl}/games/${this.game}/data`)
        .then((response) => {
  
    var cardData = response.data.players[this.player];
          // Create an empty object to store card type counts
     if (typeof cardData['hand'] !== 'undefined') {
        this.count_hand = cardData['hand'].length;
    } else {
        this.count_hand = 0;
    }
    if (typeof cardData['closed'] !== 'undefined') {
        this.count_closed = cardData['closed'].length;
    } else {
        this.count_closed = 0;
    }
console.log(jQuery('#check-'+ this.player));
          this.loading = false; // Set loading to false when data is loaded
          set_events();
          setTimeout(() => {
            makeItemsDraggable();
          }, 200);
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
          this.loading = false; // Set loading to false even on error
        });
    },
  }

};
</script>