import './bootstrap';
import "bootstrap";

import { createApp } from "vue/dist/vue.esm-bundler";
import CardList from "./Components/CardList.vue";
import PlayerStats from "./Components/PlayerStats.vue";
import Card from "./Components/Card.vue";
import Stack from "./Components/Stack.vue";
// import LobbyList from "./Components/LobbyList.vue";
const app = createApp({});
app.component("card-list", CardList);
app.component("card", Card);
app.component("stack-pile", Stack);
app.component("player-stats", PlayerStats);
// app.component("lobby-list", LobbyList);
app.mount("#app");

$(document).ready(function() {
    $("#joinGameForm").on("submit", function(event) {
        event.preventDefault(); // Prevent the default form submission.

        $.ajax({
            type: "POST",
            url: $(this).attr("action"), // Get the form's action URL.
            data: $(this).serialize(), // Serialize the form data.
            success: function(response) {
                if (response.status == 'success') {
                    showToast(response.message, 'success', { delay: 3000 });
                } else if (response.status == 'error') {
                    showToast(response.message, 'error', { delay: 3000 });
                } else {
                    console.log(response);
                    showToast('Unknown response', 'error', { delay: 3000 });
                }
                // You can update the UI or perform other actions as needed.
            },
            error: function(xhr, status, error) {

                showToast('Unknown response', 'error', { delay: 3000 });
                console.error(xhr.responseText);
            }
        });
    });
});


$(document).ready(function() {
    $("#createLobbyForm").on("submit", function(event) {
        event.preventDefault(); // Prevent the default form submission.

        $.ajax({
            type: "POST",
            url: $(this).attr("action"), // Get the form's action URL.
            data: $(this).serialize(), // Serialize the form data.
            success: function(response) {
                if (response.status == 'success') {
                    showToast(response.message, 'success', { delay: 3000 });
                } else if (response.status == 'error') {
                    showToast(response.message, 'error', { delay: 3000 });
                } else {
                    console.log(response);
                    showToast('Unknown response', 'error', { delay: 3000 });
                }
                // You can update the UI or perform other actions as needed.
            },
            error: function(xhr, status, error) {

                showToast('Unknown response', 'error', { delay: 3000 });
                console.error(xhr.responseText);
            }
        });
    });
});

$(document).ready(function() {
    $("#leaveGameForm").on("submit", function(e) {
        e.preventDefault(); // Prevent the default form submission

        $.ajax({
            type: 'POST',
            url: $(this).attr("action"), // Get the form's action URL.
            data: $(this).serialize(), // Serialize the form data.
            success: function(response) {
                if (response.status == 'success') {
                    showToast(response.message, 'success', { delay: 3000 });
                } else if (response.status == 'error') {
                    showToast(response.message, 'error', { delay: 3000 });
                } else {
                    console.log(response);
                    showToast('Unknown response', 'error', { delay: 3000 });
                }
                // You can update the UI or perform other actions as needed.
            },
            error: function(xhr, status, error) {

                showToast('Unknown response', 'error', { delay: 3000 });
                console.error(xhr.responseText);
            }
        });
    });
});