<?php

$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'toolsync';

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Recebe os dados do usuário via JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verifica se os dados necessários estão presentes
$nome = isset($data['nome']) ? trim($data['nome']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$via_google = isset($data['via_google']) ? $data['via_google'] : false;

if (empty($nome) || empty($email)) {
    echo "Nome e E-mail são obrigatórios.";
    exit;
}

// Verificar se o e-mail já existe no banco de dados
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "E-mail já cadastrado.";
    exit;
}

$stmt->close();

// Se for um usuário via Google, não é necessário criptografar a senha
// Você pode gerar uma senha aleatória para manter o padrão de cadastro, caso necessário
$senhaCriptografada = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT); // Gerando uma senha aleatória

// Inserir os dados do usuário no banco
$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nome, $email, $senhaCriptografada);

if ($stmt->execute()) {
    echo "Cadastro realizado com sucesso!";
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>
