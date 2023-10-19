import './bootstrap';
import "bootstrap";

import { createApp } from "vue/dist/vue.esm-bundler";
import CardList from "./Components/CardList.vue";
import PlayerStats from "./Components/PlayerStats.vue";
import Card from "./Components/Card.vue";
import Stack from "./Components/Stack.vue";
import GameLobby from "./Components/GameLobby.vue";
import LobbyList from "./Components/LobbyList.vue";
const app = createApp({});
app.component("card-list", CardList);
app.component("card", Card);
app.component("stack-pile", Stack);
app.component("player-stats", PlayerStats);
app.component("game-table", LobbyList);
app.component("game-lobby", GameLobby);
app.mount("#app");