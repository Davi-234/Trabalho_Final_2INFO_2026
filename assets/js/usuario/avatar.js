const imagemPerfil = document.getElementById("perfil-imagem");
const inicialPerfil = document.getElementById("perfil-inicial");

if (imagemPerfil && inicialPerfil) {
    const mostrarImagemSeCarregada = () => {
        if (imagemPerfil.complete && imagemPerfil.naturalWidth > 0) {
            imagemPerfil.hidden = false;
            inicialPerfil.hidden = true;
        }
    };

    imagemPerfil.addEventListener("load", mostrarImagemSeCarregada, { once: true });
    imagemPerfil.addEventListener("error", () => imagemPerfil.remove(), { once: true });
    mostrarImagemSeCarregada();
}