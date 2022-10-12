import { default as Icons } from './icons.js';

document.addEventListener('DOMContentLoaded', () => {
  const htmlClassList = document.getElementsByTagName('html')[0].classList;
  htmlClassList.remove('no-js');
  htmlClassList.add('js');
  Icons.initialize();
});

