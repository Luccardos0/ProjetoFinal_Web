class ModalNotificacao {

    constructor() {
        this.modal = document.getElementById('modalNotificacao');
        this.init();
    }

    init() {
        if (!this.modal) return;

        const closeBtn = this.modal.querySelector('.modal-close');
        closeBtn.addEventListener('click', () => this.fechar());

        const entendidoBtn = this.modal.querySelector('.btn-modal');
        entendidoBtn.addEventListener('click', () => this.fechar());

        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.fechar();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.fechar();
            }
        });

        this.autoCloseTimer = setTimeout(() => {
            this.fechar();
        }, 10000);
    }

    fechar() {
        if (this.autoCloseTimer) {
            clearTimeout(this.autoCloseTimer);
        }

        this.modal.style.opacity = '0';
        this.modal.style.transform = 'scale(0.8)';

        setTimeout(() => {
            this.modal.style.display = 'none';
        }, 300);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('modalNotificacao')) {
        new ModalNotificacao();
    }
});