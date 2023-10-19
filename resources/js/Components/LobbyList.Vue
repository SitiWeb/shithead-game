<template>
  <table class="table table-dark">
    <thead>
      <tr>
        <th scope="col"></th>
        <th scope="col">Name</th>
        <th scope="col">Made by</th>
        <th scope="col">Num of players</th>
        <th scope="col">Join</th>
        <th scope="col">Lobby</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="game in games" :key="game.id">
        <th scope="row">{{ game.id }}</th>
        <td>{{ game.name }}</td>
        <td>{{ game.created_by }}</td>
        <td>{{ game.playersCount }}</td>
        <td>
          <form method="post" :action="apiUrl+'/games/'+game.id+'/join'" class="form-with-ajax" id="joinGameForm" >
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="game" :value="game.id" />
            <button class="btn btn-primary" type="submit">Join game</button>
          </form>
        </td>
        <td>
          <a class="btn btn-secondary" :href="apiUrl+'/games/' + game.id+'/lobby/'">Lobby</a>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: {

  },
  mounted() {
    this.fetchCardData();
    Echo.channel('games').listen('GameListUpdate', (data) => {
      this.games = data.game;
      this.$nextTick(() => {
            setGameListButtons();
            });

      
      // Add click event listeners to each checkbox
 
   
    });
  },
  methods: {
  
    fetchCardData() {
      // Fetch initial card data from your Laravel backend
      axios
        .get(`${this.dataUrl}`)
        .then((response) => {

          this.games = response.data.lobby;
           this.$nextTick(() => {
            setGameListButtons();
            });
        

        });
       
        
    },
  },
  data() {
    return {
      games: [], // Initialize cardData as an empty array     
      apiUrl: window.appUrl,
      dataUrl: window.appUrl+'/games/data' ,
      csrf: document.head.querySelector('meta[name="csrf-token"]').content,
    };
  },
   
};
</script>
