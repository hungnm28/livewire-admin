<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use mysqli;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateDatabase extends Command
{
    protected $signature = 'la:create-database';

    protected $description = 'Create new database and modify .env';

    public function handle()
    {

        $setENV = $this->setupEnv();

        if ($setENV) {
            $this->createDatabase();
        }

        return Command::SUCCESS;
    }

    private function createDatabase()
    {
        $mysql_root_pass = password("ROOT PASSWORD",'Root password',true);
        $servername = "127.0.0.1";
        $username = "root";
        $password = $mysql_root_pass;
        $db_name = getenv('DB_DATABASE');

        // Creating a connection
        $conn = new mysqli($servername, $username, $password);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        // Creating a database named newDB
        if ($conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci") === TRUE) {
            \Laravel\Prompts\info("Database created successfully with the name $db_name");
        }
        if (strtolower(getenv('DB_USERNAME')) != "root") {
            \Laravel\Prompts\info("CREATE USER IF NOT EXISTS " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . " IDENTIFIED BY '" . getenv('DB_PASSWORD') . "';");
            if ($conn->query("CREATE USER IF NOT EXISTS " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . " IDENTIFIED BY '" . getenv('DB_PASSWORD') . "'") === TRUE) {
                \Laravel\Prompts\info("CREATE USER IF NOT EXISTS " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . " IDENTIFIED BY '" . getenv('DB_PASSWORD') . "';");
            }
            if ($conn->query("GRANT ALL PRIVILEGES ON " . getenv('DB_DATABASE') . ".*   TO " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . "  WITH GRANT OPTION") === TRUE) {
                \Laravel\Prompts\info("GRANT ALL PRIVILEGES ON " . getenv('DB_DATABASE') . ".*   TO " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . "  WITH GRANT OPTION;");
            }
            if ($conn->query("ALTER USER " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . " IDENTIFIED WITH mysql_native_password BY '" . getenv('DB_PASSWORD') . "'") === TRUE) {
                \Laravel\Prompts\info("ALTER USER " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . " IDENTIFIED WITH mysql_native_password BY '" . getenv('DB_PASSWORD') . "';");
            }
        }
        // closing connection
        $conn->close();

    }

    private function setupEnv()
    {
        Env::enablePutenv();
        $envs = [];
        $app_url = text("APP_URL", "insert app url", env('APP_URL'));
        if (filter_var($app_url, FILTER_VALIDATE_URL)) {
            $envs["APP_URL"] = $app_url;
            \Laravel\Prompts\info("APP_URL: $app_url");
        }
        $db_name = text("ENV: DB_DATABASE", 'database name', env('DB_DATABASE'));
        $db_name = strtolower($db_name);
        if ($db_name != "no") {
            $envs["DB_DATABASE"] = $db_name;
            \Laravel\Prompts\info("DB_DATABASE: $db_name");
        }

        $db_username = text("ENV: DB_USERNAME", 'user name', env("DB_USERNAME"));
        $db_username = strtolower($db_username);
        if ($db_username != "no") {
            $envs["DB_USERNAME"] = $db_username;
            \Laravel\Prompts\info("DB_USERNAME: $db_username");
        }

        $db_password = password("ENV: DB_PASSWORD", 'passwprd', env("DB_PASSWORD"));
        if ($db_password != "no") {
            $envs["DB_PASSWORD"] = $db_password;
            \Laravel\Prompts\info("DB_PASSWORD: $db_password");
        }

        return $this->writeEnv($envs);
    }

    private function writeEnv($data = [])
    {
        $str = "";
        foreach ($data as $key => $value) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
            \Laravel\Prompts\info("Added: $key" . "=" . $value);
        }
        foreach ($_ENV as $key => $value) {
            $value = trim($value);
            if (!Str::isAscii($value) || str_contains($value, " ")) {
                $value = "\"$value\"";
            }
            $str .= "$key=$value \n";
        }
        $pathSave = base_path(".env");
        return file_put_contents($pathSave, $str);
    }
}