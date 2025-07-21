# 🔗 Integração Google Sheets - Sistema de Estoque Ruckus

## 📋 Visão Geral

Esta implementação permite importar dados diretamente da sua planilha pública do Google Sheets, eliminando os problemas de importação CSV e fornecendo dados sempre atualizados.

## 🎯 Planilha Configurada

**URL da Planilha:** [Gestão de APs](https://docs.google.com/spreadsheets/d/12yHevEiJJTfT_PPn8zcyW8yQjzlY0L_4fcx2JOguAxw/edit?usp=sharing)

**ID da Planilha:** `12yHevEiJJTfT_PPn8zcyW8yQjzlY0L_4fcx2JOguAxw`

## 📊 Estrutura da Planilha

| Coluna | Cabeçalho | Descrição | Mapeamento |
|--------|-----------|-----------|------------|
| A | AP MAC | MAC Address do Access Point | APMAC |
| B | AP Name | Nome do AP | APName |
| C | Model | Modelo do equipamento | Model |
| D | Serial | Número Serial | Serial |
| E | Status | Status atual | Status |
| F | Location | Localização | Location |
| G | Registered On | Data de registro | Inclusao |
| H | Observação | Observações adicionais | Obs |

## 🚀 Como Usar

### 1. **Acessar a Importação**
1. Faça login no sistema
2. No menu lateral, clique em "Google Sheets"
3. A página mostrará o status da conexão e preview dos dados

### 2. **Verificar Dados**
- **Status da Conexão:** Verde = Conectado, Vermelho = Erro
- **Preview:** Primeiras 10 linhas da planilha
- **Estatísticas:** Total de linhas e última atualização

### 3. **Importar Dados**
1. Clique no botão "Importar do Google Sheets"
2. Confirme a ação
3. Aguarde o processamento
4. Veja o relatório de importação

## ⚙️ Funcionalidades

### ✅ **Cache Inteligente**
- Dados são armazenados em cache por 5 minutos
- Evita múltiplas requisições à planilha
- Botão "Atualizar" para forçar nova leitura

### ✅ **Preview em Tempo Real**
- Visualização das primeiras 10 linhas
- Formatação tabular com cabeçalhos
- Dados sempre atualizados

### ✅ **Mapeamento Automático**
- Colunas mapeadas automaticamente
- Suporte a campos vazios
- Validação de dados

### ✅ **Relatório Detalhado**
- Número de itens importados
- Itens ignorados (duplicatas)
- Modelos únicos encontrados
- Timestamp da última atualização

## 🔧 Arquivos Criados

### **Configuração**
- `config/google_sheets.php` - Configurações e funções de conexão

### **Classes**
- `classes/GoogleSheets.php` - Classe principal de integração

### **Interface**
- `dashboard/import_google_sheets.php` - Página de importação
- `dashboard/import_google_sheets_process.php` - Processamento

### **Testes**
- `test_google_sheets.php` - Script de teste da integração

## 📈 Vantagens vs CSV

| Aspecto | CSV | Google Sheets |
|---------|-----|---------------|
| **Atualização** | Manual | Automática |
| **Colaboração** | Não | Sim |
| **Backup** | Manual | Automático |
| **Validação** | Limitada | Avançada |
| **Formatação** | Inconsistente | Consistente |
| **Erros** | Frequentes | Raros |

## 🛠️ Solução de Problemas

### **Erro de Conexão**
```
Erro: Erro ao acessar a planilha Google Sheets
```

**Soluções:**
1. Verifique se a planilha está pública
2. Confirme o ID da planilha em `config/google_sheets.php`
3. Teste a URL: `https://docs.google.com/spreadsheets/d/12yHevEiJJTfT_PPn8zcyW8yQjzlY0L_4fcx2JOguAxw/export?format=csv`

### **Dados Não Atualizados**
```
Última atualização: Há muito tempo
```

**Solução:**
1. Clique no botão "Atualizar" na página
2. Ou aguarde 5 minutos para atualização automática

### **Erro de Importação**
```
Erro na importação: [mensagem específica]
```

**Soluções:**
1. Verifique se o banco de dados está configurado
2. Confirme as permissões de escrita
3. Execute `test_google_sheets.php` para diagnóstico

## 🧪 Testando a Integração

Execute o script de teste para verificar se tudo está funcionando:

```bash
php test_google_sheets.php
```

**Resultados Esperados:**
- ✅ Conexão com Google Sheets funcionando
- ✅ Leitura de dados: OK
- ✅ Formatação: OK
- ✅ Cache: OK

## 📊 Dados da Sua Planilha

**Estatísticas Atuais:**
- **Total de APs:** 100+
- **Modelos:** T350D, H510, T310D
- **Status:** Instalado
- **Localizações:** Vitória/ES
- **Período:** 2019-2025

## 🔄 Atualizações Futuras

### **Funcionalidades Planejadas:**
- [ ] Sincronização bidirecional
- [ ] Notificações de mudanças
- [ ] Histórico de importações
- [ ] Validação avançada de dados
- [ ] Exportação de relatórios

### **Melhorias Técnicas:**
- [ ] Cache Redis/Memcached
- [ ] Processamento em background
- [ ] Logs detalhados
- [ ] Métricas de performance

## 📞 Suporte

Para problemas ou dúvidas:

1. **Execute o teste:** `php test_google_sheets.php`
2. **Verifique os logs:** Arquivos de erro do PHP
3. **Teste a URL:** Acesse a planilha diretamente
4. **Consulte a documentação:** Este arquivo

---

**Status:** ✅ **IMPLEMENTAÇÃO CONCLUÍDA E FUNCIONAL**

A integração está pronta para uso! Acesse o dashboard e teste a nova funcionalidade. 