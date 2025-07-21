---
trigger: manual
---

## 🧾 Regras de Desenvolvimento da Aplicação

A seguir estão definidas as **regras de negócio**, **regras de funcionamento** e **regras técnicas** que devem ser seguidas durante o desenvolvimento da aplicação web de controle de estoque.

---

### 📌 Regras de Negócio

1. O sistema deve iniciar com o **upload obrigatório de uma planilha Excel (.xlsx ou .csv)** com os dados iniciais do estoque.
2. **Campos vazios na planilha são permitidos** e não devem impedir a importação.
3. Após a importação da planilha, os dados devem ser inseridos automaticamente na tabela `estoque` do banco de dados.
4. O sistema deve permitir o **cadastro de novos itens manualmente**, além dos que foram importados.
5. Os usuários devem estar **logados para acessar o painel de controle de estoque**.
6. Cada usuário poderá visualizar e gerenciar o estoque após login, mas o sistema será de acesso único (não multitenant).
7. Todos os dados devem ser armazenados com integridade no banco de dados.
8. O sistema deve permitir atualização e exclusão de itens já cadastrados.

---

### 🔐 Regras de Segurança

1. Todas as senhas devem ser armazenadas usando `password_hash()` (algoritmo seguro).
2. As sessões devem ser utilizadas para controle de acesso. Sem login, o usuário não deve acessar nenhuma funcionalidade do sistema.
3. As entradas do usuário devem ser **validadas e sanitizadas** para evitar SQL Injection e XSS.
4. O upload da planilha deve ser **limitado a arquivos `.xlsx` ou `.csv`**, com verificação de tipo MIME e extensão.
5. A aplicação não deve permitir o upload de arquivos executáveis ou maliciosos.

---

### 🧑‍💻 Regras Técnicas e de Desenvolvimento

1. O projeto deve ser desenvolvido usando **PHP procedural puro (sem frameworks)**.
2. O banco de dados deve ser **MySQL**, com scripts SQL entregues para criação das tabelas.
3. A estrutura de diretórios deve ser organizada da seguinte forma:


4. Os arquivos devem ser comentados explicando as seções principais do código.
5. O frontend pode ser em HTML simples com CSS básico para usabilidade mínima.
6. A lógica de negócio deve ser separada da camada de exibição quando possível.
7. O sistema deve funcionar corretamente em ambiente local com Apache + MySQL (XAMPP, WAMP ou similar).

---

### ✅ Funcionalidades Obrigatórias

- [x] Cadastro e login de usuário
- [x] Upload inicial de planilha com validação
- [x] Importação de dados da planilha para o banco
- [x] Cadastro manual de itens
- [x] Listagem paginada de itens
- [x] Edição de itens
- [x] Exclusão de itens
- [x] Proteção por login e sessão

---
