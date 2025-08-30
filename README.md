# Aplicação Web com AWS EC2 e RDS MariaDB

## Descrição do Projeto

Este projeto implementa uma aplicação web simples para gerenciamento de produtos, integrada a uma base de dados MariaDB na nuvem AWS. O sistema permite criar e listar registros de produtos com nome, preço e data de criação.

## Arquitetura da Solução

### Serviços AWS Utilizados

#### 1. Amazon EC2 (Elastic Compute Cloud)
**Função:** Servidor web responsável por hospedar e executar a aplicação PHP.

**Configurações realizadas:**
- Instância: `t2.micro` (tier gratuito)
- Sistema Operacional: Amazon Linux 2023
- Nome da instância: `tutorial-ec2-instance-web-server`
- Configurações de rede:
  - Acesso SSH habilitado
  - HTTP (porta 80) e HTTPS (porta 443) liberados
  - Grupo de segurança configurado para permitir tráfego web

**Instalações realizadas na EC2:**
- Apache HTTP Server (web server)
- PHP 8.x com extensão mysqli para conexão com MySQL/MariaDB
- Cliente MariaDB para administração do banco
- Sistema de arquivos configurado com permissões apropriadas para o usuário `ec2-user`

**Responsabilidades da EC2:**
- Hospedar os arquivos PHP da aplicação (`ProductPage.php`, `dbinfo.inc`)
- Executar o servidor web Apache
- Processar requisições HTTP dos usuários
- Conectar-se ao banco de dados RDS via rede privada
- Renderizar páginas HTML dinâmicas com dados do banco

#### 2. Amazon RDS MariaDB
**Função:** Banco de dados relacional gerenciado para armazenamento persistente dos dados.

**Configurações realizadas:**
- Engine: MariaDB
- Instância: `db.t3.micro` (tier gratuito)
- Identificador: `tutorial-db-instance`
- Banco inicial: `sample`
- Usuário: `tutorial_user`
- Porta: 3306 (padrão MariaDB)
- Armazenamento: SSD General Purpose (20GB)

**Conectividade:**
- Conectado automaticamente à EC2 através do VPC padrão
- Comunicação via rede privada (sem exposição pública)
- Endpoint gerado automaticamente pela AWS

**Responsabilidades do RDS:**
- Armazenar dados da tabela `PRODUCTS`
- Processar queries SQL enviadas pela aplicação
- Gerenciar conexões de forma segura
- Backup automático e manutenção do banco
- Escalabilidade automática conforme demanda

## Tabela Criada

### PRODUCTS
```sql
CREATE TABLE PRODUCTS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Campos:**
- `id` (INT): Chave primária auto-incrementada
- `name` (VARCHAR): Nome do produto (até 100 caracteres)
- `price` (DECIMAL): Preço com 2 casas decimais de precisão
- `created_at` (TIMESTAMP): Data/hora de criação automática

## Funcionalidades da Aplicação

### Página de Gerenciamento de Produtos (`ProductPage.php`)
- **Criação:** Formulário para adicionar novos produtos
- **Listagem:** Tabela exibindo todos os produtos cadastrados
- **Validação:** Verificação de dados de entrada (preço numérico positivo)
- **Interface:** Design simples com HTML/CSS básico

### Arquivo de Configuração (`dbinfo.inc`)
- Credenciais de conexão com o banco de dados
- Constantes para endpoint, usuário, senha e nome do banco
- Localizado fora do diretório web público por segurança

## Processo de Deploy

### 1. Criação da Infraestrutura AWS
1. **EC2 Instance Launch:**
   - Criada instância EC2 no console AWS
   - Configurada com Amazon Linux 2023
   - Grupo de segurança com portas HTTP/HTTPS abertas
   - Par de chaves SSH gerado para acesso remoto

2. **RDS Database Creation:**
   - Criada instância RDS MariaDB
   - Configurada para conectar automaticamente com a EC2
   - Banco de dados inicial "sample" criado
   - Credenciais de acesso definidas

### 2. Configuração do Servidor Web
1. **Conexão SSH:** Acesso remoto à EC2 via SSH
2. **Atualização do Sistema:** `sudo dnf update -y`
3. **Instalação de Software:**
   - Apache: `sudo dnf install -y httpd`
   - PHP: `sudo dnf install -y php php-mysqli`
   - MariaDB Client: `sudo dnf install -y mariadb105`

4. **Configuração de Permissões:**
   - Usuário `ec2-user` adicionado ao grupo `apache`
   - Permissões configuradas no diretório `/var/www`
   - Propriedade de arquivos ajustada

### 3. Desenvolvimento da Aplicação
1. **Estrutura de Diretórios:**
   ```
   /var/www/html/ (público)
   └── ProductPage.php
   /var/www/inc/ (privado)
   └── dbinfo.inc
   ```

2. **Arquivo de Configuração:**
   - Endpoint do RDS configurado
   - Credenciais de acesso ao banco

3. **Página Principal:**
   - Interface web para CRUD de produtos
   - Conexão com banco de dados
   - Validação de entrada de dados

## Benefícios da Arquitetura

### Escalabilidade
- EC2 pode ser redimensionada conforme demanda
- RDS escala automaticamente o armazenamento
- Load balancer pode ser adicionado para múltiplas EC2

### Segurança
- Banco de dados não exposto publicamente
- Comunicação via VPC privada
- Credenciais separadas em arquivo seguro

### Gerenciamento
- AWS gerencia infraestrutura subjacente
- Backups automáticos
- Monitoramento integrado

### Custos
- Utilização do tier gratuito da AWS
- Cobrança apenas pelo uso real
- Sem custos de licenciamento de software

## Vídeo Demonstrativo

https://drive.google.com/file/d/1i6_zYdPuiplSYVpuDdnBscukOEQD22e3/view?usp=sharing

---

**Nota:** Este projeto foi desenvolvido seguindo o tutorial oficial da AWS para criação de web server com EC2 e RDS MariaDB.
