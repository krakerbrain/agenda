// offcanvas-manager.js
export class OffcanvasManager {
  constructor({ toggleSelector, menuSelector, closeSelector, backdropSelector, direction = "left", onOpen, onClose }) {
    this.toggle = document.querySelector(toggleSelector);
    this.menu = document.querySelector(menuSelector);
    this.close = document.querySelector(closeSelector);
    this.backdrop = document.querySelector(backdropSelector);
    this.direction = direction;
    this.onOpen = onOpen;
    this.onClose = onClose;
    this._init();
  }

  _init() {
    if (!this.toggle || !this.menu || !this.close || !this.backdrop) return;
    this.toggle.addEventListener("click", () => this.open());
    this.close.addEventListener("click", () => this.closeCanvas());
    this.backdrop.addEventListener("click", () => this.closeCanvas());
  }

  open() {
    if (this.direction === "top") {
      this.menu.classList.remove("-translate-y-full");
      this.menu.classList.add("translate-y-0");
    } else {
      this.menu.classList.remove("-translate-x-full");
      this.menu.classList.add("translate-x-0");
    }
    this.backdrop.classList.remove("hidden");
    document.body.classList.add("overflow-hidden");
    if (typeof this.onOpen === "function") this.onOpen();
  }

  closeCanvas() {
    if (this.direction === "top") {
      this.menu.classList.remove("translate-y-0");
      this.menu.classList.add("-translate-y-full");
    } else {
      this.menu.classList.remove("translate-x-0");
      this.menu.classList.add("-translate-x-full");
    }
    this.backdrop.classList.add("hidden");
    document.body.classList.remove("overflow-hidden");
    if (typeof this.onClose === "function") this.onClose();
  }
}
