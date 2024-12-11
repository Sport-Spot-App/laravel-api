<p align="center" width="400">
<a href="https://ibb.co/3mcKSS9"><img src="https://i.ibb.co/Mk7dPPx/Design-sem-nome-1.png" alt="Design-sem-nome-1" border="0"></a>
</p>

# 🏐 Sport Spot API
[![Front-end](https://img.shields.io/badge/Frontend-F98C43?style=for-the-badge)](https://github.com/Sport-Spot-App/flutter-frontend/)
## Introdução
Bem-vindo à API do Sport Spot, um sistema desenvolvido para facilitar a gestão de quadras esportivas, reservas, e esportes disponíveis. Com esta API, proprietários podem gerenciar suas quadras, usuários podem reservar horários e marcar suas quadras favoritas, criando uma experiência simples e eficiente para todos os envolvidos.

## Construído com
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
![Mysql](https://img.shields.io/badge/MySQL-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-%231D63ED?style=for-the-badge&logo=docker&logoColor=white)
- **PHP**: Back-end robusto e escalável com Laravel.
- **MySQL**: Banco de dados relacional para armazenamento de informações.
- **Docker**: Ambiente de desenvolvimento padronizado e fácil de configurar.

## Escopo do Projeto
Este projeto tem como objetivo fornecer um sistema de gerenciamento de quadras esportivas que possibilite:
- Cadastro de quadras esportivas e esportes disponíveis.
- Gerenciamento de horários disponíveis ou bloqueados para reservas.
- Funcionalidades de reservas e favoritos.
- Interface simplificada para administradores e usuários.

## Pré-requisitos
Para executar este projeto, é necessário:
- Docker instalado na máquina.

## Tutorial de Instalação

### Passo 1: Ter o Docker instalado no PC
Certifique-se de que o Docker está instalado e configurado corretamente na sua máquina.

### Passo 2: Baixar o projeto do GitHub
Clone este repositório para sua máquina local:
```bash
git clone https://github.com/Sport-Spot-App/laravel-api
cd laravel-api
```

### Passo 3: Copiar o arquivo `.env.example`
Crie uma cópia do arquivo de exemplo de configuração e ajuste as variáveis, se necessário:
```bash
cp .env.example .env
```
> **Nota:** Você pode alterar as configurações do banco de dados ou a porta padrão, mas recomendamos manter a porta padrão 80 para maior compatibilidade.

### Passo 4: Subir o container do Docker com Docker Compose
Inicie os serviços necessários utilizando o Docker Compose:
```bash
docker-compose up -d
```

### Passo 5: Acessar o container do workspace
Entre no ambiente de desenvolvimento do container:
```bash
docker exec -it workspace bash
```

### Passo 6: Instalar as dependências do PHP
Dentro do container do workspace, instale as dependências do Laravel:
```bash
composer install
```

### Passo 7: Executar as migrations
Configure o banco de dados executando as migrations:
```bash
php artisan migrate
```

## Autor
* **Allan Gabriel de Freitas Pedroso** - *Desenvolvedor Fullstack* - [Allan Gabriel](https://github.com/agp531)

## Licença
Este projeto está licenciado sob a licença MIT. Consulte o arquivo LICENSE para mais detalhes.
