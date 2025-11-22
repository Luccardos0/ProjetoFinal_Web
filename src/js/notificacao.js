class ModalNotificacao {
    constructor() {
        this.modal = document.getElementById('modalNotificacao');
        this.init();
    }
    
    // Falta esse

    init() {
        if (!this.modal) return;

        // Botão de fechar (X)
        const closeBtn = this.modal.querySelector('.modal-close');
        closeBtn.addEventListener('click', () => this.fechar());

        // Botão "Entendido"
        const entendidoBtn = this.modal.querySelector('.btn-modal');
        entendidoBtn.addEventListener('click', () => this.fechar());

        // Fechar clicando fora
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.fechar();
            }
        });

        // Fechar com ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.fechar();
            }
        });

        // Auto-fechar após 10 segundos (opcional)
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

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('modalNotificacao')) {
        new ModalNotificacao();
    }
});