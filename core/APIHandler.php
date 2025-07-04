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
     * Envia resposta JSON padronizada
     */
    public static function sendJsonResponse($data, $statusCode = 200)
    {
        header("Content-Type: application/json");
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    /**
     * Autentica a requisição via token salvo no banco (tabela user_tokens)
     * Retorna true se autenticado, false caso contrário
     */
    public static function authenticate()
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authToken = isset($headers["Authorization"]) ? str_replace("Bearer ", "", $headers["Authorization"]) : null;
        if (!$authToken) return false;
        $token = (new QueryBuilder("user_tokens"))->select()->where(["token" => $authToken])->first();
        return $token ? true : false;
    }

    /**
     * Gera um token seguro e salva no banco para o usuário autenticado
     */
    public static function generateToken()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (empty($input["username"]) || empty($input["password"])) {
            self::sendJsonResponse(["error" => "Username and password required"], 400);
        }
        $user = (new QueryBuilder("users"))->select()->where(["username" => $input["username"]])->first();
        if (!$user || !password_verify($input["password"], $user["password"])) {
            self::sendJsonResponse(["error" => "Invalid credentials"], 401);
        }
        $token = self::generateSecureToken();
        (new QueryBuilder("user_tokens"))->insert([
            "user_id" => $user["id"],
            "token" => $token,
            "created_at" => date("Y-m-d H:i:s")
        ])->execute();
        self::sendJsonResponse(["token" => $token]);
    }

    /**
     * Gera um token seguro
     */
    public static function generateSecureToken($length = 64)
    {
        return bin2hex(random_bytes($length / 2));
    }
}

