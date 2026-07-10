const header = document.querySelector("[data-header]");
const menuToggle = document.querySelector("[data-menu-toggle]");
const nav = document.querySelector("[data-nav]");

function updateHeader() { header.classList.toggle("is-scrolled", window.scrollY > 12); }
menuToggle.addEventListener("click", () => {
  const isOpen = nav.classList.toggle("is-open");
  header.classList.toggle("is-open", isOpen);
  menuToggle.setAttribute("aria-expanded", String(isOpen));
});
nav.addEventListener("click", (event) => {
  if (event.target.closest("a")) {
    nav.classList.remove("is-open"); header.classList.remove("is-open"); menuToggle.setAttribute("aria-expanded", "false");
  }
});
updateHeader();
window.addEventListener("scroll", updateHeader, { passive: true });

const revealItems = document.querySelectorAll(".section article, .section-heading, .architecture-visual, .purpose-section blockquote");
if ("IntersectionObserver" in window) {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => { if (entry.isIntersecting) { entry.target.classList.add("is-visible"); observer.unobserve(entry.target); } });
  }, { threshold: 0.1 });
  revealItems.forEach((item) => { item.classList.add("reveal"); observer.observe(item); });
}
