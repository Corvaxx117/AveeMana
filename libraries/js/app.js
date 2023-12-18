import PageTitleClickEvent from "./class/PageTitleClickEvent.js";
import SpriteAnimator from "./class/SpriteAnimator.js";
import HrRemover from "./class/HrRemover.js";
import FormUpdater from "./class/FormUpdater.js";
import NavBarScrollEvent from "./class/NavBarScrollEvent.js";
import DisplayModalGallery from "./class/DisplayModalGallery.js";
import PaypalPayment from "./class/PaypalPayment.js";

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

// Instancier la classe PaypalPayment
const paypal = new PaypalPayment();

// Affichage en plein écran des images de la galerie
// Initialisation de la classe uniquement sur la page galerie
if (document.querySelector(".galleryImages")) {
  const displayModal = new DisplayModalGallery();
}
