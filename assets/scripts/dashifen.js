document.addEventListener('DOMContentLoaded', () => {
  const htmlClassList = document.getElementsByTagName('html')[0].classList;
  htmlClassList.remove('no-js');
  htmlClassList.add('js');

  // https://fontawesome.com/docs/web/use-with/vue/add-icons
  // https://blog.fontawesome.com/how-to-use-vue-js-with-font-awesome/
});
