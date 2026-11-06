import { Controller } from '@hotwired/stimulus';
import Swal from 'sweetalert2';

export default class extends Controller {
    static values = { message: String };

    async submit(event) {
        event.preventDefault();

        const result = await Swal.fire({
            title: 'Tem certeza?',
            text: this.messageValue || 'Essa ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
        });

        if (result.isConfirmed) {
            this.element.submit();
        }
    }
}