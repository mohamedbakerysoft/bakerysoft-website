import { $, jQuery } from "jquery";
let navbar = document.getElementById("main-nav");
let navPos = navbar.getBoundingClientRect().top;

window.addEventListener("scroll", (e) => {
  let scrollPos = window.scrollY;
  if (scrollPos > navPos) {
    navbar.classList.add("sticky");
  } else {
    navbar.classList.remove("sticky");
  }
});

// Features section
var tabLink = document.querySelectorAll(".feature-content-wrap .nav-link");
var tabPane = document.querySelectorAll(".feature-tab-content .tab-pane");
for (let i = 0; i < tabLink.length; i++) {
  tabLink[i].onclick = function (e) {
    e.preventDefault();
    for (let x = 0; x < tabLink.length; x++) {
      tabPane[x].classList.remove("active");
      tabLink[x].classList.remove("active");
    }
    tabLink[i].classList.add("active");
    tabPane[i].classList.add("active");
  };
}

// projects
