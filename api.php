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

    case 'usuario':
        if ($method !== 'POST') {
            json_response(['erro' => 'Método não permitido'], 405);
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            json_response(['erro' => 'JSON malformado'], 400);
        }
        $nome = isset($input['nome']) ? trim($input['nome']) : '';
        $email = isset($input['email']) ? trim($input['email']) : '';
        $idade = isset($input['idade']) ? (int)$input['idade'] : null;
        if (strlen($nome) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL) || $idade < 0) {
            json_response(['erro' => 'Dados inválidos'], 422);
        }
        $id = rand(1000,9999);
        $data_cadastro = date('Y-m-d H:i:s');
        json_response([
            'mensagem' => 'Usuário cadastrado com sucesso',
            'usuario' => [
                'id' => $id,
                'nome' => $nome,
                'email' => $email,
                'idade' => $idade,
                'data_cadastro' => $data_cadastro
            ]
        ], 201);
        break;

    case 'produtos':
        if ($method !== 'GET') {
            json_response(['erro' => 'Método não permitido'], 405);
        }
        $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
        if ($categoria === 'livros') {
            $items = $produtos['livros'];
            json_response([
                'categoria' => 'livros',
                'total' => count($items),
                'produtos' => $items
            ]);
        } else {
            json_response(['erro' => 'Categoria não encontrada'], 404);
        }
        break;

    case 'status':
        $tempo_resposta_ms = round((microtime(true) - $start) * 1000, 2);
        json_response([
            'status' => 'online',
            'versao' => '1.0.0',
            'servidor' => [
                'php_version' => phpversion(),
                'sapi' => php_sapi_name()
            ],
            'tempo_resposta_ms' => $tempo_resposta_ms,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

    case 'lento':
        if ($method !== 'GET') {
            json_response(['erro' => 'Método não permitido'], 405);
        }
        $delay = isset($_GET['delay']) ? (float)$_GET['delay'] : 1;
        $inicio = microtime(true);
        usleep($delay * 1000000);
        $tempo_real = round(microtime(true) - $inicio, 2);
        json_response([
            'mensagem' => 'Resposta atrasada propositalmente',
            'delay_solicitado' => $delay,
            'tempo_real' => "{$tempo_real} segundos"
        ]);
        break;

    default:
        json_response(['erro' => 'Endpoint não encontrado'], 404);
}
?>