
const bar = document.getElementById('bar');
const close = document.getElementById('close');
const nav = document.getElementById('navbar');

if (bar) {
    bar.addEventListener('click', () => {
        nav.classList.add('active');
    })
}

if (close) {
    close.addEventListener('click', () => {
        nav.classList.remove('active');
    })
}


// open login form
const formOnpenBtn = document.querySelector("#form_open");
const mobileFormOpenBtn = document.querySelector("#mobile_form_open");
const formLogin = document.querySelector(".login");
const formContainer = document.querySelector(".form_container");
const formCloseBtn = document.querySelector(".form_close");
const loginBtn = document.querySelector("#signup");
const pwShowHide = document.querySelectorAll(".password_hide");

formOnpenBtn.addEventListener("click", () => formLogin.classList.add("show"));
mobileFormOpenBtn.addEventListener("click", () => formLogin.classList.add("show"));
formCloseBtn.addEventListener("click", () => formLogin.classList.remove("show"));

pwShowHide.forEach((icon) => {
    icon.addEventListener("click", () => {
        let getPwInput = icon.parentElement.querySelector("input");
        if (getPwInput.type === "password") {
            getPwInput.type = "text";
            icon.classList.replace("fa-eye-slash", "fa-eye")
        } else {
            getPwInput.type = "password";
            icon.classList.replace("fa-eye", "fa-eye-slash")
        }
    })
})