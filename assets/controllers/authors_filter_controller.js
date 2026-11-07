import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['checkbox', 'count', 'empty', 'option', 'search'];

    connect() {
        this.updateCount();
    }

    filter() {
        const query = this.searchTarget.value.trim().toLocaleLowerCase();
        let visibleOptions = 0;

        this.optionTargets.forEach((option) => {
            const matches = option.textContent.trim().toLocaleLowerCase().includes(query);
            option.hidden = !matches;
            visibleOptions += Number(matches);
        });

        this.emptyTarget.hidden = visibleOptions > 0;
    }

    updateCount() {
        const selectedCount = this.checkboxTargets.filter((checkbox) => checkbox.checked).length;
        this.countTarget.textContent = selectedCount;
        this.countTarget.hidden = selectedCount === 0;
    }
}
