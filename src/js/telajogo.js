/*
   Jogo da Memória "Memóremon"
   Lógica do Jogo da Memória para a Matéria de Web.
   Autores: Pedro Coelho Terossi, Leonardo Bonfá Schroeder, Lucas de Olivera Lopes Cardoso e Guilherme Vicente Ramalho. 
 */

document.addEventListener('DOMContentLoaded', iniciarEventos);

// Seleção dos elementos principais da tela 
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

// Configurações iniciais e estado do jogo 
const POKEMONS = [
  'pikachu', 'charizard', 'eevee', 'mewtwo', 'mew', 'gengar',
  'lucario', 'greninja', 'umbreon', 'sylveon', 'arcanine', 'dragonite',
  'blastoise', 'bulbasaur', 'squirtle', 'charmander', 'lapras', 'snorlax',
  'jigglypuff', 'psyduck', 'gyrados', 'alakazam', 'magikarp', 'lugia',
  'rayquaza', 'jirachi', 'vaporeon', 'jolteon', 'flareon', 'hooh', 'gardevoir', 'darkrai'
];

const TEMPO_POR_TAMANHO = { 2: 30, 4: 90, 6: 180, 8: 300 };

// Variáveis de controle do jogo
let tamanho = 4;
let modo = 'Clássico';
let virouUmaCarta = false;
let travarTabuleiro = false; // bloqueia cliques (usado enquanto cartas estão desvirando).
let primeiraCarta, segundaCarta;
let jogadas = 0;
let timer = null;
let segundos = 0;
let paresEncontrados = 0;
let totalPares = 0;
let timerAtivo = false;

// Criando o tabuleiro e iniciando o jogo 
function iniciarJogo(iniciarTimer = false) {
  tabuleiroValor.textContent = `${tamanho}x${tamanho}`;
  modalidadeValor.textContent = modo;
  tabuleiro.innerHTML = ''; //Limpa o Tabuleiro
  tabuleiro.style.gridTemplateColumns = `repeat(${tamanho}, 1fr)`;
  tabuleiro.style.gridTemplateRows = `repeat(${tamanho}, 1fr)`;

  totalPares = (tamanho * tamanho) / 2;

  const pokemonsUsados = POKEMONS.slice(0, totalPares);
  const baralho = [...pokemonsUsados, ...pokemonsUsados].sort(() => Math.random() - 0.5);

  baralho.forEach(pokemon => {
    const carta = document.createElement('div');
    carta.className = 'card';
    carta.dataset.name = pokemon;
    carta.addEventListener('click', virarCarta);

    const frente = document.createElement('div');
    frente.className = 'front';

    const verso = document.createElement('div');
    verso.className = 'back';
    verso.style.backgroundImage = `url('../img/${pokemon}.png')`;

    carta.appendChild(frente); //Insere a frente na Carta
    carta.appendChild(verso); //Insere o verso na Carta
    tabuleiro.appendChild(carta); //Insere a carta no tabuleiro
  });

  reiniciarStatus();
  if (iniciarTimer) comecarTempo();
}

// Função chamada ao clicar em uma carta 
function virarCarta() {
  if (!timerAtivo) comecarTempo();
  if (travarTabuleiro || this === primeiraCarta) return;

  this.classList.add('flip');

  if (!virouUmaCarta) {
    virouUmaCarta = true;
    primeiraCarta = this;
    return;
  }

  segundaCarta = this;
  jogadas++;
  jogadasValor.textContent = jogadas;
  checarPar();
}

function checarPar() {
  const acerto = primeiraCarta.dataset.name === segundaCarta.dataset.name;
  acerto ? marcarPar() : desvirarCartas();
}

function marcarPar() {
  primeiraCarta.removeEventListener('click', virarCarta);
  segundaCarta.removeEventListener('click', virarCarta);
  primeiraCarta.classList.add('matched');
  segundaCarta.classList.add('matched');
  paresEncontrados++;
  resetarSelecao();
  if (paresEncontrados === totalPares) fimDeJogo(true);
}

function desvirarCartas() {
  travarTabuleiro = true;
  setTimeout(() => {
    primeiraCarta.classList.remove('flip');
    segundaCarta.classList.remove('flip');
    resetarSelecao();
  }, 1200);
}

// Controle de estado e fim de jogo
function resetarSelecao() {
  [virouUmaCarta, travarTabuleiro] = [false, false];
  [primeiraCarta, segundaCarta] = [null, null];
}

