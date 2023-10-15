import './bootstrap';
import "bootstrap";

import { createApp } from "vue/dist/vue.esm-bundler";
import ExampleCounter from "./Components/ExampleCounter.vue";
import CardList from "./Components/CardList.vue";
import Card from "./Components/Card.vue";
import Stack from "./Components/Stack.vue";
const app = createApp({});
app.component("example-counter", ExampleCounter);
app.component("card-list", CardList);
app.component("card", Card);
app.component("stack", Stack);
app.mount("#app");