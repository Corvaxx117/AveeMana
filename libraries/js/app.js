import PageTitleClickEvent from "./class/PageTitleClickEvent.js";
import SpriteAnimator from "./class/SpriteAnimator.js";
import HrRemover from "./class/HrRemover.js";
import FormUpdater from "./class/FormUpdater.js";
import NavBarScrollEvent from "./class/NavBarScrollEvent.js";
import PaypalPayment from "./class/PaypalPayment.js";
import DisplayModalGallery from "./class/DisplayModalGallery.js";

// Événement d'ouverture / fermeture du side menu burger au clic sur titre des pages
const pageTitle = new PageTitleClickEvent();

// Animation du sprite platine vinyle
const sprite = new SpriteAnimator();

// Suppression de la dernière balise hr lorsqu'on sort d'une boucle for each
const hrRemover = new HrRemover();

// Affichage au clic d'un formulaire de modification de données de l'utilisateur
const formUpdater = new FormUpdater();

// Événement d'affichage ou masquage navbar top & bottom au scroll
const navBar = new NavBarScrollEvent();

// Instancier la classe PaypalPayment uniquement sur la page du panier

// Si la classe "checkout" existe sur la page
if (document.querySelector(".checkoutPayment")) {
  // Récupérer la taille depuis la variable JavaScript définie dans le code PHP
}
// Si la route du panier est trouvée dans l'URL, .indexOf renverra la position où elle a été trouvée (un nombre positif), et donc la condition "position > -1" sera vraie.
if (document.querySelector(".checkout")) {
  const paypal = new PaypalPayment();
}
// Affichage en plein écran des images de la galerie
// Initialisation uniquement si la classe existe sur la page
if (document.querySelector(".galleryImages")) {
  const displayModal = new DisplayModalGallery();
}
