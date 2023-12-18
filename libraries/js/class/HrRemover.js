// Suppression de la dernère balise hr lorsqu'on sort d'une boucle for each
class HrRemover {
  constructor() {
    const hrElems = document.querySelectorAll("hr");
    // S'il y a au moin une balise <hr>
    if (hrElems.length > 0) {
      // indice du dernier hr de la nodelist, suppression
      hrElems[hrElems.length - 1].remove();
    }
  }
}

export default HrRemover;
