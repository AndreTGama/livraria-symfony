const template = document.createElement("template");
template.innerHTML = `
<style>
    :host {
        display: inline-block;
    }

    :host([variant="outline"]) .btn-grad {
        background: transparent;
        color: var(--purple-color);
        border: 2px solid var(--purple-color);
    }

    .btn-grad {
        background-image: linear-gradient(to right, var(--blue-color) 0%, var(--purple-color) 51%, var(--dark-purple-color) 100%);
        padding: 15px 45px;
        text-align: center;
        transition: 0.5s;
        background-size: 200% auto;
        color: white;
        box-shadow: 0 0 20px #eee;
        border-radius: 10px;
        display: block;
        border: none;
        width: 200px;
        text-decoration: none;
    }

    .btn-grad:hover {
        background-position: right center;
        color: #fff;
        text-decoration: none;
    }

    .btn-grad:focus-visible {
        outline: 2px solid var(--dark-purple-color);
    }
</style>

<a class='btn-grad' part='button'>
    <slot> Botão </slot>
</a>
`;

class CustomButton extends HTMLElement {
    static formAssociated = true;
    static observedAttributes = ["href", "type", "target"];

    constructor() {
        super();
        this.internals = this.attachInternals();
        this.attachShadow({ mode: "open" }).append(
            template.content.cloneNode(true),
        );
        this.link = this.shadowRoot.querySelector("a");

        this.link.addEventListener("click", (event) => {
            if (!this.#isSubmit()) return;
            event.preventDefault();
            this.#submit();
        });

        this.link.addEventListener("keydown", (event) => {
            if (
                this.#isSubmit() &&
                (event.key === "Enter" || event.key === " ")
            ) {
                event.preventDefault();
                this.#submit();
            }
        });
    }

    connectedCallback() {
        this.#update();
    }

    attributeChangedCallback() {
        this.#update();
    }

    #isSubmit() {
        return this.getAttribute("type") === "submit";
    }

    #submit() {
        this.internals.form?.requestSubmit();
    }

    #update() {
        if (this.#isSubmit()) {
            this.link.removeAttribute("href");
            this.link.removeAttribute("target");
            this.link.setAttribute("role", "button");
            this.link.setAttribute("tabindex", "0");
        } else {
            this.link.setAttribute("href", this.getAttribute("href") ?? "#");
            
            const target = this.getAttribute("target");
            if (target) {
                this.link.setAttribute("target", target);
            } else {
                this.link.removeAttribute("target");
            }
            this.link.removeAttribute("role");
            this.link.removeAttribute("tabindex");
        }
    }
}

if (!customElements.get("custom-button")) {
    customElements.define("custom-button", CustomButton);
}
