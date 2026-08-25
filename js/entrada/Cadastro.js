const form = document.getElementById("form-cadastro");

        const sections = Array.from(
            form.querySelectorAll("section")
        );

        const progressItems = Array.from(
            document.querySelectorAll("#progressbar li")
        );

        const progressBar =
            document.getElementById("progress-bar");

        let etapaAtual = 0;

        function atualizarProgresso() {

            const porcentagem =
                ((etapaAtual + 1) / sections.length) * 100;

            progressBar.style.width =
                `${porcentagem}%`;


            progressItems.forEach(
                (item, index) => {

                    item.classList.remove("active");
                    item.classList.remove("completed");

                    if (index < etapaAtual) {
                        item.classList.add("completed");
                    }

                    if (index === etapaAtual) {
                        item.classList.add("active");
                    }

                }
            );

        }

        function mostrarEtapa(numero) {

            sections.forEach(
                (section, index) => {

                    section.style.display =
                        index === numero
                            ? "block"
                            : "none";

                }
            );

            etapaAtual = numero;

            atualizarProgresso();

            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });

        }

        const campoSenha =
            document.getElementById("validationCustom03");

        const campoConfirmacao =
            document.getElementById("validationCustom04");

        function atualizarRequisitosSenha() {

            const valoresSenha =
                validar_senha(campoSenha.value);


            const requisitos =
                valoresSenha.requisitos_senha;

            const requisitoMinimo =
                document.getElementById("senha_pequena");

            const requisitoEspecial =
                document.getElementById("caracteres_especiais");

            const requisitoNumeros =
                document.getElementById("contem_numeros");

            const requisitoMinusculas =
                document.getElementById("letras_minusculas");

            const requisitoMaiusculas =
                document.getElementById("letras_maiusculas");

            requisitoMinimo.classList.toggle(
                "visivel",
                !requisitos.tamanho_minimo
            );

            requisitoEspecial.classList.toggle(
                "visivel",
                !requisitos.tem_caracteres_especiais
            );

            requisitoNumeros.classList.toggle(
                "visivel",
                !requisitos.tem_numeros
            );

            requisitoMinusculas.classList.toggle(
                "visivel",
                !requisitos.tem_letras_minusculas
            );

            requisitoMaiusculas.classList.toggle(
                "visivel",
                !requisitos.tem_letras_maiusculas
            );


            atualizarTituloRequisitos();

        }

        function atualizarTituloRequisitos() {

            const mensagens =
                Array.from(
                    document.querySelectorAll(".mensagem_erro")
                );

            const existeErro =
                mensagens.some(
                    mensagem =>
                        mensagem.classList.contains("visivel")
                );


            document.getElementById(
                "titulo_erro_senha"
            ).style.display =
                existeErro
                    ? "block"
                    : "none";

        }

        function validarConfirmacaoSenha() {

            const senha =
                campoSenha.value;

            const confirmacao =
                campoConfirmacao.value;


            if (
                confirmacao.length > 0 &&
                senha !== confirmacao
            ) {

                campoConfirmacao.setCustomValidity(
                    "As senhas não coincidem."
                );

                return false;

            }


            campoConfirmacao.setCustomValidity("");

            return true;

        }

        campoSenha.addEventListener(
            "input",
            () => {

                atualizarRequisitosSenha();
                validarConfirmacaoSenha();

            }
        );

        campoConfirmacao.addEventListener(
            "input",
            () => {

                validarConfirmacaoSenha();

            }
        );

        function validarEtapa(numero) {

            const campos =
                Array.from(
                    sections[numero].querySelectorAll(
                        "input, select, textarea"
                    )
                );


            let etapaValida = true;


            campos.forEach(
                campo => {
                    if (
                        campo.required &&
                        !campo.checkValidity()
                    ) {

                        etapaValida = false;

                    }

                }
            );

            if (numero === 1) {

                atualizarRequisitosSenha();

                const mensagens =
                    Array.from(
                        document.querySelectorAll(".mensagem_erro")
                    );

                const senhaPossuiErros =
                    mensagens.some(
                        mensagem =>
                            mensagem.classList.contains("visivel")
                    );


                if (senhaPossuiErros) {

                    campoSenha.setCustomValidity(
                        "A senha não atende aos requisitos."
                    );

                    etapaValida = false;

                } else {

                    campoSenha.setCustomValidity("");

                }

                if (!validarConfirmacaoSenha()) {

                    etapaValida = false;

                }

            }

            sections[numero].classList.add(
                "was-validated"
            );


            return etapaValida;

        }

        document
            .querySelectorAll(".next-step")
            .forEach(
                botao => {

                    botao.addEventListener(
                        "click",
                        () => {

                            if (
                                !validarEtapa(etapaAtual)
                            ) {

                                return;

                            }


                            if (
                                etapaAtual <
                                sections.length - 1
                            ) {

                                mostrarEtapa(
                                    etapaAtual + 1
                                );

                            }

                        }
                    );

                }
            );

        document
            .querySelectorAll(".previous-step")
            .forEach(
                botao => {

                    botao.addEventListener(
                        "click",
                        () => {

                            if (etapaAtual > 0) {

                                mostrarEtapa(
                                    etapaAtual - 1
                                );

                            }

                        }
                    );

                }
            );

        form.addEventListener(
            "submit",
            event => {

                event.preventDefault();

                if (!validarEtapa(etapaAtual)) {

                    return;

                }

                for (
                    let i = 0;
                    i < sections.length;
                    i++
                ) {

                    if (!validarEtapa(i)) {

                        mostrarEtapa(i);

                        return;

                    }
                }
                window.location.href = "../usuario/home.html"
            }
        );

        atualizarProgresso();
        atualizarRequisitosSenha();