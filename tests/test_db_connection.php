<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/console.php');
new yii\console\Application($config);

try {
    Yii::$app->db->open();
    echo "✅ Conexão estabelecida com sucesso!";
    exit(0);
} catch (\Exception $e) {
    echo "❌ Erro de conexão: " . $e->getMessage();
    exit(1);
}
