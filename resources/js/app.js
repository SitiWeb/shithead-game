import './bootstrap';
import "bootstrap";

import { createApp } from "vue/dist/vue.esm-bundler";
import ExampleCounter from "./components/ExampleCounter.vue";
import CardList from "./components/CardList.vue";
import Card from "./components/Card.vue";
import Stack from "./components/Stack.vue";
const app = createApp({});
app.component("example-counter", ExampleCounter);
app.component("card-list", CardList);
app.component("card", Card);
app.component("stack", Stack);
app.mount("#app");