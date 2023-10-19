<template>
  <div>
    <table class="table table-dark">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Name</th>
          <th scope="col">User ID</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(player, index) in lobby.players" :key="player.id">
          <td>{{ player.id }}</td>
          <td>{{ player.user ? player.user.name : '-' }}</td>
          <td>{{ player.user_id ? player.user_id : '-' }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
   data() {
    return {
      lobby: [],
      apiUrl: window.appUrl,
 
      csrf: document.head.querySelector('meta[name="csrf-token"]').content,
    };
  },
   props: {
    game: Number, // Define the gameId prop and specify its type
  },
   methods: {
  
    fetchCardData() {
      // Fetch initial card data from your Laravel backend
      axios
        .get(`${this.apiUrl}/games/${this.game}/data/lobby`)
        .then((response) => {
            
          this.lobby = response.data.lobby;
          console.log(response);
           this.$nextTick(() => {
            setGameListButtons();
            });
        

        });
       
        
    },
  },
   mounted() {
    this.fetchCardData();
    Echo.channel('games.lobby.'+this.game).listen('GameListUpdate', (data) => {
      this.games = data.game;
      this.$nextTick(() => {
            setGameListButtons();
            });

      
      // Add click event listeners to each checkbox
 
   
    });
   }
  
};
</script>