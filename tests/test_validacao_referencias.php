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

use app\models\Referencia;

$errors = [];

// 1. Verificar se a classe existe
if (!class_exists('app\models\Referencia')) {
    echo "❌ ERRO: O Model 'Referencia' não foi encontrado em models/Referencia.php.\n";
    exit(1);
}

$model = new Referencia();

// Verificação de segurança: as colunas do banco foram mapeadas?
if (!$model->hasAttribute('ano') || !$model->hasAttribute('titulo')) {
    echo "❌ ERRO: O Model 'Referencia' existe, mas não possui os atributos 'ano' ou 'titulo'.\n";
    echo "Verifique se você gerou o Model a partir da tabela correta no Gii.\n";
    exit(1);
}

// 2. Testar Validação de Ano Futuro
$ano_futuro = date('Y') + 5;
$model->ano = $ano_futuro; 
if ($model->validate(['ano'])) {
    $errors[] = "O sistema ACEITOU um ano futuro ($ano_futuro). (Dica: use a regra 'compare' com 'operator' => '<=')";
}

// 3. Testar Validação de Título Curto
$model->titulo = "Curto";
if ($model->validate(['titulo'])) {
    $errors[] = "O sistema ACEITOU um título com menos de 10 caracteres. (Dica: use a regra 'string' com 'min' => 10)";
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
