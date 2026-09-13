# Sistema de Controle de Ordens de Servico - JM Informatica

Projeto desenvolvido como avaliacao tecnica em PHP Puro com MySQL e JavaScript nativo, seguindo arquitetura MVC e padroes de orientacao a objetos.

## Tecnologias e Arquitetura

- **Backend:** PHP 8+ Orientado a Objetos (MVC nativo, sem frameworks)
- **Persistencia:** PDO com MySQL e Prepared Statements
- **Autoload:** PSR-4 nativo via spl_autoload_register (sem Composer)
- **Frontend:** HTML5, CSS3 modular e JavaScript puro (Vanilla JS)
- **Ambiente de Desenvolvimento:** VS Code com extensao PHP Intelephense para analise estatica e organizacao

## Estrutura do Projeto

```text
├── app/
│   ├── Controllers/   # Controladores de fluxo (Auth, Dashboard, Service)
│   ├── Core/          # Nucleo da aplicacao (Autoload, Database, Router, Session, Mailer)
│   ├── Models/        # Regras de negocio e persistencia (User, ServiceOrder)
│   └── Views/         # Templates de visualizacao (auth, dashboard, services, layouts)
├── config/            # Configuracoes gerais e conexao com banco de dados
├── database/          # Script SQL de criacao das tabelas (schema.sql)
├── public/            # Ponto de entrada (index.php, .htaccess e assets CSS/JS)
└── logs/              # Registro de logs de envio de e-mails
```

## Decisoes Arquiteturais e Boas Praticas

1. **Autoload Nativo (Zero Composer):**
   - Para atender a restricao de nao utilizar Composer, foi implementado um autoloader nativo em `app/Core/Autoload.php` com `spl_autoload_register()`, seguindo o padrao PSR-4 (mapeando o namespace `App\` para o diretorio `app/`).

2. **Conexao Singleton (`Database.php`):**
   - A classe de banco de dados utiliza o padrao de projeto Singleton para manter uma unica instancia da conexao PDO aberta durante toda a requisicao, evitando multiplas conexoes desnecessarias ao MySQL.

3. **Seguranca e Tratamento de Dados:**
   - Consultas parametrizadas com Prepared Statements (`bindValue`) em todos os Models para protecao contra SQL Injection.
   - Criptografia de senhas utilizando a funcao nativa `password_hash()` (algoritmo Bcrypt) e autenticacao via `password_verify()`.

4. **Padronizacao e Analise Estatica no Desenvolvimento (VS Code + PHP Intelephense):**
   - Durante o desenvolvimento no editor (VS Code com a extensao PHP Intelephense), foram utilizadas anotacoes de tipo `@var` nos templates de visualizacao (Views). Isso garante que as variaveis injetadas pelos Controllers sejam devidamente reconhecidas pelo linter e analisador estatico do editor, assegurando uma organizacao rigorosa do codigo, autocompletion correto e zero warnings de variaveis indefinidas.

5. **Frontend Modular Sem Frameworks:**
   - O CSS foi dividido por responsabilidade (`base.css`, `auth.css` e `dashboard.css`), unificados pelo `style.css` via `@import`, mantendo o codigo limpo e facil de manter.
   - Mascaras de moeda e interacoes desenvolvidas em JavaScript puro nativo.

## Como Executar o Projeto

### 1. Configuracao do Banco de Dados
1. Importe o arquivo `database/schema.sql` no seu MySQL (via phpMyAdmin, HeidiSQL, DBeaver ou terminal).
2. Se necessario, ajuste as credenciais do banco no arquivo `config/config.php`.

### 2. Executando o Servidor Local

**Opcao A - Servidor embutido do PHP (Recomendado para testes rapidos):**
```bash
php -S localhost:8000 -t public
```
Acesse no navegador: `http://localhost:8000`

**Opcao B - Apache (XAMPP / Laragon):**
Coloque o projeto no diretorio web e acesse pelo caminho correspondente:
http://localhost/Teste_Titan_Rafael_Melo/public

## Credenciais de Acesso para Teste

- **Usuario 1:** jose@jminformatica.com.br | **Senha:** 123456
- **Usuario 2:** maria@jminformatica.com.br | **Senha:** 123456
- **Novo Cadastro:** E possivel cadastrar novos tecnicos diretamente pela tela de login.

## Regras de Negocio Implementadas

1. **Calculo de Comissao:**
   - Servicos ate R$ 1.000,00: 5% de comissao
   - Servicos de R$ 1.000,01 a R$ 10.000,00: 10% de comissao
   - Servicos acima de R$ 10.000,00: 20% de comissao
2. **Finalizacao e Notificacao:**
   - Ao finalizar a OS, calcula a comissao, grava a data de conclusao e registra a notificacao por e-mail em logs/email.log.
