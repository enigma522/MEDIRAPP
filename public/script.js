

const optionMenu = document.querySelector(".options");
const selectBtn = document.querySelector(".select-btn");
const options = document.querySelectorAll("ul#menu li");
const sBtn_text = document.querySelector(".sBtn-text");

selectBtn.addEventListener("click",() =>{
    optionMenu.classList.toggle("disable");
})
