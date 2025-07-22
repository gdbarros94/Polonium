<?php

/**
 * Controlador da API de Clientes
 * Gerencia todas as operações da API RESTful para clientes
 */
class ClientsApiController {
    
    private $itemsPerPage = 20;
    private $maxItemsPerPage = 100;
    
    /**
     * GET /api/clientes
     * Lista clientes com suporte a paginação, busca e filtros
     */
    public function index() {
        try {
            // Parâmetros de consulta
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = min($this->maxItemsPerPage, max(1, intval($_GET['limit'] ?? $this->itemsPerPage)));
            $offset = ($page - 1) * $limit;
            $search = $_GET['search'] ?? null;
            $status = $_GET['status'] ?? null;
            $company = $_GET['company'] ?? null;
            $city = $_GET['city'] ?? null;
            $state = $_GET['state'] ?? null;
            $country = $_GET['country'] ?? null;
            $created_after = $_GET['created_after'] ?? null;
            $created_before = $_GET['created_before'] ?? null;
            $sort_by = $_GET['sort_by'] ?? 'created_at';
            $sort_order = strtoupper($_GET['sort_order'] ?? 'DESC');
            
            // Validar parâmetros de ordenação
            $allowedSortFields = ['id', 'name', 'email', 'company', 'city', 'state', 'created_at', 'updated_at'];
            if (!in_array($sort_by, $allowedSortFields)) {
                $sort_by = 'created_at';
            }
            
            if (!in_array($sort_order, ['ASC', 'DESC'])) {
                $sort_order = 'DESC';
            }
            
            // Buscar clientes
            $clients = $this->getFilteredClients($search, $status, $company, $city, $state, $country, $created_after, $created_before, $sort_by, $sort_order, $limit, $offset);
            
            // Contar total de registros
            $total = $this->getFilteredClientsCount($search, $status, $company, $city, $state, $country, $created_after, $created_before);
            
            // Calcular paginação
            $totalPages = ceil($total / $limit);
            $hasNextPage = $page < $totalPages;
            $hasPrevPage = $page > 1;
            
            $response = [
                'success' => true,
                'data' => $clients,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => $totalPages,
                    'has_next_page' => $hasNextPage,
                    'has_prev_page' => $hasPrevPage,
                    'next_page' => $hasNextPage ? $page + 1 : null,
                    'prev_page' => $hasPrevPage ? $page - 1 : null
                ],
                'filters' => [
                    'search' => $search,
                    'status' => $status,
                    'company' => $company,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'created_after' => $created_after,
                    'created_before' => $created_before,
                    'sort_by' => $sort_by,
                    'sort_order' => $sort_order
                ],
                'timestamp' => date('c')
            ];
            
            $this->sendJsonResponse($response, 200);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Erro ao buscar clientes: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * GET /api/clientes/{id}
     * Obtém um cliente específico
     */
    public function show($id) {
        try {
            if (!is_numeric($id)) {
                $this->sendErrorResponse('ID inválido', 400);
                return;
            }
            
            $client = ClientManager::getClient($id);
            
            if (!$client) {
                $this->sendErrorResponse('Cliente não encontrado', 404);
                return;
            }
            
            $response = [
                'success' => true,
                'data' => $client,
                'timestamp' => date('c')
            ];
            
            $this->sendJsonResponse($response, 200);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Erro ao buscar cliente: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * POST /api/clientes
     * Cria um novo cliente
     */
    public function store() {
        try {
            $data = $this->getJsonInput();
            
            if (!$data) {
                $this->sendErrorResponse('Dados JSON inválidos', 400);
                return;
            }
            
            // Validar dados
            $errors = ClientManager::validateClient($data);
            if (!empty($errors)) {
                $this->sendErrorResponse('Dados inválidos', 422, $errors);
                return;
            }
            
            // Criar cliente
            $client = ClientManager::createClient($data);
            
            if (!$client) {
                $this->sendErrorResponse('Erro ao criar cliente', 500);
                return;
            }
            
            $response = [
                'success' => true,
                'message' => 'Cliente criado com sucesso',
                'data' => $client,
                'timestamp' => date('c')
            ];
            
            $this->sendJsonResponse($response, 201);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Erro ao criar cliente: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * PUT /api/clientes/{id}
     * Atualiza um cliente existente
     */
    public function update($id) {
        try {
            if (!is_numeric($id)) {
                $this->sendErrorResponse('ID inválido', 400);
                return;
            }
            
            // Verificar se cliente existe
            $existingClient = ClientManager::getClient($id);
            if (!$existingClient) {
                $this->sendErrorResponse('Cliente não encontrado', 404);
                return;
            }
            
            $data = $this->getJsonInput();
            
            if (!$data) {
                $this->sendErrorResponse('Dados JSON inválidos', 400);
                return;
            }
            
            // Adicionar ID aos dados para validação
            $data['id'] = $id;
            
            // Validar dados
            $errors = ClientManager::validateClient($data);
            if (!empty($errors)) {
                $this->sendErrorResponse('Dados inválidos', 422, $errors);
                return;
            }
            
            // Atualizar cliente
            $client = ClientManager::updateClient($id, $data);
            
            if (!$client) {
                $this->sendErrorResponse('Erro ao atualizar cliente', 500);
                return;
            }
            
            $response = [
                'success' => true,
                'message' => 'Cliente atualizado com sucesso',
                'data' => $client,
                'timestamp' => date('c')
            ];
            
            $this->sendJsonResponse($response, 200);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Erro ao atualizar cliente: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * DELETE /api/clientes/{id}
     * Remove um cliente (soft delete)
     */
    public function destroy($id) {
        try {
            if (!is_numeric($id)) {
                $this->sendErrorResponse('ID inválido', 400);
                return;
            }
            
            // Verificar se cliente existe
            $client = ClientManager::getClient($id);
            if (!$client) {
                $this->sendErrorResponse('Cliente não encontrado', 404);
                return;
            }
            
            // Deletar cliente
            $result = $this->softDeleteClient($id);
            
            if (!$result) {
                $this->sendErrorResponse('Erro ao deletar cliente', 500);
                return;
            }
            
            $response = [
                'success' => true,
                'message' => 'Cliente deletado com sucesso',
                'timestamp' => date('c')
            ];
            
            $this->sendJsonResponse($response, 200);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Erro ao deletar cliente: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * GET /api/clientes/stats
     * Retorna estatísticas dos clientes
     */
    public function stats() {
        try {
            global $db;
            
            // Total de clientes
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM clients WHERE deleted_at IS NULL");
            $stmt->execute();
            $total = $stmt->fetchColumn();
            
            // Clientes por status
            $stmt = $db->prepare("SELECT status, COUNT(*) as count FROM clients WHERE deleted_at IS NULL GROUP BY status");
            $stmt->execute();
            $byStatus = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            // Clientes criados nos últimos 30 dias
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM clients WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $stmt->execute();
            $recentClients = $stmt->fetchColumn();
            
            // Top 10 cidades
            $stmt = $db->prepare("SELECT city, COUNT(*) as count FROM clients WHERE deleted_at IS NULL AND city IS NOT NULL AND city != '' GROUP BY city ORDER BY count DESC LIMIT 10");
            $stmt->execute();
            $topCities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Top 10 empresas
            $stmt = $db->prepare("SELECT company, COUNT(*) as count FROM clients WHERE deleted_at IS NULL AND company IS NOT NULL AND company != '' GROUP BY company ORDER BY count DESC LIMIT 10");
            $stmt->execute();
            $topCompanies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response = [
                'success' => true,
                'data' => [
                    'total_clients' => (int)$total,
                    'recent_clients_30_days' => (int)$recentClients,
                    'by_status' => $byStatus,
                    'top_cities' => $topCities,
                    'top_companies' => $topCompanies
                ],
                'timestamp' => date('c')
            ];
            
            $this->sendJsonResponse($response, 200);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Erro ao buscar estatísticas: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * GET /api/clientes/export
     * Exporta clientes em diferentes formatos
     */
    public function export() {
        try {
            $format = strtolower($_GET['format'] ?? 'json');
            $allowedFormats = ['json', 'csv', 'xml'];
            
            if (!in_array($format, $allowedFormats)) {
                $this->sendErrorResponse('Formato não suportado. Use: ' . implode(', ', $allowedFormats), 400);
                return;
            }
            
            // Buscar todos os clientes (com limite de segurança)
            $clients = ClientManager::getClients(5000); // máximo 5000 registros
            
            switch ($format) {
                case 'csv':
                    $this->exportToCsv($clients);
                    break;
                case 'xml':
                    $this->exportToXml($clients);
                    break;
                default:
                    $this->exportToJson($clients);
            }
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Erro ao exportar dados: ' . $e->getMessage(), 500);
        }
    }
    
    // Métodos auxiliares
    
    private function getFilteredClients($search, $status, $company, $city, $state, $country, $created_after, $created_before, $sort_by, $sort_order, $limit, $offset) {
        global $db;
        
        $sql = "SELECT * FROM clients WHERE deleted_at IS NULL";
        $params = [];
        
        if ($search) {
            $sql .= " AND (name LIKE :search OR email LIKE :search OR company LIKE :search OR phone LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        if ($status) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }
        
        if ($company) {
            $sql .= " AND company LIKE :company";
            $params[':company'] = '%' . $company . '%';
        }
        
        if ($city) {
            $sql .= " AND city LIKE :city";
            $params[':city'] = '%' . $city . '%';
        }
        
        if ($state) {
            $sql .= " AND state LIKE :state";
            $params[':state'] = '%' . $state . '%';
        }
        
        if ($country) {
            $sql .= " AND country LIKE :country";
            $params[':country'] = '%' . $country . '%';
        }
        
        if ($created_after) {
            $sql .= " AND created_at >= :created_after";
            $params[':created_after'] = $created_after;
        }
        
        if ($created_before) {
            $sql .= " AND created_at <= :created_before";
            $params[':created_before'] = $created_before;
        }
        
        $sql .= " ORDER BY {$sort_by} {$sort_order}";
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getFilteredClientsCount($search, $status, $company, $city, $state, $country, $created_after, $created_before) {
        global $db;
        
        $sql = "SELECT COUNT(*) FROM clients WHERE deleted_at IS NULL";
        $params = [];
        
        if ($search) {
            $sql .= " AND (name LIKE :search OR email LIKE :search OR company LIKE :search OR phone LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        if ($status) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }
        
        if ($company) {
            $sql .= " AND company LIKE :company";
            $params[':company'] = '%' . $company . '%';
        }
        
        if ($city) {
            $sql .= " AND city LIKE :city";
            $params[':city'] = '%' . $city . '%';
        }
        
        if ($state) {
            $sql .= " AND state LIKE :state";
            $params[':state'] = '%' . $state . '%';
        }
        
        if ($country) {
            $sql .= " AND country LIKE :country";
            $params[':country'] = '%' . $country . '%';
        }
        
        if ($created_after) {
            $sql .= " AND created_at >= :created_after";
            $params[':created_after'] = $created_after;
        }
        
        if ($created_before) {
            $sql .= " AND created_at <= :created_before";
            $params[':created_before'] = $created_before;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    
    private function softDeleteClient($id) {
        global $db;
        
        $stmt = $db->prepare("UPDATE clients SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);
        
        if ($result && class_exists('HookHandler') && method_exists('HookHandler', 'call_hook')) {
            HookHandler::call_hook("after_client_deleted", $id);
        }
        
        return $result;
    }
    
    private function getJsonInput() {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }
    
    private function sendJsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    private function sendErrorResponse($message, $statusCode = 400, $errors = null) {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('c')
        ];
        
        if ($errors) {
            $response['errors'] = $errors;
        }
        
        $this->sendJsonResponse($response, $statusCode);
    }
    
    private function exportToCsv($clients) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="clientes_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Cabeçalhos CSV
        $headers = ['ID', 'Nome', 'Email', 'Telefone', 'Empresa', 'Endereço', 'Cidade', 'Estado', 'CEP', 'País', 'Status', 'Observações', 'Criado em'];
        fputcsv($output, $headers);
        
        // Dados
        foreach ($clients as $client) {
            $row = [
                $client['id'],
                $client['name'],
                $client['email'],
                $client['phone'],
                $client['company'],
                $client['address'],
                $client['city'],
                $client['state'],
                $client['zip_code'],
                $client['country'],
                $client['status'],
                $client['notes'],
                $client['created_at']
            ];
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportToXml($clients) {
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="clientes_' . date('Y-m-d_H-i-s') . '.xml"');
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><clients/>');
        
        foreach ($clients as $client) {
            $clientNode = $xml->addChild('client');
            foreach ($client as $key => $value) {
                $clientNode->addChild($key, htmlspecialchars($value));
            }
        }
        
        echo $xml->asXML();
        exit;
    }
    
    private function exportToJson($clients) {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="clientes_' . date('Y-m-d_H-i-s') . '.json"');
        
        $response = [
            'export_date' => date('c'),
            'total_records' => count($clients),
            'data' => $clients
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}