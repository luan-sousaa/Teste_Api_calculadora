<?php
header('Content-Type: application/json');

function json_response($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$start = microtime(true);

$method = $_SERVER['REQUEST_METHOD'];
$params = $method === 'GET' ? $_GET : $_POST;
$path = isset($params['path']) ? $params['path'] : (isset($_GET['path']) ? $_GET['path'] : null);

$produtos = [
    'livros' => [
        ['id' => 4, 'nome' => 'Teste de Software', 'preco' => 85, 'estoque' => 30],
        ['id' => 5, 'nome' => 'PHP Avançado', 'preco' => 95, 'estoque' => 20]
    ]
];

switch ($path) {
    case 'calculadora':
        if ($method !== 'GET') {
            json_response(['erro' => 'Método não permitido'], 405);
        }
        $operacao = isset($_GET['operacao']) ? $_GET['operacao'] : null;
        $num1 = isset($_GET['num1']) ? (float)$_GET['num1'] : null;
        $num2 = isset($_GET['num2']) ? (float)$_GET['num2'] : null;
        if ($operacao === 'somar') {
            $resultado = $num1 + $num2;
        } elseif ($operacao === 'multiplicar') {
            $resultado = $num1 * $num2;
        } elseif ($operacao === 'dividir') {
            if ($num2 == 0) {
                json_response(['erro' => 'Divisão por zero não permitida'], 400);
            }
            $resultado = $num1 / $num2;
        } else {
            json_response(['erro' => 'Operação não suportada'], 400);
        }
        json_response([
            'operacao' => $operacao,
            'num1' => $num1,
            'num2' => $num2,
            'resultado' => $resultado
        ]);
        break;
    default:
        json_response(['erro' => 'Endpoint não encontrado'], 404);
}
?>