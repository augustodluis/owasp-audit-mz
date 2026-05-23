# Como contribuir para o OWASP-AUDIT-MZ

Obrigado pelo interesse em contribuir. Este documento descreve o fluxo de trabalho, padrões de código e processo de revisão.

## Fluxo Git resumido

```text
            main (produção, protegido)
              ↑
              │  PR + revisão do mantenedor
              │
            develop (integração)
              ↑
              │  PR
              │
        feature/<descricao>  bugfix/<descricao>  docs/<descricao>
```

- `main` é o ramo estável, protegido, só recebe merge por **Pull Request** aprovado pelo mantenedor.
- `develop` é o ramo de integração contínua. Todas as features começam a partir daqui.
- Cada nova alteração vive num ramo dedicado (`feature/...`, `bugfix/...`, `docs/...`).

## Mantenedor

O mantenedor único do projecto é **Augusto Domingos Luís** ([@augustodomingosluis](https://github.com/augustodomingosluis)). Nenhum PR entra em `main` sem a sua aprovação explícita. A regra está descrita em `.github/CODEOWNERS` e nas *branch protection rules* do GitHub (ver secção seguinte).

## Política de Pull Request

1. **Fork** ou crie um ramo a partir de `develop`.
2. **Commit** com mensagens claras (Conventional Commits). Exemplos:
   - `feat(scanner): adiciona deteccao de open redirect`
   - `fix(crawler): ignora links com esquema mailto`
   - `docs(readme): actualiza instrucoes de SMTP`
3. **Push** para o seu fork ou ramo.
4. Abra um **Pull Request** apontando para `develop` (nunca directamente para `main`).
5. O **CI** corre automaticamente. O PR só pode ser aprovado se todos os jobs passarem.
6. O mantenedor revê, comenta, e **aprova** ou pede alterações.
7. Após aprovação, o mantenedor faz o merge (squash and merge é a estratégia preferida).
8. Releases para `main` ocorrem por PR `develop` → `main`, com tag semântica (v1.0.0, v1.1.0...).

## Branch protection rules (configurar no GitHub)

No repositório, em **Settings → Branches**, criar regras para `main` e `develop`:

- ✅ Require a pull request before merging
- ✅ Require approvals: **1** (do mantenedor)
- ✅ Dismiss stale pull request approvals when new commits are pushed
- ✅ Require review from Code Owners
- ✅ Require status checks to pass before merging
  - Selecionar o workflow `CI` definido em `.github/workflows/ci.yml`
- ✅ Require conversation resolution before merging
- ✅ Do not allow bypassing the above settings

## Padrões de código

### PHP

- Seguir o standard **PSR-12**.
- Tipagem estrita sempre que possível (`declare(strict_types=1)` é opcional mas recomendado em novo código).
- Nomes de classes em `PascalCase`, métodos e variáveis em `camelCase`.
- Cada classe num único ficheiro com o mesmo nome.
- Evitar `try/catch` vazio. Capturar e registar via `ErrorMonitoringService`.

Formatação automática com [Laravel Pint](https://laravel.com/docs/10.x/pint):

```bash
./vendor/bin/pint
```

### Blade

- Sem lógica complexa nas views. Toda a lógica vai para o controlador ou view composer.
- Sempre `{{ }}` (escape automático). Nunca `{!! !!}` sem justificação documentada.
- Sempre `@csrf` em formulários POST/PUT/PATCH/DELETE.

### JavaScript

- ES6+ com módulos quando aplicável.
- Sem dependências externas além de Bootstrap e Notification API.
- Manter `notifications.js` e `audit.js` enxutos.

### SQL e migrações

- Cada migração deve ter `up()` e `down()` reversíveis.
- Sempre indexar foreign keys e colunas usadas em `where`.
- Usar `ENUM` apenas para valores estáveis (estado, papel, severidade).

## Adicionar um novo Vulnerability Check

1. Criar `app/Checks/MeuCheck.php` que implementa `App\Checks\Contracts\VulnerabilityCheck`.
2. Registar a classe em `config/audit.php`, secção `checks`.
3. Preencher sempre `owasp_category`, `evidence`, `bad_example`, `good_example`, `reference` e `cwe_id` nas vulnerabilidades devolvidas.
4. Adicionar teste em `tests/Feature/Checks/MeuCheckTest.php`.

## Como correr os testes

```bash
php artisan test
# ou
./vendor/bin/phpunit
```

## Política de segurança

Se descobrir uma vulnerabilidade na própria plataforma OWASP-AUDIT-MZ, **não abra um issue público**. Envie um e-mail privado ao mantenedor (`augusto.domingos.luis@gmail.com`) com:

- Versão afectada
- Passos para reproduzir
- Impacto estimado

A resposta inicial é dada em até 72 horas.

## Código de conduta

Discussões respeitosas. Críticas técnicas, nunca pessoais. Decisão final do mantenedor é soberana.

---

Obrigado por contribuir.
