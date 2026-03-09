import $ from "jquery";

let navbar = document.getElementById("main-nav");
let navPos = navbar ? navbar.getBoundingClientRect().top : 0;

window.addEventListener("scroll", (e) => {
  let scrollPos = window.scrollY;
  if (navbar && scrollPos > navPos) {
    navbar.classList.add("sticky");
  } else if (navbar) {
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
$(function () {
  const slider = $("#news-slider");
  if (slider.length && typeof slider.owlCarousel === "function") {
    slider.owlCarousel({
      nav: true,
      items: 3,
      itemsDesktop: [1199, 3],
      itemsDesktopSmall: [980, 2],
      itemsMobile: [600, 1],
      navigation: true,
      navigationText: ["", ""],
      pagination: true,
      autoplay: true,
      loop: true,
      responsiveClass: true,
      responsive: {
        0: {
          items: 1,
        },
        700: {
          items: 2,
        },
        1000: {
          items: 3,
        },
      },
    });
  }
});

const launchButton = document.querySelector("#launch");
const chatContainer = document.querySelector("#smith-container");

if (launchButton && chatContainer) {
  launchButton.addEventListener("click", function () {
    chatContainer.classList.toggle("active");
  });
}
