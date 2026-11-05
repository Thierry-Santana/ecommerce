const carrosseis = document.querySelectorAll(".produtos-carousel");

function quantidadeVisivel() {

    if (window.innerWidth <= 576) {
        return 1;
    }

    if (window.innerWidth <= 992) {
        return 2;
    }

    return 5;
}

carrosseis.forEach(function (carousel) {

    const lista = carousel.querySelector(".produtos-lista");
    const btnNext = carousel.querySelector(".btnNext");
    const btnPrev = carousel.querySelector(".btnPrev");

    let posicao = 0;

    function moverCarousel(direcao) {

        const produtos = lista.querySelectorAll(".produto");

        if (produtos.length === 0) {
            return;
        }

        const visiveis = quantidadeVisivel();
        const total = produtos.length;

        const maxPosicao = Math.max(0, total - visiveis);

        posicao += direcao;

        if (posicao < 0) {
            posicao = 0;
        }

        if (posicao > maxPosicao) {
            posicao = maxPosicao;
        }

        const larguraProduto = produtos[0].offsetWidth;

        const estilo = window.getComputedStyle(lista);
        const gap = parseFloat(estilo.gap);

        lista.style.transform =
            `translateX(-${posicao * (larguraProduto + gap)}px)`;
    }

    btnNext.addEventListener("click", function () {
        moverCarousel(2);
    });

    btnPrev.addEventListener("click", function () {
        moverCarousel(-2);
    });

    window.addEventListener("resize", function () {

        posicao = 0;

        lista.style.transform = "translateX(0)";
    });

});