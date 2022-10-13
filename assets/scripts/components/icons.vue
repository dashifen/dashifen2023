<script>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

export default {
  name: "FontAwesomeIcon",
  components: [FontAwesomeIcon],
  props: ["set", "icon"],
}
</script>

<template>
  <font-awesome-icon :icon="[set, icon]"></font-awesome-icon>
</template>
