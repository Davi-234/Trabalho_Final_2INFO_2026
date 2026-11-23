<?php

class SendPulseEmailService
{
	private const API_BASE_URL = 'https://api.sendpulse.com';
	private string $clientId;
	private string $clientSecret;
	private string $remetente;
	private string $nomeRemetente;

	public function __construct()
	{
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
				'html' => '
					<div style="font-family: Arial, sans-serif;">
						<h2 style="color: #333;">Redefinição de senha</h2>
						<p>Acesse o link abaixo para redefinir sua senha:</p>
						<a href="' . htmlspecialchars($link) . '" 
						style="background: #2563eb; color: white; padding: 12px 20px; text-decoration: none;">
							Redefinir senha
						</a>
					</div>
				',
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
			error_log('Falha na API SendPulse; HTTP ' . $status . '.');
			throw new RuntimeException('Não foi possível enviar o e-mail pela SendPulse.');
		}

		if (($dadosResposta['result'] ?? true) === false) {
			error_log('A API SendPulse recusou o envio do e-mail.');
			throw new RuntimeException('A API SendPulse recusou o envio do e-mail.');
		}

		return $dadosResposta;
	}
}