import { Controller } from '@hotwired/stimulus';

/*
 * Filtre côté navigateur une liste d'éléments.
 * HTML : data-controller="filtre", puis des targets "champ", "element" (avec data-texte) et "compteur".
 */
export default class extends Controller {
    static targets = ['champ', 'element', 'compteur'];

    connect() {
        this.filtrer();
    }

    filtrer() {
        const recherche = this.champTarget.value.trim().toLowerCase();
        let visibles = 0;

        this.elementTargets.forEach((element) => {
            const correspond = element.dataset.texte.includes(recherche);
            element.hidden = !correspond;
            if (correspond) visibles++;
        });

        if (this.hasCompteurTarget) {
            this.compteurTarget.textContent = `${visibles} formation${visibles > 1 ? 's' : ''} affichée${visibles > 1 ? 's' : ''}`;
        }
    }
}
