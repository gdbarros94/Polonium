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
     * Obtém os headers HTTP, incluindo Authorization, de forma robusta e case-insensitive
     */
    private static function getAuthorizationHeader()
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        // Normaliza as chaves para minúsculas
        $normalized = [];
        foreach ($headers as $k => $v) {
            $normalized[strtolower($k)] = $v;
        }
        // Tenta pegar o Authorization de várias formas
        if (empty($normalized['authorization'])) {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $normalized['authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $normalized['authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            }
        }
        // Log temporário para debug (remova em produção)
        System::log("HEADERS: " . json_encode($_SERVER), "debug");
        return [
            'Authorization' => $normalized['authorization'] ?? null
        ];
    }

    /**
     * Autentica a requisição via token salvo no banco (tabela user_tokens)
     * Retorna true se autenticado, false caso contrário
     */
    public static function authenticate()
    {
        $headers = self::getAuthorizationHeader();
        $authToken = isset($headers["Authorization"]) ? str_replace("Bearer ", "", $headers["Authorization"]) : null;
        if (!$authToken) return false;
        $token = (new QueryBuilder("api_tokens"))->select()->where("token", "=", $authToken)->get();
        $token = isset($token[0]) ? $token[0] : null;
        
        return $token ? true : false;
    }

    /**
     * Gera um token seguro e salva no banco para o usuário autenticado
     */
    public static function generateToken()
    {
        $headers = self::getAuthorizationHeader();
        if (empty($headers['Authorization']) || stripos($headers['Authorization'], 'Basic ') !== 0) {
            self::sendJsonResponse(["error" => "Authorization header with Basic Auth required"], 400);
        }
        $auth = base64_decode(substr($headers['Authorization'], 6));
        if (!$auth || strpos($auth, ':') === false) {
            self::sendJsonResponse(["error" => "Invalid Basic Auth format"], 400);
        }
        list($username, $password) = explode(':', $auth, 2);
        if (empty($username) || empty($password)) {
            self::sendJsonResponse(["error" => "Username and password required"], 400);
        }
        $user = (new QueryBuilder("users"))
            ->select(["id", "username", "senha", "tipo", "ativo"])
            ->where("username", "=", $username)
            ->get();
        $user = isset($user[0]) ? $user[0] : null;
        if (!$user || !$user["ativo"]) {
            self::sendJsonResponse(["error" => "Invalid credentials"], 401);
        }
        if (!AuthHandler::verifyPassword($password, $user["senha"])) {
            self::sendJsonResponse(["error" => "Invalid credentials"], 401);
        }
        $token = self::generateSecureToken();
        (new QueryBuilder("api_tokens"))->insert([
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