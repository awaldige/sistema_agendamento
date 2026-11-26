# 📅 AgendaPro
### Sistema Inteligente de Gestão de Agendamentos

<p align="center">
  Uma solução web moderna para organização de agendamentos, gerenciamento de serviços e controle administrativo.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Next.js-16.4.6-000000?style=for-the-badge&logo=next.js" />
  <img src="https://img.shields.io/badge/React-19.3-20232A?style=for-the-badge&logo=react" />
  <img src="https://img.shields.io/badge/TypeScript-5-3178C6?style=for-the-badge&logo=typescript" />
  <img src="https://img.shields.io/badge/Prisma-7.10-2D3748?style=for-the-badge&logo=prisma" />
  <img src="https://img.shields.io/badge/PostgreSQL-Database-4169E1?style=for-the-badge&logo=postgresql" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-4.3-06B6D4?style=for-the-badge&logo=tailwindcss" />
</p>

---

## 🎯 Sobre o projeto

O **AgendaPro** é uma aplicação web desenvolvida para simplificar a gestão de agendamentos e serviços, oferecendo uma experiência administrativa intuitiva, organizada e responsiva.

A solução centraliza as principais operações em um único ambiente, permitindo acompanhar agendamentos, administrar serviços e controlar o acesso às funcionalidades conforme o perfil do usuário.

Desenvolvido com tecnologias modernas do ecossistema JavaScript e TypeScript, o projeto utiliza uma arquitetura baseada em Next.js, APIs integradas e persistência de dados em PostgreSQL.

## ✨ Principais funcionalidades

| Módulo | Recursos |
|---|---|
| Dashboard | Visão geral e indicadores administrativos |
| Agendamentos | Consulta, cadastro, edição e exclusão conforme permissões |
| Serviços | Cadastro, consulta, edição e gerenciamento |
| Usuários | Área dedicada a informações de usuários |
| Configurações | Área de configurações do sistema |
| Autenticação | Login, sessão e encerramento de sessão |
| Controle de acesso | Permissões distintas para administrador e visitante |

## 🔐 Controle de acesso

O sistema possui dois níveis de acesso, com permissões verificadas tanto na aplicação quanto nas APIs.

| Operação | Administrador | Visitante |
|---|:---:|:---:|
| Acessar o sistema | ✓ | ✓ |
| Visualizar dashboard | ✓ | ✓ |
| Consultar agendamentos | ✓ | ✓ |
| Criar agendamentos | ✓ | — |
| Editar agendamentos | ✓ | — |
| Excluir agendamentos | ✓ | — |
| Consultar serviços | ✓ | ✓ |
| Cadastrar serviços | ✓ | — |
| Editar serviços | ✓ | — |
| Excluir serviços | ✓ | — |

**Administrador:** acesso completo às operações administrativas.

**Visitante:** acesso demonstrativo em modo de consulta, sem permissão para modificar os registros.

## 🖥️ Demonstração visual

As imagens abaixo serão atualizadas com capturas da versão atual do AgendaPro.

| Dashboard | Agendamentos |
|---|---|
| *Adicionar captura atual* | *Adicionar captura atual* |

| Serviços | Autenticação |
|---|---|
| *Adicionar captura atual* | *Adicionar captura atual* |

## 🧰 Tecnologias e ferramentas

**Frontend**
- Next.js 16
- React 19
- TypeScript
- Tailwind CSS 4
- Lucide React

**Backend e autenticação**
- Next.js Route Handlers
- JOSE — geração e validação de JWT
- Cookies HTTP-only
- APIs REST para operações de agendamentos e serviços

**Banco de dados**
- PostgreSQL
- Prisma ORM 7
- Prisma PostgreSQL Adapter
- Node PostgreSQL (`pg`)

## 🗄️ Modelagem de dados

A persistência utiliza PostgreSQL, com duas entidades principais:

**Serviços**
- Identificação e nome
- Descrição
- Duração em minutos
- Data de criação

**Agendamentos**
- Identificação do paciente
- E-mail e telefone
- Data e horário
- Serviço associado
- Tipo de agendamento
- Observações
- Data de criação

Os agendamentos possuem relacionamento com os serviços, com integridade referencial e índices para consultas por data, horário e serviço.

As migrações do banco estão versionadas em `frontend/prisma/migrations`.

## 🏗️ Organização do projeto

```text
sistema-agendamento-next/
│
└── frontend/
    ├── prisma/
    │   ├── migrations/
    │   └── schema.prisma
    │
    ├── src/
    │   ├── app/
    │   │   ├── agendamentos/
    │   │   ├── api/
    │   │   ├── configuracoes/
    │   │   ├── login/
    │   │   ├── servicos/
    │   │   └── usuarios/
    │   │
    │   ├── components/
    │   ├── lib/
    │   └── proxy.ts
    │
    ├── package.json
    └── ...
```

## ⚙️ Instalação e execução

### Pré-requisitos

- Node.js
- npm
- PostgreSQL

### 1. Clonar o repositório

```bash
git clone URL_DO_REPOSITORIO
cd sistema-agendamento-next/frontend
```

### 2. Instalar as dependências

```bash
npm install
```

### 3. Configurar as variáveis de ambiente

Criar um arquivo `.env` na pasta `frontend`:

```env
DATABASE_URL="SUA_URL_POSTGRESQL"

ADMIN_USERNAME="seu_usuario_admin"
ADMIN_PASSWORD="sua_senha_admin"

DEMO_USERNAME="visitante"
DEMO_PASSWORD="sua_senha_visitante"

AUTH_SECRET="seu_segredo_jwt"
```

As credenciais devem ser definidas pelo responsável pela instalação. O segredo JWT deve possuir pelo menos 32 caracteres.

### 4. Preparar o banco de dados

Configure a conexão PostgreSQL e aplique as migrações existentes do Prisma antes de iniciar a aplicação.

### 5. Executar em desenvolvimento

```bash
npm run dev
```

A aplicação estará disponível em:

```text
http://localhost:3000
```

### 6. Gerar a versão de produção

```bash
npm run build
npm run start
```

## 🛡️ Segurança e integridade

O AgendaPro utiliza mecanismos de proteção para controlar o acesso aos recursos da aplicação:

- Autenticação baseada em JWT.
- Cookies de sessão HTTP-only.
- Validação de sessão nas rotas protegidas.
- Verificação de permissões nas operações administrativas.
- Proteção das APIs contra operações não autorizadas.
- Credenciais e segredos armazenados em variáveis de ambiente.
- Integridade referencial entre agendamentos e serviços.

## 🚀 Evoluções planejadas

- [ ] Relatórios gerenciais e gráficos analíticos.
- [ ] Filtros avançados para consultas.
- [ ] Notificações automáticas de agendamentos.
- [ ] Integração com Google Calendar.
- [ ] Aprimoramentos na experiência administrativa.

---

## 👨‍💻 Desenvolvimento

**Projeto desenvolvido por André Waldige**

**AW TECHNOLOGY — Desenvolvimento de Software**

Portfólio: https://andre-waldige.vercel.app/

GitHub: https://github.com/awaldige

LinkedIn: https://www.linkedin.com/in/andre-waldige-dev/

<p align="center">
  <strong>AW TECHNOLOGY</strong><br/>
  Soluções inteligentes em software.
</p>