import { Controller } from '@hotwired/stimulus';

/*
 * Demande une confirmation avant d'envoyer un formulaire.
 * HTML : data-controller="confirmation" data-confirmation-message-value="…" data-action="submit->confirmation#demander"
 */
export default class extends Controller {
    static values = { message: { type: String, default: 'Confirmer ?' } };

    demander(event) {
        if (!window.confirm(this.messageValue)) {
            event.preventDefault();
        }
    }
}
