<template>
  <div class="">
    
    <label :for="'card-' + card.id" class="animated-div">
    <input
      class="animate-checkbox"
      type="checkbox"
      :style="'width:0;opacity:0;'"
      :name="'card['+card.card_type+']'"  
      :value="card.id"        
      :id="'card-' + card.id" 
    />
      <div class="card text-center card-ratio">
        <div class="card-body p-0">
          <div class="card-number" v-if="visible">
            <span class="card-icon">{{ suitIcon }}</span>
            <span class="card-value">{{ rankValue }}</span>
          </div>
        </div>
      </div>
    </label>
  </div>
</template>


<script>
import axios from "axios";

export default {
  props: {
    card: {
      type: Object,
      required: true,
    },
    visible: {
      type: Boolean,
      required: true,
    },
    
  },
  computed: {

    suitIcon() {
      switch (this.card.card_suit) {
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
    rankValue() {
      switch (this.card.card_rank) {
        case 'Queen':
          return 'Q';
        case 'Ace':
          return 'A';
        case 'Jack':
          return 'J';
        case 'King':
          return 'K';
        default:
          return this.card.card_rank;
      }
    },
  },
};


</script>