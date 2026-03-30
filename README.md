# PubMan - Versão Yii Framework 🚀

Este laboratório foca no uso do framework Yii2 para gerenciar referências bibliográficas.

## 🛠️ Como iniciar o ambiente no Codespaces

1. **Inicializar o Banco de Dados:**
   No terminal, execute o comando para carregar seu esquema:
   ```bash
   mysql -u root < schema.sql
   ```

Teste de Sanidade do Banco de Dados
Antes de prosseguir para a instalação do framework, verificar se o script SQL funcionou corretamente. No terminal:

mysql -u root -e "SHOW TABLES IN db_pubman;"


Se aparecer a lista de tabelas (pub_manager, pub_manager-author): O banco está pronto.

Se der erro "Unknown database": O script schema.sql falhou ou não foi executado.

2. **Instalar o Yii Framework**:
Como o repositório não está vazio, usaremos este comando para baixar o framework e mover os arquivos para a raiz:

```php
composer create-project --prefer-dist yiisoft/yii2-app-basic yii-temp && cp -rn yii-temp/. . && rm -rf yii-temp
```

3. **Configurar a Conexão (config/db.php)**:
Ajuste o arquivo para usar as credenciais do projeto:

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

4. Iniciar o Servidor:
No terminal, execute:

php yii serve --port=8080 --docroot=web

5.Clique no Open in Browser na notificação que aparecerá.

----











# PubMan - Versão Yii Framework 🚀

Este laboratório foca no uso do framework Yii2 para gerenciar referências bibliográficas.

## 🛠️ Como iniciar o ambiente no Codespaces

1. inicializar banco de dados com o `squema.sql`

2. No temrinal, executar:
```bash
composer create-project --prefer-dist yiisoft/yii2-app-basic .
```

4. ajustar o arquivo config/db.php para usar as mesmas credenciais do `squema.sql`:
```sq
<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1;dbname=db_pubman',
    'username' => 'pubman_app',
    'password' => 'pubmanapp',
    'charset' => 'utf8',
];
```

4.  No terminal, inicie o servidor embutido do Yii:
```bash
php yii serve --port=8080 --docroot=web
```

5. Clicar no **Open in Browser** na notificação que aparecerá.

### Gerando o CRUD com o Gii
Para não precisar escrever todo o código do formulário manualmente, vamos usar o `gii`, uma ferramenta de geração automática de CRUD.

No entanto, por questões de segurança, o Yii bloqueia o acesso a essas ferramentas se a requisição não vier de 127.0.0.1. Como o Codespaces funciona através de um túnel/proxy de rede, o IP que chega ao servidor PHP não é o local, o que causa um erro 403 Forbidden.

Portanto, primeiro temos que fazer um ajuste no arquivo config/web.php

Abra o arquivo config/web.php e localize o bloco onde o Gii é configurado (geralmente no final do arquivo). Você deve adicionar a propriedade allowedIPs permitindo qualquer IP (*), já que o Codespaces gerencia a segurança da porta para você:

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

Acesse a URL do projeto e adicione ?r=gii ao final (ex: https://...8080.app.github.dev/?r=gii).

Model Generator:

Table Name: pub_manager

Model Class: PubManager

Clique em Preview e depois em Generate.

CRUD Generator:

Model Class: app\models\PubManager

Search Model Class: app\models\PubManagerSearch

Controller Class: app\controllers\PubManagerController

Clique em Preview e depois em Generate.

Agora, acesse ?r=pub-manager para ver seu sistema funcionando com ordenação, busca e paginação automáticas!

### Adicionando regras de validação

Depois que o Gii gerar o arquivo models/PubManager.php, abra esse arquivo e localize o método rules().

1. Customizando o Model (models/PubManager.php)
O Gii criará regras básicas (como required para campos NOT NULL). Vamos adicionar uma regra para o campo Ano e outra para o formato dos Autores.

```phppublic function rules()
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


