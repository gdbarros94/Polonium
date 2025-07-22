<?php

/**
 * Middleware de Autenticação para API
 * Gerencia autenticação via API Key e rate limiting
 */
class ApiAuthMiddleware {
    
    private static $rateLimits = [];
    private static $maxRequestsPerHour = 1000;
    private static $maxRequestsPerMinute = 50;
    
    /**
     * Middleware principal para autenticação da API
     */
    public static function authenticate() {
        // Verificar se é uma requisição para a API
        $requestUri = $_SERVER['REQUEST_URI'];
        if (!preg_match('/^\/api\//', $requestUri)) {
            return true; // Não é uma requisição da API, prosseguir
        }
        
        // Extrair API Key do cabeçalho ou query parameter
        $apiKey = self::getApiKey();
        
        if (!$apiKey) {
            self::sendErrorResponse('API Key é obrigatória', 401);
            return false;
        }
        
        // Validar API Key
        $apiKeyData = self::validateApiKey($apiKey);
        if (!$apiKeyData) {
            self::sendErrorResponse('API Key inválida', 401);
            return false;
        }
        
        // Verificar se a API Key está ativa
        if ($apiKeyData['status'] !== 'active') {
            self::sendErrorResponse('API Key inativa', 401);
            return false;
        }
        
        // Verificar permissões
        if (!self::checkPermissions($apiKeyData, $requestUri, $_SERVER['REQUEST_METHOD'])) {
            self::sendErrorResponse('Sem permissão para acessar este recurso', 403);
            return false;
        }
        
        // Aplicar rate limiting
        if (!self::checkRateLimit($apiKey)) {
            self::sendErrorResponse('Rate limit excedido', 429);
            return false;
        }
        
        // Registrar uso da API
        self::logApiUsage($apiKey, $requestUri, $_SERVER['REQUEST_METHOD']);
        
        // Definir dados da API Key para uso posterior
        $_SESSION['api_key_data'] = $apiKeyData;
        
        return true;
    }
    
    /**
     * Envia resposta de erro e termina a execução
     */
    private static function sendErrorResponse($message, $statusCode) {
        http_response_code($statusCode);
        echo json_encode(['error' => $message]);
        exit;
    }
    
