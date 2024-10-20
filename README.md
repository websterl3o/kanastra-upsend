# UpSend

## Descrição do Projeto
Projeto UpSend é uma aplicação que tem como objetivo realizar o upload de uma lista de cobrança e enviar um e-mail para cada cliente da lista.

## Origem do Projeto
O projeto foi desenvolvido como parte de um desafio do processo seletivo da empresa Kanastra para testar minhas habilidades técnicas.

## Tecnologias e Ferramentas Utilizadas

<div align="left">
    <img src="https://img.shields.io/badge/-Docker-%23fff?style=for-the-badge&logo=docker&logoColor=2496ED" target="_blank">
    <img src="https://img.shields.io/badge/-PHP-%23fff?style=for-the-badge&logo=php&logoColor=777BB4" target="_blank">
    <img src="https://img.shields.io/badge/-Laravel-%23fff?style=for-the-badge&logo=laravel&logoColor=FF2D20" target="_blank">
    <img src="https://img.shields.io/badge/-Vue.js-%23fff?style=for-the-badge&logo=vue.js&logoColor=4FC08D" target="_blank">
    <img src="https://img.shields.io/badge/-PHPUnit-%23fff?style=for-the-badge&logo=phpunit&logoColor=4FC08D" target="_blank">
    <img src="https://img.shields.io/badge/-Tailwindcss-%23fff?style=for-the-badge&logo=tailwindcss&logoColor=4FC08D" target="_blank">
    <img src="https://img.shields.io/badge/-Vite-%23fff?style=for-the-badge&logo=vite&logoColor=4FC08D" target="_blank">
</div>

## Configuração do Ambiente
1. Clone o repositório do projeto
    ```shellScript
    git clone git@github.com:websterl3o/kanastra-upsend.git
    ```
2. Crie o arquivo .env apartir do .env.example
    ```shellScript
    cp .env.example .env
    ```
3. Execute o comando para subir o ambiente
    ```shellScript
    docker-compose up -d --build
    ```
4. Execute o comando para instalar as dependências do projeto
    ```shellScript
    docker-compose exec app composer install
    ```
5. Execute o comando para gerar a chave do projeto
    ```shellScript
    docker-compose exec app php artisan key:generate
    ```
6. Execute o comando para criar a estrutura do banco de dados
    ```shellScript
    docker-compose exec app php artisan migrate
    ```
7. Instalar dependencias do node
    ```shellScript
    docker-compose exec app npm install
    ```
8. Acesse o projeto em http://localhost:9696

## Comandos Úteis
### Executar testes
```shellScript
docker-compose exec app php artisan test
```

### Gerar coverage html
```shellScript
docker-compose exec app php artisan test --coverage-html=coverage-html
```

### Executar os jobs
```shellScript
docker-compose exec app php artisan queue:work
```

## Alguns Screenshots da tela do projeto:

### Tela inicial de upload de lista de cobrança
![Tela inicial de upload de lista de cobrança](./storage/screenshots/tela-inicial.png)

### Caso não tenha nenhum arquivo anexado e tentar enviar
![Caso não tenha nenhum arquivo anexado e tentar enviar](./storage/screenshots/erro-arquivo-nao-anexado.png)

### Caso o arquivo anexado não seja um arquivo CSV
![Caso o arquivo anexado não seja um arquivo CSV](./storage/screenshots/erro-arquivo-nao-csv.png)

### Anexando um arquivo CSV
![Anexando um arquivo CSV](./storage/screenshots/anexando-arquivo-csv.png)

### Após selecionar e enviar o arquivo no formato correto
![Após selecionar e enviar o arquivo no formato correto](./storage/screenshots/arquivo-enviado-com-sucesso.png)
