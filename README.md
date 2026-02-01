# 📅 Sistema de Agendamentos

Sistema administrativo para gerenciamento de agendamentos desenvolvido em **PHP** com banco de dados **MySQL/MariaDB**, oferecendo controle centralizado de agendamentos, usuários e serviços em um painel administrativo responsivo.

O projeto foi concebido para ambientes que necessitam organização operacional eficiente, fornecendo indicadores e ferramentas de gestão para o uso diário.

---

## 🚀 Visão Geral

O sistema permite:

- ✔ Cadastro e gerenciamento de agendamentos
- ✔ Controle de serviços oferecidos
- ✔ Gerenciamento de usuários
- ✔ Painel administrativo responsivo
- ✔ Controle de sessões e autenticação
- ✔ Indicadores de agendamentos diários, semanais e mensais
- ✔ Interface leve e fácil de usar

O foco do projeto é oferecer uma solução prática para pequenas e médias operações que necessitam controle organizado de atendimentos e serviços.

---

## 🛠 Tecnologias Utilizadas

- PHP 7+
- MySQL / MariaDB
- PDO para comunicação segura com banco de dados
- HTML5
- CSS3
- JavaScript
- Font Awesome

---

## 🔐 Segurança

O projeto utiliza:

- Conexões seguras via PDO
- Prevenção contra SQL Injection
- Controle de sessão para usuários autenticados
- Exclusão de arquivos sensíveis do repositório público
- Uso de arquivos modelo (`*.example.php`) para configuração local

Arquivos protegidos via `.gitignore`:

- `conexao.php`
- `login.php`
- `criar_admin.php`
- `.env`

Isso evita exposição de credenciais e facilita colaboração segura.

---

## ⚙️ Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/awaldige/sistema_agendamento.git
2. Criar banco de dados
Crie um banco chamado:

sistema_agendamentos
e importe o arquivo .sql do projeto.

3. Configurar arquivos privados
Configure manualmente:

conexao.php

login.php

criar_admin.php

Utilize os arquivos .example.php como modelo.

4. Executar o sistema
Acesse:

http://localhost/sistema_agendamento/
ou conforme configuração do servidor.

📂 Estrutura do Projeto
Principais módulos:

/sistema_agendamento
│
├── index.php
├── agendamentos.php
├── servicos.php
├── usuarios.php
│
├── css/
│   ├── style.css
│   └── login.css
│
├── js/
│   └── script.js
│
└── database/
    └── sistema_agendamentos.sql
A arquitetura permite expansão futura com relatórios, integrações e novos módulos administrativos.

📈 Possíveis Melhorias Futuras
Painel analítico avançado

API REST para integração externa

Sistema de notificações

Relatórios exportáveis

Controle de permissões por perfil

Integração com agenda externa

👨‍💻 Autor
André Waldige
GitHub: https://github.com/awaldige

⭐ Considerações
Projeto voltado para aprendizado e aplicações práticas de gerenciamento administrativo, podendo ser expandido para ambientes corporativos conforme necessidade.
