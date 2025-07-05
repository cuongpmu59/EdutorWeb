function env($key, $default = null) {
    static $env = null;

    if ($env === null) {
        $env = [];

        $path = __DIR__ . '/.env';
        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;

                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $name = trim($parts[0]);
                    $value = trim($parts[1]);

                    if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                        (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                        $value = substr($value, 1, -1);
                    }

                    $env[$name] = $value;
                }
            }
        } else {
            error_log(".env file not found in " . __DIR__);
        }
    }

    return $env[$key] ?? $default;
}
