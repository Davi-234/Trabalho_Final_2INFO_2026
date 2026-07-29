function criar_tela_erro(text) {
  const tela_error = $(`
            <div id="tela_falta_dados">
              <div>
                <strong>${text}</strong>
              </div>
            </div>
        `);

  $("body").append(tela_error);
}
