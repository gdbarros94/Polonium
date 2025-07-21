<?php

/**
 * Class ThemeHandler
 *
 * The ThemeHandler class is responsible for managing themes in the application.
 * It initializes the active theme based on the configuration, provides methods
 * to render the header and footer templates, and allows loading of custom
 * templates from the active theme's directory.
 *
 * It handles fallback to a default theme if the configured theme is not found,
 * and provides methods to retrieve the active theme's name and path.
 *
 * Usage:
 * - Call `ThemeHandler::init()` to initialize the theme settings.
 * - Use `ThemeHandler::render_header($data)` to render the header.
 * - Use `ThemeHandler::render_footer($data)` to render the footer.
 * - Use `ThemeHandler::load_template($templateName, $data)` to load a specific template.
 * - Use `ThemeHandler::getThemePath()` to get the path of the active theme.
 * - Use `ThemeHandler::getActiveTheme()` to get the name of the active theme.
 */
class ThemeHandler
{
    private static $activeTheme;
    private static $themePath;

    /**
     * Initialize the ThemeHandler.
     *
     * This method loads the theme settings from the config array and sets the
     * theme path. If the theme path does not exist, it will fallback to the
     * default theme.
     *
     * @return void
     */
    public static function init()
    {
        global $config;
        self::$activeTheme = $config["theme"];
        self::$themePath = __DIR__ . "/../themes/" . self::$activeTheme . "/";

        if (!is_dir(self::$themePath)) {
            System::log("Theme '" . self::$activeTheme . "' not found.", "error");
            // Fallback to a default theme if the configured one is not found
            self::$activeTheme = "default";
            self::$themePath = __DIR__ . "/../themes/default/";
            if (!is_dir(self::$themePath)) {
                die("Default theme not found. System cannot proceed.");
            }
        }
        System::log("ThemeHandler initialized. Active theme: " . self::$activeTheme);
    }

    /**
     * Render the theme's header template.
     *
     * This method requires a "header.php" file in the theme path. The file
     * should contain the HTML for the page header.
     *
     * @param array $data An associative array of data to be passed to the
     *                    template. The keys will be extracted as variables
     *                    in the template.
     *
     * @return void
     */
    public static function render_header($data = [])
    {
        extract($data);
        require_once self::$themePath . "header.php";
    }

    /**
     * Render the theme's footer template.
     *
     * This method requires a "footer.php" file in the theme path. The file
     * should contain the HTML for the page footer.
     *
     * @param array $data An associative array of data to be passed to the
     *                    template. The keys will be extracted as variables
     *                    in the template.
     *
     * @return void
     */
    public static function render_footer($data = [])
    {
        extract($data);
        require_once self::$themePath . "footer.php";
    }

    /**
     * Load a template from the current theme.
     *
     * This method requires a PHP file with the same name as the template
     * name in the theme path. The file should contain the HTML for the
     * template.
     *
     * @param string $templateName The name of the template to load.
     * @param array  $data         An associative array of data to be passed
     *                              to the template. The keys will be
     *                              extracted as variables in the template.
     *
     * @return void
     */
    public static function load_template($templateName, $data = [])
    {
        extract($data);
        $templateFile = self::$themePath . $templateName . ".php";
        if (file_exists($templateFile)) {
            require_once $templateFile;
        } else {
            System::log("Template '{$templateName}' not found in theme '" . self::$activeTheme . "'.", "error");
        }
    }

    /**
     * Render a full page layout with header, content, and footer.
     * Includes self-healing: fallback to default theme if header/footer missing, logs errors.
     *
     * @param callable|string $content Content callback or HTML string for the main area.
     * @param array $data Data to pass to header/footer/content.
     */
    public static function render_layout($content, $data = [])
    {
        $headerFile = self::$themePath . "header.php";
        $footerFile = self::$themePath . "footer.php";
        $fallbackThemePath = __DIR__ . "/../themes/default/";
        // Header
        if (file_exists($headerFile)) {
            extract($data);
            require $headerFile;
        } elseif (file_exists($fallbackThemePath . "header.php")) {
            System::log("Header do tema '" . self::$activeTheme . "' não encontrado. Usando fallback.", "error");
            require $fallbackThemePath . "header.php";
        } else {
            echo "<div style='color:red'>Header não encontrado em nenhum tema.</div>";
        }
        // Content
        echo '<main id="theme-miolo">';
        if (is_callable($content)) {
            $content($data);
        } else {
            echo $content;
        }
        echo '</main>';
        // Footer
        if (file_exists($footerFile)) {
            extract($data);
            require $footerFile;
        } elseif (file_exists($fallbackThemePath . "footer.php")) {
            System::log("Footer do tema '" . self::$activeTheme . "' não encontrado. Usando fallback.", "error");
            require $fallbackThemePath . "footer.php";
        } else {
            echo "<div style='color:red'>Footer não encontrado em nenhum tema.</div>";
        }
    }

    /**
     * Self-test: checks existence of theme files and logs/report problems.
     * Returns array with status of header, footer, and templates.
     */
    public static function selfTest($templates = ['header', 'footer'])
    {
        $results = [];
        $theme = self::$activeTheme;
        $themePath = self::$themePath;
        foreach ($templates as $tpl) {
            $file = $themePath . $tpl . ".php";
            $results[$tpl] = file_exists($file) ? 'ok' : 'missing';
        }
        // Test fallback
        $fallbackPath = __DIR__ . "/../themes/default/";
        foreach ($templates as $tpl) {
            $file = $fallbackPath . $tpl . ".php";
            $results[$tpl . '_fallback'] = file_exists($file) ? 'ok' : 'missing';
        }
        // Log results
        foreach ($results as $k => $v) {
            if ($v !== 'ok') {
                System::log("Theme selfTest: $k is $v", "error");
            }
        }
        return $results;
    }

    /**
     * Get the path to the active theme.
     *
     * @return string The path to the active theme.
     */
    public static function getThemePath()
    {
        return self::$themePath;
    }

    /**
     * Get the name of the active theme.
     *
     * @return string The name of the active theme.
     */

    public static function getActiveTheme()
    {
        return self::$activeTheme;
    }

    /**
     * Teste de mesa para ThemeHandler::render_layout e selfTest
     */
    public static function testBench() {
        echo "<h3>ThemeHandler::selfTest</h3>";
        $result = self::selfTest();
        foreach ($result as $k => $v) {
            echo "<div>Arquivo $k: $v</div>";
        }
        echo "<h3>ThemeHandler::render_layout (callback)</h3>";
        self::render_layout(function() {
            echo "<div>Miolo de teste</div>";
        }, ['test' => true]);
        echo "<h3>ThemeHandler::render_layout (string)</h3>";
        self::render_layout("<div>Miolo como string</div>", ['test' => true]);
    }
}


