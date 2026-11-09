// --------------------------- Calculo de Entropia --------------------------- //
function tempo_quebra_senha(entropia) {
  const tempoSegundos = Math.pow(2, entropia - 1) / 100;

  return {
    segundos: tempoSegundos,
    minutos: tempoSegundos / 60,
    horas: tempoSegundos / 3600,
    dias: tempoSegundos / 86400,
    anos: tempoSegundos / (86400 * 365.25),
  };
}

function calculo_entropia(senha) {
  let l = senha.length; // Tamanho da senha
  let r = 0; // Número de variações da string (soma da quantidade de tipos de caracteres).

  if (/[A-Z]/.test(senha)) {
    r += 26;
  }

  if (/[a-z]/.test(senha)) {
    r += 26;
  }

  if (/\d/.test(senha)) {
    r += 10;
  }

  if (/[^a-zA-Z0-9]/.test(senha)) {
    r += 33; // símbolos ASCII imprimíveis
  }

  return l * Math.log2(r);
}
// --------------------------------------------------------------------------- //

// --------------------------- Validação de requisitos --------------------------- //
function validar_requisitos_senha(senha) {
  return {
        tem_letras_maiusculas: /[A-Z]/.test(senha),
        tem_letras_minusculas: /[a-z]/.test(senha),
        tem_numeros: /\d/.test(senha),
        tem_caracteres_especiais: /[^a-zA-Z0-9]/.test(senha),
        tamanho_minimo: senha.length >= 8
    };
}
// --------------------------------------------------------------------------- //

function validar_senha(senha) {
  let entropia_senha = calculo_entropia(senha);

  let requisitos_senha = validar_requisitos_senha(senha);

  let tempoQuebra = tempo_quebra_senha(entropia_senha);

  return {
    tempo_quebra_senha:
      senha.length >= 8 ? tempoQuebra : null,
    requisitos_senha
  };
}
