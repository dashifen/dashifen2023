import { createApp } from 'vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faHouse, faHouseNight } from '@fortawesome/pro-solid-svg-icons';

export default {
  initialize() {
    library.add(faHouse, faHouseNight);
    createApp(FontAwesomeIcon).mount('font-awesome-icon');
  }
}


