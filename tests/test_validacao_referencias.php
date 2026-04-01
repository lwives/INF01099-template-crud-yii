<?php
// Simula o ambiente do Yii2
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

// Caminhos baseados na estrutura padrão do Yii Basic
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
$config = require(__DIR__ . '/../config/console.php');
new yii\console\Application($config);

use app\models\Referencia;

$errors = [];

// 1. Verificar se a classe existe (se o Gii foi rodado)
if (!class_exists('app\models\Referencia')) {
    echo "❌ ERRO: O Model 'Referencia' não foi encontrado. Você gerou o Model no Gii?\n";
    exit(1);
}

$model = new Referencia();

// 2. Testar Validação de Ano Futuro
$ano_futuro = date('Y') + 5;
$model->ano = $ano_futuro; 
if ($model->validate(['ano'])) {
    $errors[] = "Falha: O sistema aceitou um ano futuro (" . $ano_futuro . "). Verifique a regra 'compare' no Model.";
}

// 3. Testar Validação de Título Curto
$model->titulo = "Curto";
if ($model->validate(['titulo'])) {
    $errors[] = "Falha: O sistema aceitou um título com menos de 10 caracteres. Verifique a regra 'string' no Model.";
}

// Resultado final
if (empty($errors)) {
    echo "✅ Todas as validações customizadas da INF01099 foram implementadas com sucesso!\n";
    exit(0); 
} else {
    echo "⚠️ Erros encontrados na validação:\n";
    foreach ($errors as $err) {
        echo " - " . $err . "\n";
    }
    exit(1); 
}
