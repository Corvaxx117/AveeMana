// Evenement d'affichage ou masquage navbar top & bottom au scroll
class NavBarScrollEvent {
  constructor() {
    // Position de défilement actuelle de la page
    this.prevScrollpos = window.pageYOffset;
    // Top
    this.navbar = document.getElementById("navBar");
    // Bottom
    this.navBot = document.getElementById("navBot");
    window.addEventListener("scroll", () => {
      let currentScrollPos = window.scrollY;
      // Position de défilement actuelle + hauteur de la fenêtre
      let scrollPosition = window.innerHeight + window.scrollY;
      // si la position de défilement actuelle de la page est inférieure à la position de défilement précédente
      if (this.prevScrollpos > currentScrollPos) {
        // Afficher
        this.navbar.style.opacity = "1";
      } else {
        // Masquer
        this.navbar.style.opacity = "0.3";
        // Réduction du zIndex pour pouvoir cliquer à travers
/*        this.navbar.style.zIndex = "0";*/
      }
      // si la position de défilement actuelle est à la fin de la page
      if (scrollPosition >= document.body.offsetHeight) {
        // Afficher
        this.navBot.classList.add("show");
      } else {
        // Masquer
        this.navBot.classList.remove("show");
        this.navBot.style.zIndex = "-33";
      }
      this.prevScrollpos = currentScrollPos;
    });
  }
}
export default NavBarScrollEvent;