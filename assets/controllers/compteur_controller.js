import { Controller } from '@hotwired/stimulus';

/*
 * Compteur de caractères. Exemple de "values" Stimulus : data-compteur-max-value="500".
 */
export default class extends Controller {
    static targets = ['champ', 'affichage'];
    static values = { max: Number };

    connect() {
        this.maj();
    }

    maj() {
        const longueur = this.champTarget.value.length;
        this.affichageTarget.textContent = `${longueur} / ${this.maxValue} caractères`;
        this.affichageTarget.classList.toggle('fr-error-text', longueur > this.maxValue);
    }
}
