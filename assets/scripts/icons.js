import { createApp } from 'vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faHouse, faHouseNight } from '@fortawesome/pro-solid-svg-icons';
import { default as Icons } from './components/icons.vue';

export default {
  initialize() {
    library.add(faHouse, faHouseNight);
    document.querySelectorAll('icon').forEach((element) => {
      createApp(Icons).mount(element);
    });
  }
}


