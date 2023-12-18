// Evénement d'ouverture / fermeture du side menu burger au clic sur titre des pages
class PageTitleClickEvent {
  constructor() {
    // Titres h2 cliquables
    this.pageTitles = document.querySelectorAll(".pageTitle");
    this.pageTitles.forEach((pageTitle) => {
      pageTitle.addEventListener("click", () => {
        // Simule un clic sur le menu hamburger
        const burgerClicked = document.getElementById("hambToggle");
        burgerClicked.click();
      });
    });
  }
}
export default PageTitleClickEvent;
