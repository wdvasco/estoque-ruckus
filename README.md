# 🎯 Sistema de Controle de Estoque Ruckus

Sistema web completo para gerenciamento de estoque de Access Points Ruckus, desenvolvido em **PHP puro** com **MySQL**.

## 🚀 Funcionalidades

### 🔐 Autenticação
- Cadastro de usuários
- Login com sessões seguras
- Controle de acesso

### 📦 Gestão de Estoque (CRUD)
- ✅ **Criar**: Cadastrar novos Access Points
- 📖 **Listar**: Visualizar todos os itens com paginação
- ✏️ **Editar**: Modificar dados dos equipamentos
- 🗑️ **Excluir**: Remover itens do estoque
- 🔍 **Buscar**: Pesquisar por MAC, nome, modelo, serial ou localização

### 📥 Importação de Dados
- Upload de planilhas CSV
- Template para download
- Validação automática de dados
- Relatório de importação com estatísticas

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 7.4+ (puro, sem frameworks)
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Ícones**: Font Awesome 6

## 📋 Requisitos do Sistema

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL

## 🔧 Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/seu-usuario/estoque-ruckus.git
cd estoque-ruckus
```

### 2. Configure o banco de dados
1. Crie um banco MySQL
2. Execute o script `database/schema.sql`
3. Edite `config/database.php` com suas credenciais

### 3. Configure o servidor web
Aponte o document root para a pasta do projeto.

### 4. Acesse o sistema
- URL: `http://localhost/estoque-ruckus`
- Usuário teste: `admin@estoque.com`
- Senha teste: `admin123`

## 📁 Estrutura do Projeto

```
estoque-ruckus/
├── auth/                   # Módulo de autenticação
│   ├── login.php          # Página de login
│   ├── login_process.php  # Processamento do login
│   ├── register.php       # Processamento do cadastro
│   └── logout.php         # Logout
├── classes/               # Classes PHP
│   ├── User.php          # Gerenciamento de usuários
│   └── Inventory.php     # Gerenciamento do estoque
├── config/               # Configurações
│   └── database.php     # Configuração do banco
├── dashboard/           # Painel principal
│   ├── index.php       # Listagem de itens
│   ├── add_item.php    # Adicionar item
│   ├── edit_item.php   # Editar item
│   ├── delete_item.php # Excluir item
│   ├── import.php      # Importar planilha
│   └── ...
├── database/           # Scripts do banco
│   └── schema.sql     # Estrutura das tabelas
├── index.php          # Página inicial (cadastro)
└── README.md          # Este arquivo
```

## 🗃️ Estrutura do Banco de Dados

### Tabela `users`
- `id` - ID único do usuário
- `name` - Nome completo
- `email` - E-mail (único)
- `password` - Senha criptografada
- `created_at` - Data de criação
- `updated_at` - Data de atualização

### Tabela `estoque`
- `id` - ID único do item
- `apmac` - MAC Address do AP (único)
- `apname` - Nome do Access Point
- `model` - Modelo do equipamento
- `serial` - Número serial (único)
- `status` - Status operacional (Active, Inactive, Maintenance, Retired)
- `location` - Localização física
- `inclusao` - Data de inclusão no estoque
- `obs` - Observações adicionais
- `created_at` - Data de criação
- `updated_at` - Data de atualização

## 📊 Formato da Planilha de Importação

### Colunas Obrigatórias (CSV)
| Coluna | Descrição | Exemplo |
|--------|-----------|---------|
| APMAC | MAC Address | 00:11:22:33:44:55 |
| APName | Nome do AP | AP-LOBBY-01 |
| Model | Modelo | R750 |
| Serial | Número Serial | RK7501234567 |
| Status | Status | Active |
| Location | Localização | Main Lobby - Floor 1 |
| Inclusao | Data | 2024-01-15 |
| Obs | Observações | Observações opcionais |

### Modelos Suportados
- R750, R650, R550, R350, R320
- T750, T350
- H550, H350

## 🔒 Segurança

- Senhas criptografadas com `password_hash()`
- Prepared statements para prevenir SQL injection
- Validação de entrada em todos os formulários
- Controle de sessões seguro
- Verificação de autenticação em todas as páginas protegidas

## 🎨 Interface

- Design responsivo com Bootstrap 5
- Interface moderna e intuitiva
- Ícones Font Awesome
- Gradientes e animações CSS
- Compatível com dispositivos móveis

## 📝 Uso do Sistema

### 1. Primeiro Acesso
1. Acesse `index.php`
2. Cadastre um novo usuário
3. Faça login no sistema

### 2. Gerenciar Estoque
- **Listar**: Visualize todos os itens na página principal
- **Adicionar**: Use o botão "Novo Item"
- **Editar**: Clique em "Editar" na linha do item
- **Excluir**: Clique em "Excluir" (com confirmação)
- **Buscar**: Use a barra de pesquisa

### 3. Importar Dados
1. Acesse "Importar Excel"
2. Baixe o template CSV
3. Preencha com seus dados
4. Faça upload do arquivo
5. Verifique o relatório de importação

## 🐛 Solução de Problemas

### Erro de Conexão com Banco
- Verifique as credenciais em `config/database.php`
- Certifique-se que o MySQL está rodando
- Confirme se o banco foi criado

### Erro de Upload
- Verifique as permissões da pasta
- Confirme o tamanho máximo de upload no PHP
- Use apenas arquivos CSV

### Problemas de Sessão
- Verifique se o PHP pode escrever na pasta de sessões
- Confirme as configurações de sessão no PHP

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique a documentação
2. Consulte os logs de erro do PHP
3. Abra uma issue no repositório

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

---

**Desenvolvido com ❤️ para gerenciamento eficiente de Access Points Ruckus**
