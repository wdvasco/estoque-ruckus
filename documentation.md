## 🎯 Objetivo do Prompt

Você é um desenvolvedor especializado em PHP e MySQL.  
Crie um sistema web completo utilizando **PHP puro** (sem frameworks) e **MySQL**, com foco em controle de estoque.  
O sistema deve começar com o upload de uma planilha Excel, permitir o cadastro e login de usuários e oferecer operações de CRUD para os itens do estoque.

---

## 🔐 Módulo de Autenticação de Usuários

- Página inicial com formulário de **cadastro de usuário**:
  - Campos: nome, e-mail, senha
- Após cadastro, redirecionar para página de login
- Página de **login** com verificação de credenciais
- Utilizar **sessões** para manter o usuário logado
- Redirecionar usuário autenticado para o painel principal do sistema de estoque

---

## 📦 Módulo de Controle de Estoque (CRUD)

O sistema deve permitir o gerenciamento completo dos itens em estoque por meio das seguintes operações:

### 🔹 Funcionalidades obrigatórias

- **Cadastrar novo item** no estoque
- **Listar todos os itens** cadastrados, com paginação
- **Editar item existente**
- **Excluir item do estoque**

### 🔹 Detalhes da implementação

- Cada item listado na tabela deve conter:
  - Um botão **Editar**
  - Um botão **Excluir**
- O formulário de cadastro e edição deve conter os campos baseados nas colunas da planilha importada.  
  Exemplo de campos:

{APMAC,	APName,	Model, Serial, Status, Location, Inclusao, Obs}



---

## 📥 Importação Inicial de Planilha Excel

- O sistema **deve iniciar** com a importação de uma planilha Excel (`.xlsx` ou `.csv`)
- Essa planilha conterá os dados iniciais do estoque
- Cada coluna da planilha representa um campo da tabela `estoque`, por exemplo:

 {APMAC,	APName,	Model, Serial, Status, Location, Inclusao, Obs}