function fimDeJogo(venceu) {
  clearInterval(timer);
  travarTabuleiro = true;
  salvarPartida(venceu);
  setTimeout(() => {
    if (venceu) {
      alert(`Parabéns! Você venceu com ${jogadas} jogadas em ${tempoValor.textContent}.`);
    } else {
      alert(`Tempo esgotado! Fim de jogo.`);
    }
  }, 400);
}

function reiniciarStatus() {
  jogadas = 0;
  paresEncontrados = 0;
  jogadasValor.textContent = '0';
  travarTabuleiro = false;
  clearInterval(timer);
  timerAtivo = false;
  segundos = 0;
  tempoValor.textContent = '00:00';
  botaoDesativarTrapaca.disabled = true;
  botaoAtivarTrapaca.disabled = false;
  statusTrapaca.textContent = 'Desativado';
}

// Timer 
function comecarTempo() {
  timerAtivo = true;
  modo === 'Clássico' ? tempoCrescente() : tempoRegressivo();
}

function tempoCrescente() {
  segundos = 0;
  tempoValor.textContent = formatarTempo(segundos);
  timer = setInterval(() => {
    segundos++;
    tempoValor.textContent = formatarTempo(segundos);
  }, 1000);
}

function tempoRegressivo() {
  segundos = TEMPO_POR_TAMANHO[tamanho];
  tempoValor.textContent = formatarTempo(segundos);
  timer = setInterval(() => {
    segundos--;
    tempoValor.textContent = formatarTempo(segundos);
    if (segundos <= 0) fimDeJogo(false);
  }, 1000);
}

function formatarTempo(total) {
  const min = Math.floor(total / 60);
  const seg = total % 60;
  return `${String(min).padStart(2, '0')}:${String(seg).padStart(2, '0')}`;
}

// Modo Trapaça (mostrar/ocultar cartas)
function mostrarCartas() {
  document.querySelectorAll('.card:not(.matched)').forEach(c => c.classList.add('flip'));
  statusTrapaca.textContent = 'Ativado';
  botaoAtivarTrapaca.disabled = true;
  botaoDesativarTrapaca.disabled = false;
}

function esconderCartas() {
  document.querySelectorAll('.card:not(.matched)').forEach(c => c.classList.remove('flip'));
  statusTrapaca.textContent = 'Desativado';
  botaoAtivarTrapaca.disabled = false;
  botaoDesativarTrapaca.disabled = true;
}

// Inicialização dos botões e do jogo
function iniciarEventos() {
  botoesTamanho.forEach(botao => {
    botao.addEventListener('click', () => {
      botoesTamanho.forEach(b => b.classList.remove('ativo'));
      botao.classList.add('ativo');
      tamanho = parseInt(botao.dataset.size);
      iniciarJogo(true);
    });
  });

  botoesModo.forEach(botao => {
    botao.addEventListener('click', () => {
      botoesModo.forEach(b => b.classList.remove('ativo'));
      botao.classList.add('ativo');
      modo = botao.textContent;
      iniciarJogo(true);
    });
  });

  botaoIniciar.addEventListener('click', () => iniciarJogo(true));
  botaoReiniciar.addEventListener('click', () => iniciarJogo(true));
  botaoAtivarTrapaca.addEventListener('click', mostrarCartas);
  botaoDesativarTrapaca.addEventListener('click', esconderCartas);

  iniciarJogo(false);
}

// Integração com o Back
function salvarPartida(venceu) {
  let tempoFinalSeg = segundos;
  let dimensaoFinal = `${tamanho}x${tamanho}`;

  if (modo !== 'Clássico') {
    tempoFinalSeg = TEMPO_POR_TAMANHO[tamanho] - segundos;
  }

  const tempoString = tempoFinalSeg.toFixed(2);

  const dados = new URLSearchParams();
  dados.append('dimensao', dimensaoFinal);
  dados.append('modalidade', modo);
  dados.append('num_jogadas', jogadas);
  dados.append('tempo_gasto_seg', tempoString);
  dados.append('resultado', venceu ? 'Vitoria' : 'Derrota');

  try {
    fetch('../back/salvarPartida.php', {
      method: 'POST',
      body: dados
    }).then(response => {
      setTimeout(() => {
        window.location.reload();
      }, 700);
    })
  } catch (error) {
    console.error(error);
  }
}

iniciarEventos();

