const navbar = document.querySelector('#navbar');
const burger = document.getElementById('burger');

burger.addEventListener('click', () => {
    navbar.classList.toggle('open');
});
