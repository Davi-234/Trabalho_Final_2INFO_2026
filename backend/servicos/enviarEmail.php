<?php

require_once __DIR__ . '/../configuracao/ambiente.php';

class SendPulseEmailService
{
	private const API_BASE_URL = 'https://api.sendpulse.com';
	private string $clientId;
	private string $clientSecret;
	private string $remetente;
	private string $nomeRemetente;

	public function __construct()
	{
		carregarAmbiente();
		$this->clientId = (string) getenv('SENDPULSE_CLIENT_ID');
		$this->clientSecret = (string) getenv('SENDPULSE_CLIENT_SECRET');
		$this->remetente = (string) getenv('DEVSHARE_MAIL_FROM');
		$this->nomeRemetente = (string) (getenv('DEVSHARE_MAIL_FROM_NAME') ?: 'DevShare');

		if (
			$this->clientId === '' || $this->clientSecret === '' ||
			!filter_var($this->remetente, FILTER_VALIDATE_EMAIL)
		) {
			throw new RuntimeException('Configuração da API SendPulse incompleta.');
		}
	}

	public function enviarRedefinicaoSenha(string $destinatario, string $link): void
	{
		$autenticacao = $this->solicitarToken();
		$token = $autenticacao['access_token'] ?? null;
		if (!is_string($token) || $token === '') {
			throw new RuntimeException('A API SendPulse não retornou um token de acesso.');
		}

		$linkSeguro = htmlspecialchars($link, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$html = <<<HTML
			<!doctype html>
			<html lang="pt-BR">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Redefinição de senha - DevShare</title>
			</head>
			<body style="margin:0;padding:0;background-color:#0d1117;font-family:Arial,Helvetica,sans-serif;color:#e6edf3;">
				<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#0d1117;padding:36px 12px;">
					<tr>
						<td align="center">
							<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background-color:#161b22;border:1px solid #30363d;border-radius:12px;overflow:hidden;">
								<tr>
									<td style="padding:24px 32px;border-bottom:1px solid #30363d;">
										<p style="margin:0;color:#58a6ff;font-size:22px;font-weight:bold;letter-spacing:0.3px;">DevShare</p>
										<p style="margin:6px 0 0;color:#8b949e;font-size:13px;">Compartilhe, avalie e evolua projetos</p>
									</td>
								</tr>
								<tr>
									<td style="padding:32px;">
										<p style="margin:0 0 10px;color:#58a6ff;font-size:13px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;">Segurança da conta</p>
										<h1 style="margin:0 0 18px;color:#e6edf3;font-size:25px;line-height:1.3;">Redefina sua senha</h1>
										<p style="margin:0 0 14px;color:#c9d1d9;font-size:15px;line-height:1.7;">Recebemos uma solicitação para criar uma nova senha para sua conta DevShare.</p>
										<p style="margin:0 0 26px;color:#c9d1d9;font-size:15px;line-height:1.7;">Clique no botão abaixo para continuar. O link é temporário e pode ser usado apenas uma vez.</p>
										<table role="presentation" cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td align="center" style="border-radius:7px;background-color:#1f6feb;">
													<a href="{$linkSeguro}" style="display:inline-block;padding:13px 22px;border:1px solid #58a6ff;border-radius:7px;color:#ffffff;font-size:15px;font-weight:bold;text-decoration:none;">Redefinir minha senha</a>
												</td>
											</tr>
										</table>
										<p style="margin:26px 0 0;color:#8b949e;font-size:13px;line-height:1.6;">Se o botão não funcionar, copie este endereço e cole no navegador:</p>
										<p style="margin:8px 0 0;overflow-wrap:anywhere;font-size:12px;line-height:1.6;"><a href="{$linkSeguro}" style="color:#58a6ff;text-decoration:underline;">{$linkSeguro}</a></p>
									</td>
								</tr>
								<tr>
									<td style="padding:20px 32px;border-top:1px solid #30363d;">
										<p style="margin:0;color:#8b949e;font-size:13px;line-height:1.6;">Se você não solicitou a redefinição, ignore esta mensagem. Sua senha não será alterada.</p>
									</td>
								</tr>
							</table>
							<p style="margin:18px 0 0;color:#8b949e;font-size:12px;">DevShare · Compartilhe ideias, encontre parceiros e transforme projetos em realidade.</p>
						</td>
					</tr>
				</table>
			</body>
			</html>
			HTML;
		$texto = "Redefinição de senha - DevShare\n\n"
			. "Recebemos uma solicitação para redefinir a senha da sua conta.\n"
			. "Acesse este link para escolher uma nova senha:\n{$link}\n\n"
			. "Se você não solicitou a redefinição, ignore esta mensagem.";

		$this->solicitar('/smtp/emails', [
			'Authorization: Bearer ' . $token,
		], [
			'email' => [
				'subject' => 'Redefinição de senha - DevShare',
				'from' => [
					'name' => $this->nomeRemetente,
					'email' => $this->remetente,
				],
				'to' => [[
					'email' => $destinatario,
				]],
				'html' => base64_encode($html),
				'text' => $texto,
			],
		]);
	}

	private function solicitarToken(): array
	{
		return $this->solicitar('/oauth/access_token', [], [
			'grant_type' => 'client_credentials',
			'client_id' => $this->clientId,
			'client_secret' => $this->clientSecret,
		]);
	}

	private function solicitar(string $caminho, array $cabecalhosAdicionais, array $dados): array
	{
		$cabecalhos = array_merge([
			'Content-Type: application/json',
			'Accept: application/json',
		], $cabecalhosAdicionais);
		$contexto = stream_context_create([
			'http' => [
				'method' => 'POST',
				'header' => implode("\r\n", $cabecalhos),
				'content' => json_encode($dados, JSON_THROW_ON_ERROR),
				'timeout' => 15,
				'ignore_errors' => true,
			],
		]);

		$resposta = @file_get_contents(self::API_BASE_URL . $caminho, false, $contexto);
		$cabecalhosResposta = $http_response_header ?? [];
		preg_match('/\s([0-9]{3})\s/', $cabecalhosResposta[0] ?? '', $correspondenciaStatus);
		$status = isset($correspondenciaStatus[1]) ? (int) $correspondenciaStatus[1] : 0;
		$dadosResposta = is_string($resposta) ? json_decode($resposta, true) : null;

		if ($status < 200 || $status >= 300 || !is_array($dadosResposta)) {
			$detalhe = is_array($dadosResposta)
				? ($dadosResposta['error_description'] ?? $dadosResposta['message'] ?? $dadosResposta['error'] ?? '')
				: '';
			if (!is_string($detalhe)) {
				$detalhe = '';
			}
			error_log(
				'Falha na API SendPulse; HTTP ' . $status .
				($detalhe !== '' ? '; detalhe: ' . substr($detalhe, 0, 300) : '') . '.'
			);
			throw new RuntimeException('Não foi possível enviar o e-mail pela SendPulse.');
		}

		if (($dadosResposta['result'] ?? true) === false) {
			$detalhe = $dadosResposta['message'] ?? $dadosResposta['error'] ?? '';
			error_log(
				'A API SendPulse recusou o envio do e-mail.' .
				(is_string($detalhe) && $detalhe !== '' ? ' Detalhe: ' . substr($detalhe, 0, 300) : '')
			);
			throw new RuntimeException('A API SendPulse recusou o envio do e-mail.');
		}

		return $dadosResposta;
	}
}