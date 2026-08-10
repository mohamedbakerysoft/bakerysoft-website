const header = document.getElementById("main-nav");

const updateHeader = () => {
  if (header) header.classList.toggle("sticky", window.scrollY > 20);
};

updateHeader();
window.addEventListener("scroll", updateHeader, { passive: true });
