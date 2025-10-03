/**
 * @file telajogo.js
 * @description Lógica principal para o jogo da memória "Memóremon" do projeto de SI401.
 * @author Pedro Coelho Terossi
 */

(() => {
    // -------------------------------------------------------------------
    //  1. MAPEAMENTO DOS ELEMENTOS (DOM)
    // -------------------------------------------------------------------
    const tabuleiro = document.querySelector('.tabuleiro-jogo');
    const botaoIniciar = document.querySelector('.botao-iniciar');
    const botaoReiniciar = document.querySelector('.botao-reiniciar');
    const botoesTamanho = document.querySelectorAll('.botao-tamanho');
    const botoesModo = document.querySelectorAll('.botao-modo');
    const tempoValor = document.querySelector('#tempo-valor');
    const jogadasValor = document.querySelector('#jogadas-valor');
    const tabuleiroValor = document.querySelector('#tabuleiro-valor');
    const modalidadeValor = document.querySelector('#modalidade-valor');
    const botaoAtivarTrapaca = document.querySelector('.botao-ativar');
    const botaoDesativarTrapaca = document.querySelector('.botao-desativar');
    const statusTrapaca = document.querySelector('.texto-status');

    // -------------------------------------------------------------------
    //  2. DADOS, CONFIGURAÇÕES E ESTADO DO JOGO (STATE)
    // -------------------------------------------------------------------
    const POKEMONS = [
        'bulbasaur', 'charmander', 'squirtle', 'pikachu', 'jigglypuff', 'meowth', 
        'psyduck', 'snorlax', 'dragonite', 'mewtwo', 'chikorita', 'cyndaquil', 
        'totodile', 'togepi', 'marill', 'sudowoodo', 'wobbuffet', 'girafarig', 
        'dunsparce', 'gligar', 'treecko', 'torchic', 'mudkip', 'gardevoir', 
        'slakoth', 'loudred', 'makuhita', 'azurill', 'sableye', 'mawile', 
        'plusle', 'minun'
    ];
    const TEMPO_POR_TAMANHO = { 2: 30, 4: 90, 6: 180, 8: 300 };

    let tamanhoSelecionado = 4;
    let modoSelecionado = 'Clássico';
    let hasFlippedCard, lockBoard, firstCard, secondCard;
    let moves, timer, seconds, matchedPairs, totalPairs;
    let timerRodando = false;

    // -------------------------------------------------------------------
    //  3. LÓGICA PRINCIPAL DO JOGO
    // -------------------------------------------------------------------
    function iniciarJogo(iniciarTimerImediatamente = false) {
        tabuleiroValor.textContent = `${tamanhoSelecionado}x${tamanhoSelecionado}`;
        modalidadeValor.textContent = modoSelecionado;
        tabuleiro.innerHTML = '';
        tabuleiro.style.gridTemplateColumns = `repeat(${tamanhoSelecionado}, 1fr)`;
        totalPairs = (tamanhoSelecionado * tamanhoSelecionado) / 2;
        const pokemonsParaOJogo = POKEMONS.slice(0, totalPairs);
        const baralho = [...pokemonsParaOJogo, ...pokemonsParaOJogo];
        baralho.sort(() => Math.random() - 0.5);

        baralho.forEach(pokemon => {
            const card = document.createElement('div');
            card.className = 'card';
            card.dataset.name = pokemon;
            card.addEventListener('click', flipCard);
            const frontFace = document.createElement('div');
            frontFace.className = 'front';
            const backFace = document.createElement('div');
            backFace.className = 'back';
            backFace.style.backgroundImage = `url('img/${pokemon}.png')`;
            card.appendChild(frontFace);
            card.appendChild(backFace);
            tabuleiro.appendChild(card);
        });

        resetGameInfo();
        if (iniciarTimerImediatamente) {
            iniciarTimers();
        }
    }

    function flipCard() {
        if (!timerRodando) {
            iniciarTimers();
        }
        if (lockBoard || this === firstCard) return;
        this.classList.add('flip');
        if (!hasFlippedCard) {
            hasFlippedCard = true;
            firstCard = this;
            return;
        }
        secondCard = this;
        updateMoves();
        checkForMatch();
    }

    function checkForMatch() {
        const isMatch = firstCard.dataset.name === secondCard.dataset.name;
        isMatch ? disableCards() : unflipCards();
    }

    function disableCards() {
        firstCard.removeEventListener('click', flipCard);
        secondCard.removeEventListener('click', flipCard);
        firstCard.classList.add('matched');
        secondCard.classList.add('matched');
        matchedPairs++;
        resetBoard();
        checkWinCondition();
    }

    function unflipCards() {
        lockBoard = true;
        setTimeout(() => {
            firstCard.classList.remove('flip');
            secondCard.classList.remove('flip');
            resetBoard();
        }, 1200);
    }

    // -------------------------------------------------------------------
    //  4. FUNÇÕES DE CONTROLE E UI
    // -------------------------------------------------------------------
    function resetBoard() {
        [hasFlippedCard, lockBoard] = [false, false];
        [firstCard, secondCard] = [null, null];
    }

    function checkWinCondition() {
        if (matchedPairs === totalPairs) {
            endGame(true);
        }
    }

    function endGame(playerWon) {
        clearInterval(timer);
        lockBoard = true;
        setTimeout(() => {
            if (playerWon) {
                alert(`Parabéns! Você venceu em ${moves} jogadas e um tempo de ${tempoValor.textContent}!`);
            } else {
                alert(`Tempo esgotado! Você perdeu.`);
            }
        }, 500);
    }

    function updateMoves() {
        moves++;
        jogadasValor.textContent = moves;
    }

    function resetGameInfo() {
        moves = 0;
        matchedPairs = 0;
        jogadasValor.textContent = '0';
        lockBoard = false;
        clearInterval(timer);
        timerRodando = false;
        seconds = 0;
        tempoValor.textContent = '00:00';
        botaoDesativarTrapaca.disabled = true;
        botaoAtivarTrapaca.disabled = false;
        statusTrapaca.textContent = 'Desativado';
    }

    function iniciarTimers() {
        timerRodando = true;
        modoSelecionado === 'Clássico' ? startTimer() : startCountdownTimer();
    }

    function startTimer() {
        seconds = 0;
        tempoValor.textContent = formatTime(seconds);
        timer = setInterval(() => {
            seconds++;
            tempoValor.textContent = formatTime(seconds);
        }, 1000);
    }

    function startCountdownTimer() {
        seconds = TEMPO_POR_TAMANHO[tamanhoSelecionado];
        tempoValor.textContent = formatTime(seconds);
        timer = setInterval(() => {
            seconds--;
            tempoValor.textContent = formatTime(seconds);
            if (seconds <= 0) {
                endGame(false);
            }
        }, 1000);
    }

    function formatTime(totalSeconds) {
        const min = Math.floor(totalSeconds / 60);
        const sec = totalSeconds % 60;
        return `${String(min).padStart(2, '0')}:${String(sec).padStart(2, '0')}`;
    }

    // -------------------------------------------------------------------
    //  5. MODO TRAPAÇA
    // -------------------------------------------------------------------
    function mostrarTodasCartas() {
        const cartasOcultas = document.querySelectorAll('.card:not(.matched)');
        cartasOcultas.forEach(card => card.classList.add('flip'));
        statusTrapaca.textContent = 'Ativado';
        botaoAtivarTrapaca.disabled = true;
        botaoDesativarTrapaca.disabled = false;
    }

    function ocultarCartasNaoCombinadas() {
        const cartasOcultas = document.querySelectorAll('.card:not(.matched)');
        cartasOcultas.forEach(card => card.classList.remove('flip'));
        statusTrapaca.textContent = 'Desativado';
        botaoAtivarTrapaca.disabled = false;
        botaoDesativarTrapaca.disabled = true;
    }

    // -------------------------------------------------------------------
    //  6. INICIALIZAÇÃO DOS EVENT LISTENERS E DO JOGO
    // -------------------------------------------------------------------
    function inicializar() {
        botoesTamanho.forEach(botao => {
            botao.addEventListener('click', () => {
                botoesTamanho.forEach(btn => btn.classList.remove('ativo'));
                botao.classList.add('ativo');
                tamanhoSelecionado = parseInt(botao.dataset.size);
                iniciarJogo(true); // Inicia novo jogo ao mudar tamanho
            });
        });

        botoesModo.forEach(botao => {
            botao.addEventListener('click', () => {
                botoesModo.forEach(btn => btn.classList.remove('ativo'));
                botao.classList.add('ativo');
                modoSelecionado = botao.textContent;
                iniciarJogo(true); // Inicia novo jogo ao mudar modo
            });
        });

        botaoIniciar.addEventListener('click', () => iniciarJogo(true));
        botaoReiniciar.addEventListener('click', () => iniciarJogo(true));
        botaoAtivarTrapaca.addEventListener('click', mostrarTodasCartas);
        botaoDesativarTrapaca.addEventListener('click', ocultarCartasNaoCombinadas);

        iniciarJogo(false);
    }

    inicializar();

})();