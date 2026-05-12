// O evento DOMContentLoaded garante que o JS só rode após o HTML estar pronto
document.addEventListener('DOMContentLoaded', () => {
    const track = document.querySelector('.carousel-track');
    const nextBtn = document.querySelector('.next');
    const prevBtn = document.querySelector('.prev');
    const searchInput = document.querySelector('input[type="search"]');
    const searchForm = document.querySelector('.search-container form');

    // Segurança: Se os elementos não existirem na página, o script para aqui
    if (!track || !nextBtn || !prevBtn) return;

    // Salva os cards originais para referência
    const originalCards = Array.from(document.querySelectorAll('.carousel .card'));
    const visibleCards = 3;
    let index = visibleCards;
    let cardWidth;

    // Função para (re)inicializar o Carrossel
    function initCarousel() {
        track.innerHTML = "";
        
        // Clonagem para efeito infinito
        const firstClones = originalCards.slice(0, visibleCards).map(card => card.cloneNode(true));
        const lastClones = originalCards.slice(-visibleCards).map(card => card.cloneNode(true));

        // Ordem correta dos clones: [Clones do Fim] [Cards Reais] [Clones do Início]
        lastClones.forEach(clone => track.append(clone)); 
        originalCards.forEach(card => track.append(card));
        firstClones.forEach(clone => track.append(clone));

        // Calcula a largura considerando margens (ajuste o 20 conforme seu CSS)
        cardWidth = originalCards[0].offsetWidth + 20;
        
        index = visibleCards;
        track.style.transition = "none";
        track.style.transform = `translateX(-${index * cardWidth}px)`;
        
        nextBtn.style.display = "block";
        prevBtn.style.display = "block";
    }

    // Função de Busca
    function filterEvents(searchTerm) {
        const term = searchTerm.toLowerCase().trim();

        if (term === "") {
            initCarousel();
            return;
        }

        const filtered = originalCards.filter(card => {
            const title = card.querySelector('h3').innerText.toLowerCase();
            const desc = card.querySelector('p:not(.data-evento)').innerText.toLowerCase();
            return title.includes(term) || desc.includes(term);
        });

        track.style.transition = "none";
        track.style.transform = `translateX(0)`;
        track.innerHTML = "";
        
        if (filtered.length > 0) {
            filtered.forEach(card => track.append(card.cloneNode(true)));
        } else {
            track.innerHTML = "<p style='padding: 20px; color: white;'>Nenhum evento encontrado.</p>";
        }

        nextBtn.style.display = "none";
        prevBtn.style.display = "none";
    }

    // --- Listeners (Ouvintes de Eventos) ---

    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        filterEvents(searchInput.value);
    });

    searchInput.addEventListener('input', (e) => {
        if (e.target.value === "") filterEvents("");
    });

    nextBtn.addEventListener('click', () => {
        const allCards = track.querySelectorAll('.card');
        if (index >= allCards.length - visibleCards) return;
        index++;
        moveCarousel();
    });

    prevBtn.addEventListener('click', () => {
        if (index <= 0) return;
        index--;
        moveCarousel();
    });

    function moveCarousel() {
        track.style.transition = "transform 0.4s ease-in-out";
        track.style.transform = `translateX(-${index * cardWidth}px)`;
    }

    track.addEventListener('transitionend', () => {
        const allCards = track.querySelectorAll('.card');
        // Se houver busca ativa, não aplica a lógica de loop
        if (searchInput.value !== "") return; 

        if (index >= allCards.length - visibleCards) {
            track.style.transition = "none";
            index = visibleCards;
            track.style.transform = `translateX(-${index * cardWidth}px)`;
        }
        if (index <= 0) {
            track.style.transition = "none";
            index = allCards.length - (visibleCards * 2);
            track.style.transform = `translateX(-${index * cardWidth}px)`;
        }
    });

    // Inicializa ao carregar
    initCarousel();

    // Recalcula largura se a janela mudar de tamanho
    window.addEventListener('resize', () => {
        cardWidth = originalCards[0].offsetWidth + 20;
        track.style.transform = `translateX(-${index * cardWidth}px)`;
    });
});
