
<?php

class ThemeHandler
{
    private static $activeTheme;
    private static $themePath;

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

    public static function render_header($data = [])
    {
        extract($data);
        require_once self::$themePath . "header.php";
    }

    public static function render_footer($data = [])
    {
        extract($data);
        require_once self::$themePath . "footer.php";
    }

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

    public static function getThemePath()
    {
        return self::$themePath;
    }

    public static function getActiveTheme()
    {
        return self::$activeTheme;
    }
}