    /**
     * Extrai a API Key do cabeçalho Authorization ou query parameter
     */
    private static function getApiKey() {
        // Tentar obter do cabeçalho Authorization
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/^Bearer\s+(.+)$/', $authHeader, $matches)) {
                return $matches[1];
            }
            if (preg_match('/^ApiKey\s+(.+)$/', $authHeader, $matches)) {
                return $matches[1];
            }
        }
        
        // Tentar obter do query parameter
        if (isset($_GET['api_key'])) {
            return $_GET['api_key'];
        }
        
        // Tentar obter do cabeçalho X-API-Key
        if (isset($headers['X-API-Key'])) {
            return $headers['X-API-Key'];
        }
        
        return null;
    }
    
    /**
     * Valida a API Key no banco de dados
     */
    private static function validateApiKey($apiKey) {
        global $db;
        
        try {
            $stmt = $db->prepare("
                SELECT ak.*, u.name as user_name, u.email as user_email 
                FROM api_keys ak 
                LEFT JOIN users u ON ak.user_id = u.id 
                WHERE ak.key_hash = :key_hash AND ak.deleted_at IS NULL
            ");
            
            $stmt->execute([':key_hash' => hash('sha256', $apiKey)]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Verificar expiração
                if ($result['expires_at'] && strtotime($result['expires_at']) < time()) {
                    return false;
                }
                
                // Atualizar último uso
                $updateStmt = $db->prepare("UPDATE api_keys SET last_used_at = CURRENT_TIMESTAMP WHERE id = :id");
                $updateStmt->execute([':id' => $result['id']]);
                
                return $result;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Erro ao validar API Key: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica permissões da API Key para o recurso solicitado
     */
    private static function checkPermissions($apiKeyData, $requestUri, $method) {
        // Se não há permissões definidas, permitir tudo (para backward compatibility)
        if (empty($apiKeyData['permissions'])) {
            return true;
        }
        
        $permissions = json_decode($apiKeyData['permissions'], true);
        if (!$permissions) {
            return true;
        }
        
        // Mapear rotas para permissões
        $routePermissions = [
            'GET:/api/clientes' => 'view_clients',
            'GET:/api/clientes/\d+' => 'view_clients',
            'POST:/api/clientes' => 'create_clients',
            'PUT:/api/clientes/\d+' => 'edit_clients',
            'DELETE:/api/clientes/\d+' => 'delete_clients',
            'GET:/api/clientes/stats' => 'view_stats',
            'GET:/api/clientes/export' => 'export_clients'
        ];
        
        $currentRoute = $method . ':' . $requestUri;
        
        foreach ($routePermissions as $pattern => $requiredPermission) {
            if (preg_match('#^' . $pattern . '$#', $currentRoute)) {
                return in_array($requiredPermission, $permissions);
            }
        }
        
        // Se não encontrou padrão específico, verificar permissão geral
        return in_array('manage_clients', $permissions);
    }
    
    /**
     * Verifica rate limiting
     */
    private static function checkRateLimit($apiKey) {
        $currentTime = time();
        $keyHash = hash('sha256', $apiKey);
        
        // Inicializar contadores se não existirem
        if (!isset(self::$rateLimits[$keyHash])) {
            self::$rateLimits[$keyHash] = [
                'minute' => ['count' => 0, 'window' => $currentTime],
                'hour' => ['count' => 0, 'window' => $currentTime]
            ];
        }
        
        $limits = &self::$rateLimits[$keyHash];
        
        // Verificar janela de minuto
        if ($currentTime - $limits['minute']['window'] >= 60) {
            $limits['minute'] = ['count' => 0, 'window' => $currentTime];
        }
        
        // Verificar janela de hora
        if ($currentTime - $limits['hour']['window'] >= 3600) {
            $limits['hour'] = ['count' => 0, 'window' => $currentTime];
        }
        
        // Verificar limites
        if ($limits['minute']['count'] >= self::$maxRequestsPerMinute) {
            return false;
        }
        
        if ($limits['hour']['count'] >= self::$maxRequestsPerHour) {
            return false;
        }
        
        // Incrementar contadores
        $limits['minute']['count']++;
        $limits['hour']['count']++;
        
        return true;
    }
    
    /**
     * Registra uso da API para auditoria
     */
    private static function logApiUsage($apiKey, $endpoint, $method) {
        global $db;
        
        try {
            $stmt = $db->prepare("
                INSERT INTO api_usage_logs (api_key_hash, endpoint, method, ip_address, user_agent, created_at) 
                VALUES (:api_key_hash, :endpoint, :method, :ip_address, :user_agent, CURRENT_TIMESTAMP)
            ");
            
            $stmt->execute([
                ':api_key_hash' => hash('sha256', $apiKey),
                ':endpoint' => $endpoint,
                ':method' => $method,
                ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao registrar uso da API: " . $e->getMessage());
        }
    }
    
    /**
     * Gera uma nova API Key
     */
    public static function generateApiKey($userId, $name, $permissions = [], $expiresAt = null) {
        global $db;
        
        try {
            // Gerar chave aleatória
            $apiKey = bin2hex(random_bytes(32));
            $keyHash = hash('sha256', $apiKey);
            
            $stmt = $db->prepare("
                INSERT INTO api_keys (user_id, name, key_hash, permissions, expires_at, status, created_at) 
                VALUES (:user_id, :name, :key_hash, :permissions, :expires_at, 'active', CURRENT_TIMESTAMP)
            ");
            
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':name' => $name,
                ':key_hash' => $keyHash,
                ':permissions' => json_encode($permissions),
                ':expires_at' => $expiresAt
            ]);
            
            if ($result) {
                return [
                    'api_key' => $apiKey,
                    'key_hash' => $keyHash
                ];
            } else {
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Erro ao gerar API Key: " . $e->getMessage());
            return false;
        }
    }
}