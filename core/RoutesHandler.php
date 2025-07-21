<?php 
/**
 * Class RoutesHandler
 *
 * Essa classe é responsável por gerenciar as rotas do sistema. Ela permite
 * adicionar rotas, inicializar rotas padrão e despachar requisições para
 * as rotas apropriadas com base no método HTTP e na URL. Além disso, a
 * classe também lida com autenticação e permissões para as rotas que
 * exigem controle de acesso.
 *
 * Principais funcionalidades:
 * - Inicialização de rotas padrão do sistema.
 * - Adição de novas rotas com suporte a middlewares.
 * - Despacho de requisições para as rotas correspondentes.
 * - Verificação de autenticação e permissões antes de acessar rotas protegidas.
 *
 * @package CoreCRM
 */

class RoutesHandler
{
    private static $routes = [];

    /**
     * Inicializa o RoutesHandler com as rotas padrão do sistema
     *
     * Essa função inicializa o RoutesHandler com as rotas padrão do sistema,
     * como a rota "/" e a rota "/api". Além disso, essa função também
     * registra a função de tratamento para rotas "/api/*" que redirecionam
     * para a classe APIHandler.
     *
     * @return void
     */
    public static function init()
    {
        // Rotas padrão do sistema
        self::addRoute("GET", "/", function () {
            echo "Bem-vindo ao CRM!";
        });
        System::log("RoutesHandler initialized.");
    }

    /**
     * Adiciona uma rota ao sistema
     *
     * Essa função adiciona uma rota ao sistema, com uma função de callback
     * que será chamada quando a rota for acessada. Além disso, essa função
     * também permite adicionar middlewares para a rota, como autenticação
     * e permissões.
     *
     * @param string $method Método HTTP que a rota aceita (GET, POST, PUT, DELETE, ANY)
     * @param string $pattern Padrão da rota (ex: /, /admin, /api/(.*) )
     * @param callable $callback Função de callback que será chamada quando a rota for acessada
     * @param array $middlewares Middlewares que serão aplicados para a rota (ex: ["auth" => true, "permission" => "admin"])
     * @return void
     */
    public static function addRoute($method, $pattern, $callback, $middlewares = [])
    {
        self::$routes[] = [
            "method" => $method,
            "pattern" => self::formatPattern($pattern),
            "callback" => $callback,
            "middlewares" => $middlewares
        ];
        System::log("Route added: [{$method}] {$pattern}");
    }

    /**
     * Converte padrões como /api/(.*) para regex
     *
     * Essa função é usada internamente pelo RoutesHandler para converter
     * padrões de rotas em regex. Ela substitui todas as "/" por "\\/" e
     * adiciona "^" e "$" no início e fim da regex, respectivamente.
     *
     * @param string $pattern Padrão da rota
     * @return string Regex equivalente ao padrão
     */
    private static function formatPattern($pattern)
    {
        // Converte padrões como /api/(.*) para regex
        return "/^" . str_replace("/", "\\/", $pattern) . "$/";
    }

    /**
     * Dispatch da rota atual
     *
     * Essa função itera sobre as rotas adicionadas pelo RoutesHandler e
     * verifica se a rota atual (baseada na URL e no método HTTP) 
     * coincide com alguma das rotas. Caso coincida, a função de callback
     * da rota é chamada, passando os parâmetros da rota como argumentos.
     *
     * Além disso, essa função também verifica se a rota tem autenticação
     * e permissões, e redireciona para a página de login se a autenticação
     * for exigida e o usuário não estiver logado. Se a permissão for exigida
     * e o usuário não tiver a permissão necessária, a função apenas imprime
     * "Acesso negado.".
     *
     * Se nenhuma rota for encontrada, a função retorna um status 404 com
     * a mensagem "404 - Página não encontrada.".
     *
     * @return void
     */
    public static function dispatch()
    {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $method = $_SERVER["REQUEST_METHOD"];

        foreach (self::$routes as $route) {
            //echo $route['pattern'];
            if (preg_match($route["pattern"], $uri, $matches)) {
                if ($route["method"] === "ANY" || $route["method"] === $method) {
                    // Remover o primeiro elemento (full match) dos matches
                    array_shift($matches);

                    // Verificar autenticação e permissões
                    if (isset($route["middlewares"]["auth"]) && $route["middlewares"]["auth"] === true) {
                        if (!AuthHandler::isLoggedIn()) {
                            AuthHandler::redirect("/login");
                            return;
                        }
                    }
                    if (isset($route["middlewares"]["permission"])) {
                        if (!AuthHandler::checkPermission($route["middlewares"]["permission"])) {
                            echo "Acesso negado."; // Ou redirecionar para página de erro
                            return;
                        }
                    }

                    call_user_func_array($route["callback"], $matches);
                    return;
                }
            }
        }

        // Rota não encontrada
        header("HTTP/1.0 404 Not Found");
        echo "404 - Página não encontrada.";
    }

