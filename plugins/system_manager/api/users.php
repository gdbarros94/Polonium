<?php
// users.php - CRUD de usuários para API do system_manager

class SystemManagerUsersApi {
    public static function registerRoutes() {
        RoutesHandler::addRoute("GET", "/api/users", [self::class, 'listUsers']);
        RoutesHandler::addRoute("POST", "/api/users", [self::class, 'createUser']);
        RoutesHandler::addRoute("PUT", "/api/users", [self::class, 'updateUser']);
        RoutesHandler::addRoute("DELETE", "/api/users", [self::class, 'deleteUser']);
    }

    public static function listUsers() {
        if (!APIHandler::authenticate()) {
            APIHandler::sendJsonResponse(["error" => "Unauthorized"], 401);
        }
        $query = (new QueryBuilder("users"))->select();
        if (!empty($_GET["id"])) {
            $query->where("id", "=", $_GET["id"]);
        } elseif (!empty($_GET["username"])) {
            $query->where("username", "=", $_GET["username"]);
        }
        $users = $query->get();
        APIHandler::sendJsonResponse($users);
    }

    public static function createUser() {
        if (!APIHandler::authenticate()) {
            APIHandler::sendJsonResponse(["error" => "Unauthorized"], 401);
        }
        $input = json_decode(file_get_contents("php://input"), true);
        if (empty($input["username"]) || empty($input["password"]) || empty($input["name"]) || empty($input["email"])) {
            APIHandler::sendJsonResponse(["error" => "Username, password, name and email required"], 400);
        }
        // Definir timezone para America/Sao_Paulo (GMT-3)
        $tz = new DateTimeZone('America/Sao_Paulo');
        $now = new DateTime('now', $tz);
        $data = [
            "username" => $input["username"],
            "password" => AuthHandler::hashPassword($input["password"]),
            "name" => $input["name"],
            "email" => $input["email"],
            "role" => $input["role"] ?? "user",
            "active" => isset($input["active"]) ? (int)$input["active"] : 1,
            // Se houver campos de data, adicione-os aqui, exemplo:
            // "created_at" => $now->format('Y-m-d H:i:s'),
        ];
        (new QueryBuilder("users"))->insert($data)->execute();
        APIHandler::sendJsonResponse(["message" => "User created"], 201);
    }


    public static function updateUser() {
        if (!APIHandler::authenticate()) {
            APIHandler::sendJsonResponse(["error" => "Unauthorized"], 401);
        }
        $input = json_decode(file_get_contents("php://input"), true);
        if (empty($input["id"]) && empty($input["username"])) {
            APIHandler::sendJsonResponse(["error" => "User id or username required"], 400);
        }
        // Definir timezone para America/Sao_Paulo (GMT-3)
        $tz = new DateTimeZone('America/Sao_Paulo');
        $now = new DateTime('now', $tz);
        $data = [];
        if (!empty($input["username"])) $data["username"] = $input["username"];
        if (!empty($input["password"])) $data["password"] = AuthHandler::hashPassword($input["password"]);
        if (!empty($input["name"])) $data["name"] = $input["name"];
        if (!empty($input["email"])) $data["email"] = $input["email"];
        if (!empty($input["role"])) $data["role"] = $input["role"];
        if (isset($input["active"])) $data["active"] = (int)$input["active"];
        // Se houver campos de data de atualização, adicione-os aqui, exemplo:
        // $data["updated_at"] = $now->format('Y-m-d H:i:s');
        if (empty($data)) {
            APIHandler::sendJsonResponse(["error" => "No data to update"], 400);
        }
        $query = (new QueryBuilder("users"))->update($data);
        if (!empty($input["id"])) {
            $query->where("id", "=", $input["id"]);
        } else {
            $query->where("username", "=", $input["username"]);
        }
        $result = $query->execute();
        if ($result->rowCount() === 0) {
            APIHandler::sendJsonResponse(["error" => "User not found or nothing changed"], 404);
        }
        APIHandler::sendJsonResponse(["message" => "User updated"]);
    }

    public static function deleteUser() {
        if (!APIHandler::authenticate()) {
            APIHandler::sendJsonResponse(["error" => "Unauthorized"], 401);
        }
        $id = $_GET["id"] ?? null;
        $username = $_GET["username"] ?? null;
        if (empty($id) && empty($username)) {
            APIHandler::sendJsonResponse(["error" => "User id or username required in query string"], 400);
        }
        $query = (new QueryBuilder("users"))->delete();
        if (!empty($id)) {
            $query->where("id", "=", $id);
        } else {
            $query->where("username", "=", $username);
        }
        $result = $query->execute();
        if ($result->rowCount() === 0) {
            APIHandler::sendJsonResponse(["error" => "User not found"], 404);
        }
        APIHandler::sendJsonResponse(["message" => "User deleted"]);
    }

}
SystemManagerUsersApi::registerRoutes();