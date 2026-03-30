# PubMan - Versão Yii Framework

Este laboratório foca no uso do [*framework* Yii 2](https://www.yiiframework.com/) para gerenciar referências bibliográficas. Yii é um *framework* de desenvolvimento de aplicações Web em PHP. Ele é utilizado por muitas empresas e preza pela estabilidade extrema (está na versão 2.0 desde então). Apesar de haver rumores de uma versão 3 em desenvolvimento, ele não é o *framework* mais popular (posição que é ocupada por [Laravel](https://laravel.com/)).  

**Vantagens do Yii**:
* Yii segue MVC e ActiveRecord, padrões de projeto que são importantes de aprender, sem esconder via abstrações excessivas;
* Possui ferramenta de geração de código CRUD (`gii`) que acelera o desenvolvimento;
* É um dos *frameworks* PHP mais rápidos que existem (em termos de resposta);
* Possui proteção integrada e robusta contra SQL injection, XSS e CSRF;

**Desvantagens**:
* Curva de aprendizado inicial;
* Comunicade um pouco menor, sendo menos *hype*;
* Ecosistema menor em termos de bibliotecas prontas;
* Demora um pouco para adotar as novas tecnologias do PHP.

## 🛠️ Como iniciar o ambiente no Codespaces

Inicie um novo ambiente no CodeSpaces e aguarde a inicialização. Ele irá configurar o container e instalar o MariaDB e criar as tabelas no banco de dados, conforme especificado no arquivo `schema.sql`.

1. **Realizar teste de Sanidade do Banco de Dados**:
Antes de prosseguir adiante, verifique se o *script* SQL funcionou corretamente, digitando no terminal:
```bash
mariadb -u pubman_app -ppubmanapp db_pubman -e "show tables;"
```
Se aparecer a lista de tabelas (`pub_manager`, `pub_manager-author`), o banco está pronto.

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

5.Clique em **Open in Browser**, na notificação que aparecerá.

## Gerando o CRUD com o `Gii
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

### Usando o Gii

Acesse a URL do projeto e adicione `?r=gii` ao final (p.ex., `https://...8080.app.github.dev/?r=gii`).

Model Generator:

Table Name: pub_manager

Model Class: PubManager

Clique em Preview e, depois, em Generate.

CRUD Generator:

Model Class: app\models\PubManager

Search Model Class: app\models\PubManagerSearch

Controller Class: app\controllers\PubManagerController

Clique em Preview e, depois, em Generate.

Agora, acesse `?r=pub-manager` para ver seu sistema funcionando com ordenação, busca e paginação automáticas!

### Adicionando regras de validação

Depois que o `Gii` gerar o arquivo `models/PubManager.php`, abra-o e localize o método `rules()`.

1. **Customizando o Model** (`models/PubManager.php`)
   
O `Gii` criará regras básicas (como *required* para campos `NOT NULL`). Vamos adicionar uma regra para o campo `Ano` e outra para o formato dos Autores.

```php
public function rules()
{
    return [
        // Regras geradas pelo Gii...
        [['Ref_type', 'Ref_authors', 'Ref_title', 'Ref_year'], 'required'],
        [['Ref_year'], 'integer'],
        
        // --- NOSSAS REGRAS CUSTOMIZADAS ---
        
        // 1. Validar que o ano não seja futuro
        ['Ref_year', 'compare', 'compareValue' => date('Y'), 'operator' => '<=', 
         'message' => 'O ano de publicação não pode ser maior que o ano atual.'],

        // 2. Validar formato de autores (mínimo Nome e Sobrenome) usando Regex
        ['Ref_authors', 'match', 'pattern' => '/\w+\s+\w+/', 
         'message' => 'Cada autor deve conter pelo menos Nome e Sobrenome.'],
         
        // 3. Tornar a URL obrigatória APENAS se o tipo for 'Site' (Validação Condicional)
        ['Ref_URL', 'required', 'when' => function($model) {
            return $model->Ref_type == 'Site';
        }, 'whenClient' => "function (attribute, value) {
            return $('#pubmanager-ref_type').val() == 'Site';
        }"],
    ];
}


