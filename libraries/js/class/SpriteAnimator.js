// Animation du sprite platine vinyle
class SpriteAnimator {
  constructor() {
    this.images = [
      "libraries/assets/image/anim_platine/200.png",
      "libraries/assets/image/anim_platine/201.png",
      "libraries/assets/image/anim_platine/202.png",
      "libraries/assets/image/anim_platine/203.png",
      "libraries/assets/image/anim_platine/204.png",
      "libraries/assets/image/anim_platine/205.png",
      "libraries/assets/image/anim_platine/206.png",
      "libraries/assets/image/anim_platine/207.png",
      "libraries/assets/image/anim_platine/208.png",
      "libraries/assets/image/anim_platine/209.png",
      "libraries/assets/image/anim_platine/210.png",
      "libraries/assets/image/anim_platine/211.png",
      "libraries/assets/image/anim_platine/212.png",
      "libraries/assets/image/anim_platine/213.png",
      "libraries/assets/image/anim_platine/214.png",
      "libraries/assets/image/anim_platine/215.png",
      "libraries/assets/image/anim_platine/216.png",
      "libraries/assets/image/anim_platine/217.png",
      "libraries/assets/image/anim_platine/218.png",
      "libraries/assets/image/anim_platine/219.png",
      "libraries/assets/image/anim_platine/220.png",
      "libraries/assets/image/anim_platine/221.png",
      "libraries/assets/image/anim_platine/222.png",
      "libraries/assets/image/anim_platine/223.png",
      "libraries/assets/image/anim_platine/224.png",
      "libraries/assets/image/anim_platine/225.png",
      "libraries/assets/image/anim_platine/226.png",
      "libraries/assets/image/anim_platine/227.png",
      "libraries/assets/image/anim_platine/228.png",
      "libraries/assets/image/anim_platine/229.png",
    ];
    this.sprite = document.getElementById("spriteVinyl");
    this.currentImage = 0;

    // Vérifie si la page actuelle est bien "gallery"
    if (window.location.href.includes("gallery")) {
      // Vérifie si l'élément avec l'ID "spriteVinyl" existe sur la page
      if (this.sprite) {
        // toutes les 40 millisecondes (0,04 seconde)
        setInterval(() => this.animateSprite(), 40);
      }
    }
  }

  animateSprite() {
    this.sprite.style.backgroundImage =
      "url(" + this.images[this.currentImage] + ")";
    // boucle en continu sur les images du tableau,
    // le modulo réinitialise la valeur de 'currentImage' à 0 une fois qu'elle atteint la longueur du tableau
    this.currentImage = (this.currentImage + 1) % this.images.length;
  }
}
export default SpriteAnimator;