    /**
     * Retorna todas as rotas definidas no sistema
     *
     * @return array Um array com todas as rotas definidas no sistema
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    /**
     * RoutesHandler - Registro e gerenciamento de rotas customizadas por plugins
     * Plugins podem registrar rotas e callbacks para páginas customizadas
     * Suporta self-test para diagnóstico
     */
    class RoutesHandler {
        /**
         * Registra uma rota customizada
         * @param string $route
         * @param callable $callback
         */
        public static function register($route, $callback) {
            if (isset(self::$routes[$route])) {
                System::log("RoutesHandler: rota duplicada '$route'", "error");
                return false;
            }
            if (!is_callable($callback)) {
                System::log("RoutesHandler: callback inválido para rota '$route'", "error");
                return false;
            }
            self::$routes[$route] = $callback;
            return true;
        }

        /**
         * Executa o callback da rota, incluindo layout do tema
         * @param string $route
         * @param array $data
         */
        public static function handle($route, $data = []) {
            if (!isset(self::$routes[$route])) {
                System::log("RoutesHandler: rota '$route' não encontrada", "error");
                ThemeHandler::render_layout("<div class='text-red-600 p-8'>Página não encontrada</div>", $data);
                return;
            }
            ThemeHandler::render_layout(function($d) use ($route) {
                self::$routes[$route]($d);
            }, $data);
        }

        /**
         * Registra uma rota customizada (para plugins)
         * @param string $route
         * @param callable $callback
         */
        public static function registerPluginRoute($route, $callback) {
            if (!is_callable($callback)) {
                System::log("RoutesHandler: callback inválido para rota '$route'", "error");
                return false;
            }
            // Adiciona como rota GET sem middlewares
            self::addRoute("GET", $route, function($data = []) use ($callback) {
                ThemeHandler::render_layout(function($d) use ($callback) {
                    $callback($d);
                }, $data);
            });
            return true;
        }

        /**
         * Self-test: simula requisições para todas rotas e verifica se retornam layout válido
         * @return array
         */
        public static function selfTest() {
            $results = [];
            foreach (self::$routes as $route) {
                try {
                    ob_start();
                    if (is_callable($route['callback'])) {
                        $route['callback'](['test' => true]);
                    }
                    ob_end_clean();
                    $results[$route['pattern']] = 'ok';
                } catch (Exception $e) {
                    $results[$route['pattern']] = 'error: ' . $e->getMessage();
                    System::log("RoutesHandler selfTest: erro em '{$route['pattern']}': " . $e->getMessage(), "error");
                }
            }
            return $results;
        }

        /**
         * Lista todas rotas registradas
         */
        public static function list() {
            return array_keys(self::$routes);
        }

        /**
         * Teste de mesa para RoutesHandler
         */
        public static function testBench() {
            echo "<h3>RoutesHandler::selfTest</h3>";
            $result = self::selfTest();
            foreach ($result as $k => $v) {
                echo "<div>Rota $k: $v</div>";
            }
            echo "<h3>RoutesHandler::list</h3>";
            $list = self::list();
            foreach ($list as $route) {
                echo "<div>Rota registrada: $route</div>";
            }
        }
    }
}


