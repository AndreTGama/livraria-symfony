# CRUD Livraria em Symfony

Sistema de gerenciamento de livraria desenvolvido para permitir o cadastro, a edição e o gerenciamento de livros, autores e assuntos. O cadastro de livros exige a vinculação de ao menos um autor e um gênero(assunto). Para assegurar a confiabilidade e a persistência do histórico de dados, o projeto implementa soft delete nas operações de remoção.

## Tecnologias
* Symfony
* Twig
* MySql
* Sweetalert2
* Ux live component
* Bootstrap
* Datatables
* PHPFaker
* Fixture


## Como Rodar
### Pré-requisito
* Docker
* Make (link para apoio na instalação: https://samuel-nonoka.medium.com/instalando-makefile-no-windows-1d0021d2e0ce)

### Usando Make
```
make setup
```

Esse comando, irá iniciar os containers do docker, instalar as dependências, rodar as migrations (cirando as tabelas e view), e também irá rodar o fixtures (seeder) com alguns exemplos

- Aplicação: http://localhost:8080
- phpMyAdmin: http://localhost:8081 

### Sem make
```
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:migrate -n
docker compose exec php php bin/console doctrine:fixtures:load -n
docker compose exec php php bin/console cache:warmup
docker compose restart php
```

Ao iniciar o serviço `php`, o Docker ajusta automaticamente as permissões do volume `var` para `www-data`. O restart ao final reaplica as permissões nos arquivos de cache gerados pelos comandos de configuração.

### Testes
```
make test
```

### Scripts database
Os scripts ficam na pasta `database/`na qual tem o `schema.sql` que tem somente a tabela e o view que foi criado, e também o `seed.sql` que contém as datas

## Decisões técnicas

- *Coluna SMALLINT*: No teste mostrava um modelo sugeria texto, mas o colunas com o tipo SMALLINT ocupa menos espaço, permite filtrar por faixa e impede valores inválidos.
- *Timestamp e Soft delete*: Por segurança os registros não podem ser excluídos, o que garante rastreabilidade.
- *Uso de Redis*: o relatório e as listagens ficam em cache, no Redis o que acelera o carregamento, também está com listener do Doctrine desse modo, sempre que atualizar uma informação, o sistema irá limpar o cache, e mostrar os dados atualizados.

## Link de Apoio
* https://symfony.com/doc
* https://symfony.com/bundles/DoctrineFixturesBundle
* https://pentiminax.github.io/ux-datatables/
* https://github.com/pentiminax/ux-sweet-alert
* https://getbootstrap.com/docs/5.3/getting-started/introduction/
* https://docs.chocolatey.org/en-us/choco/setup/#install-from-powershell-v3
* https://hub.docker.com/
* https://www.w3schools.com
* https://copy-paste-css.com/
* https://www.css-buttons.com/
* https://datatables.net/
* https://mdbootstrap.com/docs/standard/data/tables/examples-and-customization/
* https://learn.getgrav.org/2/themes/twig-tags-filters-functions/filters
* https://fakerphp.org/
* https://notes.enovision.net/october-cms/passing-variables-from-twig-to-component
* https://tailwindflex.com/tag/header