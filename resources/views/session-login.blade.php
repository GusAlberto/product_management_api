<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login temporário</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #0f172a;
            --panel: rgba(15, 23, 42, 0.92);
            --panel-border: rgba(148, 163, 184, 0.2);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --accent: #22c55e;
            --accent-strong: #16a34a;
            --danger: #ef4444;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(34, 197, 94, 0.18), transparent 30%),
                radial-gradient(circle at bottom right, rgba(59, 130, 246, 0.2), transparent 28%),
                var(--bg);
            color: var(--text);
            padding: 24px;
        }

        .card {
            width: min(100%, 720px);
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(14px);
        }

        .eyebrow {
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-size: 12px;
            color: var(--accent);
            margin-bottom: 8px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: clamp(28px, 4vw, 40px);
        }

        p {
            margin: 0 0 20px;
            color: var(--muted);
            line-height: 1.6;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        label {
            display: block;
            font-size: 14px;
            color: var(--text);
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: 14px;
            padding: 14px 16px;
            background: rgba(15, 23, 42, 0.8);
            color: var(--text);
            outline: none;
        }

        input:focus {
            border-color: rgba(34, 197, 94, 0.7);
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15);
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        button, .link {
            border: 0;
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        button {
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: white;
        }

        .link {
            background: rgba(148, 163, 184, 0.12);
            color: var(--text);
        }

        .status {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: rgba(148, 163, 184, 0.1);
            border: 1px solid rgba(148, 163, 184, 0.15);
            white-space: pre-wrap;
        }

        .status.success { border-color: rgba(34, 197, 94, 0.4); }
        .status.error { border-color: rgba(239, 68, 68, 0.5); color: #fecaca; }

        .note {
            margin-top: 16px;
            font-size: 13px;
            color: var(--muted);
        }

        .token {
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 14px;
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.25);
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
            word-break: break-word;
        }

        @media (max-width: 720px) {
            .card { padding: 20px; }
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="eyebrow">Acesso temporário</div>
        <h1>Login no navegador</h1>
        <p>Use esta página para gerar uma sessão autenticada e testar a API sem `curl`.</p>

        <form id="login-form">
            <div class="grid">
                <div>
                    <label for="email">E-mail</label>
                    <input id="email" name="email" type="email" value="admin@example.com" required>
                </div>
                <div>
                    <label for="password">Senha</label>
                    <input id="password" name="password" type="password" value="password" required>
                </div>
            </div>

            <div class="actions">
                <button type="submit">Entrar e testar API</button>
                <a class="link" href="/docs/api" target="_blank" rel="noreferrer">Abrir Swagger</a>
                <a class="link" href="/browser/products" target="_blank" rel="noreferrer">Abrir produtos</a>
            </div>
        </form>

        <div id="status" class="status">Ainda não autenticado.</div>
        <div class="token">
            Token do Swagger: <strong>{{ $swaggerToken }}</strong>
            <br>
            No Swagger, clique em Authorize e use <strong>Bearer {{ $swaggerToken }}</strong>.
        </div>
        <div class="note">Depois do login, a mesma sessão do navegador será usada para acessar /browser/products.</div>
    </main>

    <script>
        const form = document.getElementById('login-form');
        const statusBox = document.getElementById('status');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            statusBox.className = 'status';
            statusBox.textContent = 'Autenticando...';

            const formData = new FormData(form);

            const response = await fetch('/session-login', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: new URLSearchParams(formData),
            });

            const payload = await response.json();

            if (!response.ok) {
                statusBox.className = 'status error';
                statusBox.textContent = payload.message ?? 'Falha ao autenticar.';
                return;
            }

            const apiResponse = await fetch('/browser/products', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            const apiPayload = await apiResponse.json();

            statusBox.className = 'status success';
            statusBox.textContent = [
                payload.message,
                '',
                'Resposta da API protegida:',
                JSON.stringify(apiPayload, null, 2),
            ].join('\n');
        });
    </script>
</body>
</html>