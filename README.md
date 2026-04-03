# 📅 Sistema de Agendamentos

<p align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" />
</p>

Sistema administrativo para gerenciamento de agendamentos desenvolvido em **PHP** com banco de dados **MySQL/MariaDB**, oferecendo controle centralizado de agendamentos, usuários e serviços em um painel administrativo responsivo.

---

## 🚀 Visão Geral

O sistema permite:
- ✔ Cadastro e gerenciamento de agendamentos
- ✔ Controle de serviços oferecidos e usuários
- ✔ Painel administrativo responsivo com controle de sessões
- ✔ Indicadores de agendamentos diários, semanais e mensais

🌐 **Acesse o Projeto Online:** [Link do Render](https://sistema-agendamento-fm7r.onrender.com)

---

## 📸 Prévia do Projeto

<table align="center">
  <tr>
    <td align="center"><b>Dashboard Principal</b><br><img src="https://github.com/user-attachments/assets/46a5b1dd-c5a3-47d7-b900-b64eba829c6c" width="400px"></td>
    <td align="center"><b>Lista de Agendamentos</b><br><img src="https://github.com/user-attachments/assets/c46a7588-0ff1-4ca1-8c60-55dda997d6cf" width="400px"></td>
  </tr>
  <tr>
    <td align="center"><b>Gerenciamento</b><br><img src="https://github.com/user-attachments/assets/f403151b-edaf-4bb7-8a80-a0f90cbe4c0c" width="400px"></td>
    <td align="center"><b>Interface de Usuários</b><br><img src="https://github.com/user-attachments/assets/9445f24d-dd73-4c0c-a161-ffc332b4fed2" width="400px"></td>
  </tr>
  <tr>
    <td align="center"><b>Configurações</b><br><img src="https://github.com/user-attachments/assets/83f42769-90ca-4fb4-99d9-477cc27920d8" width="400px"></td>
    <td align="center"><b>Tela de Login</b><br><img src="https://github.com/user-attachments/assets/17dd49c7-e383-49a0-8df7-88a2708a9cab" width="400px"></td>
  </tr>
</table>

---

## 🛠 Tecnologias Utilizadas

- **PHP 7+** (Lógica de servidor)
- **MySQL / MariaDB** (Persistência de dados)
- **PDO** (Comunicação segura e Prevenção contra SQL Injection)
- **HTML5 / CSS3 / JavaScript** (Interface e Interatividade)
- **Font Awesome** (Ícones)

---

## 🔐 Segurança

O projeto prioriza a integridade dos dados através de:
- Conexões seguras via **PDO**.
- Controle rigoroso de sessão para áreas autenticadas.
- Arquivos sensíveis (`conexao.php`, `.env`, `login.php`) protegidos via `.gitignore`.
- Uso de arquivos modelo (`*.example.php`) para facilitar o setup sem expor credenciais.

---

## ⚙️ Instalação Local

1. **Clonar o repositório:**
   ```bash
   git clone [https://github.com/awaldige/sistema_agendamento.git](https://github.com/awaldige/sistema_agendamento.git)
Banco de Dados: Importe o arquivo database/sistema_agendamentos.sql no seu MySQL.

Configuração: Renomeie os arquivos .example.php para .php e insira suas credenciais locais.

Executar: Acesse via http://localhost/sistema_agendamento/.

📈 Melhorias Futuras
[ ] Painel analítico com gráficos avançados.

[ ] API REST para integrações externas.

[ ] Sistema de notificações por E-mail/WhatsApp.

[ ] Integração com Google Calendar.

👨‍💻 Autor
André Waldige - Full Stack Developer

GitHub:(https://github.com/awaldige)

LinkedIn: [https://www.linkedin.com/in/andre-waldige-dev/]
