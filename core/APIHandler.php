<?php
/**
 * Class APIHandler
 *
 * Esta classe é responsável por gerenciar as requisições da API.
 * Ela autentica as requisições, roteia os endpoints apropriados e
 * envia respostas em formato JSON. Os métodos principais incluem
 * autenticação, listagem de clientes e criação de novos usuários.
 *
 * Exemplos de endpoints suportados:
 * - clientes/listar: Retorna uma lista de clientes.
 * - usuarios/novo: Cria um novo usuário.
 *
 * @package CoreCRM\Core
 */

class APIHandler
{
    /**
     * Handles an API request.
     *
     * @param string $endpoint API endpoint requested (e.g. "clientes/listar")
     *
     * @return void
     */
    public static function handleRequest($endpoint)
    {
        System::log("Handling API request for endpoint: {$endpoint}", "debug");

        // Autenticação via token no header
        if (!self::authenticate()) {
            self::sendJsonResponse(["error" => "Unauthorized"], 401);
            return;
        }

        // Exemplo de roteamento de API
        switch ($endpoint) {
            case "clientes/listar":
                self::listClients();
                break;
            case "usuarios/novo":
                self::createNewUser();
                break;
            default:
                HookHandler::do_action("api_" . str_replace("/", "_", $endpoint), [], function() use ($endpoint) {
                    self::sendJsonResponse(["error" => "API endpoint not found: {$endpoint}"], 404);
                });
                break;
        }
    }

    /**
     * Authenticates the API request using a token from the request headers.
     *
     * This function retrieves the 'Authorization' header, extracts the token,
     * and checks it against a predefined secret token. Logs authentication
     * success or failure. Returns true if authentication is successful,
     * otherwise returns false.
     *
     * @return bool True if the request is authenticated, false otherwise.
     */

    private static function authenticate()
    {
        // Implementar lógica de autenticação de token aqui
        // Por exemplo, verificar um token JWT ou uma chave de API no header Authorization
        $headers = getallheaders();
        $authToken = isset($headers["Authorization"]) ? str_replace("Bearer ", "", $headers["Authorization"]) : null;

        if ($authToken === "your_secret_api_token") { // Placeholder para um token simples
            System::log("API authentication successful.", "debug");
            return true;
        }
        System::log("API authentication failed.", "warning");
        return false;
    }

    /**
     * Retorna uma lista de clientes.
     *
     * Este método é chamado quando o endpoint da API "clientes/listar" é acessado.
     * Ele utiliza o DatabaseHandler para executar uma consulta SQL SELECT
     * na tabela "clients" e retorna o resultado como uma resposta em JSON.
     *
     * @return void
     */
    private static function listClients()
    {
        // Exemplo de uso do DatabaseHandler
        $clients = (new QueryBuilder("clients"))->select()->get();
        self::sendJsonResponse($clients);
    }

    private static function createNewUser()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (empty($input["username"]) || empty($input["password"])) {
            self::sendJsonResponse(["error" => "Username and password are required."], 400);
            return;
        }

        // Exemplo de inserção no banco de dados
        $data = [
            "username" => $input["username"],
            "password" => password_hash($input["password"], PASSWORD_DEFAULT), // Hash da senha
            "role" => "user"
        ];
        (new QueryBuilder("users"))->insert($data)->execute();

        self::sendJsonResponse(["message" => "User created successfully."], 201);
    }

    /**
     * Envia uma resposta em JSON com um status code opcional.
     *
     * @param mixed $data Dados a serem enviados em formato JSON.
     * @param int $statusCode Código de status HTTP a ser enviado. Padr o: 200.
     * @return void
     */
    public static function sendJsonResponse($data, $statusCode = 200)
    {
        header("Content-Type: application/json");
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }
}


