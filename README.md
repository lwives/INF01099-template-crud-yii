# PubMan - Versão Yii Framework

Este laboratório foca no uso do [*framework* Yii 2](https://www.yiiframework.com/) para gerenciar referências bibliográficas. Yii é um *framework* de desenvolvimento de aplicações Web em PHP. Ele é utilizado por muitas empresas e preza pela estabilidade extrema (está na versão 2.0 desde então). Apesar de haver rumores de uma versão 3 em desenvolvimento, ele não é o *framework* mais popular (posição que é ocupada por [Laravel](https://laravel.com/)).  

**Vantagens do Yii**:
* Yii segue MVC e ActiveRecord, padrões de projeto que são importantes de aprender, sem esconder via abstrações excessivas;
* Possui ferramenta de geração de código CRUD (`gii`) que acelera o desenvolvimento;
* É um dos *frameworks* PHP mais rápidos que existem (em termos de resposta);
* Possui proteção integrada e robusta contra SQL injection, XSS e CSRF;

**Desvantagens**:
* Curva de aprendizado inicial;
* Comunidade um pouco menor, sendo menos *hype*;
* Ecosistema menor em termos de bibliotecas prontas;
* Demora um pouco para adotar as novas tecnologias do PHP.

## 🛠️ Como iniciar o ambiente no Codespaces

Inicie um novo ambiente no CodeSpaces e aguarde a inicialização. Ele irá configurar o contêiner e instalar o MariaDB e criar as tabelas no banco de dados, conforme especificado no arquivo `schema.sql`.

1. **Realizar teste de Sanidade do Banco de Dados**:
Antes de prosseguir adiante, verifique se o *script* SQL funcionou corretamente, digitando no terminal:
```bash
php -m | grep pdo_mysql && sudo mariadb -u root -e "SHOW TABLES IN db_pubman;"
```
Se aparecer a lista de tabelas (`tbl_referencia`, `tbl_author`, `tbl_tipo`, `pub_manager-author`, etc), o banco está pronto.

Se der erro *Unknown database*, o *script* schema.sql falhou ou não foi executado.

2. **Instalar o Yii Framework**:
A instalação do *framework* é feita via `composer`, um gerenciador de pacotes do `PHP`. Normalmente, usamos o seguinte comando no terminal: `composer create-project --prefer-dist yiisoft/yii2-app-basic .`. No entanto, ele espera que o repositório (pasta) esteja vazio, mas como temos alguns arquivos de configuração do contêiner no repositório *github*, precisamos instalar em uma pasta diferente e depois copiá-la para a raiz. 

Use o seguinte comando para baixar o *framework* e mover os arquivos para a pasta raiz:
```bash
composer create-project --prefer-dist yiisoft/yii2-app-basic yii-temp && cp -rn yii-temp/. . && rm -rf yii-temp
```

3. **Configurar a Conexão (config/db.php)**:
Você precisa configurar o *framework* para que ele consiga acessar o banco de dados. Para tanto, ajuste o arquivo `config/db.php` para usar as credenciais do projeto, conforme foram criadas no `schema.sql`:
```php
<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1;dbname=db_pubman',
    'username' => 'pubman_app',
    'password' => 'pubmanapp',
    'charset' => 'utf8',
];
```

4. **Iniciar o Servidor**:
No terminal, execute:
```bash
php yii serve --port=8080 --docroot=web
```

5.Clique em **Open in Browser** na notificação que aparecerá. Se tudo estiver certo, a página de "Congratulations!" do Yii vai aparecer. PAsse para a etapa seguinte (Uso do `Gii`).

## Uso do `Gii`
Para não precisar escrever todo o código do formulário manualmente, vamos usar o `gii`, uma ferramenta de geração automática de CRUD.

No entanto, por questões de segurança, o `Yii` bloqueia o acesso a essas ferramentas se a requisição não vier de `127.0.0.1`. Como o Codespaces funciona por meio de um túnel/proxy de rede, o IP que chega ao servidor PHP não é o endereço local, o que gera um erro *403 Forbidden*.

Portanto, primeiro **temos que fazer um ajuste no arquivo `config/web.php`**

Abra o arquivo `config/web.php` e localize o bloco onde o `Gii` é configurado (geralmente no final do arquivo). Você deve adicionar a propriedade `allowedIPs` permitindo qualquer IP (*), já que o Codespaces gerencia a segurança da porta para você:

```php
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // Adicione esta linha para o Debug Toolbar funcionar:
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];

    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // Adicione esta linha para o Gii funcionar:
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];
}
```

### Usando o `Gii` para gerar as páginas de CRUD automaticamente

Para usar o `Gii`, acesse a URL do projeto e adicione `?r=gii` ao final (p.ex., `https://...8080.app.github.dev/?r=gii`). Aparecerá a tela da ferramenta "Welcome to Gii".

**Vamos gerar primeiro os *models* das tabelas. Depois, vamos gerar os CRUDS**.

#### Gerando os *models*:

Vamos iniciar pela `tbl_tipo`. Siga os seguintes passos:

1. Clique em **Model Generator** > **Start**;
2. Table Name: `tbl_tipo` (o Yii deve autocompletar se a conexão com o banco estiver correta e ativa);
3. Model Class: `Tipo` (o Yii sugere automaticamente);
4. Clique em **Preview** e depois no botão verde **Generate**.

O `Gii` irá criar o arquivo `models/Tipo.php` com todas as regras de validação baseadas no `schema.sql`. 

**Repita o processo de Model Generator para as tabelas na seguinte ordem, para que as relações fiquem corretamente mapeadas**: 

1. `tbl_tipo` (Model: `Tipo`)
2. `tbl_editora` (Model: `Editora`)
3. `tbl_veiculo` (Model: `Veiculo`)
4. `tbl_author` (Model: `Author`)
5. `tbl_referencia` (Model: `Referencia`)

No modelo `Referência`, o `Yii` detectará automaticamente os métodos `getTipo()`, `getEditora()` e `getVeiculo()`.

#### Gerando os *CRUDS*:

 **Vamos gerar a interface CRUD da tabela principal**. Siga os seguintes passos:

1. Volte para o menu do `Gii` e clique em **CRUD Generator** > **Start**;
2. Prencha como segue:
   * **Model Class**: `app\models\Referencia`
   * **Search Model Class**: `app\models\ReferenciaSearch`
   * **Controller Class**: `app\controllers\ReferenciaController`
   * **View Path**: (deixe em branco).
     
3. Clique em **Preview** e, em seguida, em **Generate**.

## Testando a aplicação

Acesse `https://URL-DO-CODESPACES/?r=referencia` para ver seu sistema funcionando com ordenação, busca e paginação automáticas!

Você verá o ID do tipo (ex: "1") ao invés do tipo em si. Para mudar, vá ao arquivo `views/referencia/index.php` e altere 'id_tipo' para 'tipo.descricao'.

Isso demonstra o poder do ActiveRecord do Yii: ele vai à tabela `tbl_tipo` buscar a descrição "Artigo em Conferência" sem que você tenha de escrever uma linha de SQL.

## Adicionando regras de validação

Depois que o `Gii` gerar o arquivo `models/Referencia.php`, abra-o e localize o método `rules()`.

1. **Customizando o Model** (`models/Referencia.php`)
   
O `Gii` criará regras básicas (como *required* para campos `NOT NULL`). Vamos adicionar uma regra para o campo `Ano` e outra para o formato dos Autores.

```php
// No arquivo models/Referencia.php
public function rules()
{
    return [
        // Regras geradas pelo Gii (mantenha as que ele criar)...
        [['titulo', 'id_tipo', 'id_editora', 'id_veiculo', 'ano'], 'required'],
        
        // --- NOSSAS REGRAS CUSTOMIZADAS ---
        
        // 1. Validar que o ano não seja futuro
        ['ano', 'compare', 'compareValue' => date('Y'), 'operator' => '<=', 
         'message' => 'O ano de publicação não pode ser maior que o ano atual.'],

        // 2. Validar que o título tenha pelo menos 10 caracteres
        ['titulo', 'string', 'min' => 10, 
         'message' => 'O título da publicação parece curto demais.'],
    ];
}
```

## Desafios e Melhorias

Se você terminou o CRUD básico e quer explorar o potencial real do Yii, tente implementar as seguintes melhorias:

1. **Relacionamentos: Substituir ID por Nome**
Por padrão, o `Gii` cria campos de texto para chaves estrangeiras (IDs). Em um sistema real, o usuário deve selecionar o nome em um dropdown.

**Tarefa**: No formulário de publicação, substitua o campo de `autor_id` (ou similar) por um `dropDownList`. 

**Dica**: Use `yii\helpers\ArrayHelper::map()` para buscar os autores do banco e transformá-los em uma lista de [id => nome].

```php
// No arquivo _form.php
<?= $form->field($model, 'author_id')->dropDownList(
    ArrayHelper::map(Author::find()->all(), 'ID', 'Name'),
    ['prompt' => 'Selecione um Autor']
) ?>
```

**Dica Extra**: Não esqueça de adicionar `use yii\helpers\ArrayHelper;` e `use app\models\Author;` no topo do arquivo `_form.php` para que o código do Dropdown funcione!

2. **Visualização Relacional no GridView**
Na listagem (`index.php`), ao invés de exibir o ID do autor, exiba o nome dele vindo da tabela relacionada.

**Tarefa**: Configure o componente GridView para acessar a relação definida no Model.

**Dica**: Certifique-se de que o Model Referencia tem o método `getAuthor()` definido.

3. **Máscaras e UI**
Melhore a experiência do usuário adicionando máscaras de entrada (ex: para ISBN) ou calendários para campos de data, utilizando os widgets do Yii2 como o `yii\widgets\MaskedInput`.
