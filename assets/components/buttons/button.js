const template = document.createElement('template');
template.innerHTML = `
<style>
    .btn-grad {
        background-image: linear-gradient(to right, var(--blue-color) 0%, var(--purple-color) 51%, var(--dark-purple-color) 100%);
    }

    .btn-grad {
        padding: 15px 45px;
        text-align: center;
        transition: 0.5s;
        background-size: 200% auto;
        color: white;
        box-shadow: 0 0 20px #eee;
        border-radius: 10px;
        display: block;
        outline: none;
        border: none;
        width: 200px;
        text-decoration: none;
    }

    .btn-grad:hover {
        background-position: right center;
        color: #fff;
        text-decoration: none;
    }
</style>

<a class='btn-grad' part='button'>
    <slot> Botão </slot>
<a>
`;

class CustomButton extends HTMLElement {
    static observedAttributes = ['href'];

    constructor() {
        super();
        this.attachShadow({ mode: 'open' }).append(
            template.content.cloneNode(true),
        );
        this.link = this.shadowRoot.querySelector('a');
    }

    connectedCallback() {
        this.#updateHref();
    }

    #updateHref() {
        this.link.setAttribute('href', this.getAttribute('href') ?? '#');
    }
}

if (!customElements.get('custom-button')) {
    customElements.define('custom-button', CustomButton);
}
