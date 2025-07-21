<?php
/**
 * WidgetHandler - Registro e renderização de widgets em áreas do layout
 * Plugins podem registrar widgets em áreas nomeadas (ex: dashboard, sidebar)
 * Suporta self-test para diagnóstico
 */
class WidgetHandler {
    private static $widgets = [];

    /**
     * Registra um widget para uma área específica
     * @param string $area Nome da área (ex: 'dashboard')
     * @param callable $callback Função que retorna HTML do widget
     */
    public static function register($area, $callback) {
        if (!is_callable($callback)) {
            System::log("WidgetHandler: callback inválido para área '$area'", "error");
            return;
        }
        self::$widgets[$area][] = $callback;
    }

    /**
     * Renderiza todos widgets registrados para uma área
     * @param string $area
     * @param array $data
     */
    public static function render_widgets($area, $data = []) {
        foreach (self::$widgets[$area] ?? [] as $cb) {
            try {
                echo $cb($data);
            } catch (Exception $e) {
                System::log("WidgetHandler: erro ao renderizar widget em '$area': " . $e->getMessage(), "error");
                echo "<div style='color:red'>Widget error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }

    /**
     * Self-test: executa todos widgets em modo teste e reporta problemas
     * @return array
     */
    public static function selfTest() {
        $results = [];
        foreach (self::$widgets as $area => $list) {
            foreach ($list as $i => $cb) {
                try {
                    ob_start();
                    $cb(['test' => true]);
                    ob_end_clean();
                    $results["$area-$i"] = 'ok';
                } catch (Exception $e) {
                    $results["$area-$i"] = 'error: ' . $e->getMessage();
                    System::log("WidgetHandler selfTest: erro em '$area' [$i]: " . $e->getMessage(), "error");
                }
            }
        }
        return $results;
    }

    /**
     * Teste de mesa para WidgetHandler
     */
    public static function testBench() {
        echo "<h3>WidgetHandler::selfTest</h3>";
        $result = WidgetHandler::selfTest();
        foreach ($result as $k => $v) {
            echo "<div>Widget $k: $v</div>";
        }
        echo "<h3>WidgetHandler::render_widgets('dashboard')</h3>";
        WidgetHandler::render_widgets('dashboard', ['test' => true]);
    }
}
