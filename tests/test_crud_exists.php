<?php
$files = [
    'models/Referencia.php',
    'models/ReferenciaSearch.php',
    'controllers/ReferenciaController.php',
    'views/referencia/index.php',
    'views/referencia/create.php',
    'views/referencia/_form.php'
];

$missing = [];
foreach ($files as $file) {
    if (!file_exists(__DIR__ . '/../' . $file)) {
        $missing[] = $file;
    }
}

if (empty($missing)) {
    echo "✅ Todos os arquivos do CRUD foram encontrados!";
    exit(0);
} else {
    echo "❌ Arquivos ausentes: " . implode(', ', $missing);
    exit(1);
}
