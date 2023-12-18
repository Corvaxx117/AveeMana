// Affichage en plein ecran des images de la galerie
class DisplayModalGallery {
  constructor() {
    this.images = document.querySelectorAll(".galleryImages img");
    this.modal = document.querySelector(".modal");
    this.modalImg = document.querySelector(".modal-content");
    this.closeModal = document.querySelector(".close");

    // Ajout de l'événement click à chaque image
    this.images.forEach((img) => {
      img.addEventListener("click", () => {
        // Affichage de la modale avec l'image en taille réelle
        this.modal.style.display = "block";
        this.modalImg.src = img.src;
      });
    });

    // Ajout de l'événement click à l'image affichée en grand
    this.modalImg.addEventListener("click", () => {
      // Fermeture de la modale
      this.modal.style.display = "none";
    });

    // Ajout de l'événement click à la fermeture de la modale
    this.closeModal.addEventListener("click", () => {
      // Fermeture de la modale
      this.modal.style.display = "none";
    });
  }
}
export default DisplayModalGallery;
