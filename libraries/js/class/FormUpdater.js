// Affichage d'un formulaire de modification de données de l'utilisateur
class FormUpdater {
  constructor() {
    // Blocs infos cliquables sur la page Mon compte
    this.elements = document.querySelectorAll(".col-4");
    // Formulaire update
    this.form = document.getElementById("updateInfos");
    this.elements.forEach((element) => {
      // Au clic sur les blocs
      element.addEventListener("click", () => {
        if (this.form.style.display === "none") {
          // Afficher
          this.form.style.display = "block";
        } else {
          // Masquer
          this.form.style.display = "none";
        }
      });
    });
  }
}

export default FormUpdater;
