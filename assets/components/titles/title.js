const template = document.createElement("template");
template.innerHTML = `
<style>
    :host {
        display: block;
        margin-bottom: 2rem;
    }

    .section-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .title-link {
        color: var(--link-color, #0d6efd);
        text-decoration: none;
        font-size: 1rem;
        font-weight: 500;
    }

    ::slotted(h1), ::slotted(h2), ::slotted(h3), ::slotted(*) {
        margin: 10px !important;
        font-size: 1.75rem !important;
        font-weight: 600 !important;
        color: var(--purple-color, var(--dark-purple-color, #2b0938)) !important;
    }

    .title-link:hover {
        text-decoration: underline;
    }
</style>

<div class="section-header">
    <slot>
        Teste
    </slot>
    
    <a id="action-link" class="title-link" href="#">
        <slot name="link-text">Ver todos</slot>
    </a>
</div>
`;

class CustomTitle extends HTMLElement {
    static observedAttributes = ["href"];

    #linkElement;

    constructor() {
        super();
        this.attachShadow({ mode: "open" }).append(
            template.content.cloneNode(true),
        );
        this.#linkElement = this.shadowRoot.querySelector("#action-link");
    }

    connectedCallback() {
        this.#updateHref();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (oldValue === newValue) return;

        if (name === "href") {
            this.#updateHref();
        }
    }

    #updateHref() {
        const href = this.getAttribute("href");
        if (href) {
            this.#linkElement.href = href;
            this.#linkElement.style.display = "inline";
        } else {
            this.#linkElement.style.display = "none";
        }
    }
}

if (!customElements.get("custom-title")) {
    customElements.define("custom-title", CustomTitle);
}
