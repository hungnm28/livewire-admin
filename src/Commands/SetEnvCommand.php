<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Env;
use mysqli;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class SetEnvCommand extends Command
{
    protected $signature = 'la:set-env';

    protected $description = 'Update .env ';

    private $arrSaveEnv = [];

    public function handle()
    {
        $this->setupEnv();
        $this->createDatabase();
        return Command::SUCCESS;
    }


    public function setupEnv()
    {
        Env::enablePutenv();
        $this->arrSaveEnv['APP_URL'] = text("APP_URL", 'APP_URL...', env('APP_URL'), true, fn(string $value) => match (true) {
            filter_var($value, FILTER_VALIDATE_URL) => 'App_URL not valid',
            default => null
        });


        $db_name =  text("ENV: DB_DATABASE",'database name',env('DB_DATABASE'));
        $db_name = strtolower($db_name);
        if ($db_name != "no") {
            $this->arrSaveEnv["DB_DATABASE"] = $db_name;
        }

        $db_username = text("ENV: DB_USERNAME",'user name',env("DB_USERNAME"));
        $db_username = strtolower($db_username);
        if ($db_username != "no") {
            $this->arrSaveEnv["DB_USERNAME"] = $db_username;
        }

        $db_password = password("ENV: DB_PASSWORD",'passwprd',env("DB_PASSWORD"));
        if ($db_password != "no") {
            $this->arrSaveEnv["DB_PASSWORD"] = $db_password;
        }

        $env = file_get_contents(base_path() . "/.env");
        $envs = explode("\n", $env);
        foreach ($envs as $key => $value) {
            foreach ($this->arrSaveEnv as $env_name => $env_value) {
                if (strpos($value, $env_name . '=') === 0) {
                    $envs[$key] = $env_name . "=" . $env_value;
                    putenv($env_name . "=" . $env_value);
                    $_ENV[$env_name] = $env_value;
                    $this->info("Added: $env_name" . "=" . $env_value);
                }
            }
        }


        file_put_contents(base_path() . "/.env", implode("\n", $envs));
        file_put_contents(base_path() . "/.env.example", implode("\n", $envs));

    }

    function createDatabase()
    {
        $mysql_root_pass = password("ROOT PASSWORD",'Root password',true);
        if (empty($mysql_root_pass)) return;
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
            $this->info("Database created successfully with the name $db_name");
        }
        if (strtolower(getenv('DB_USERNAME')) != "root") {
            $this->info("CREATE USER IF NOT EXISTS " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . " IDENTIFIED BY '" . getenv('DB_PASSWORD') . "';");
            if ($conn->query("CREATE USER IF NOT EXISTS " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . " IDENTIFIED BY '" . getenv('DB_PASSWORD') . "'") === TRUE) {
                $this->info("CREATE USER IF NOT EXISTS " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . " IDENTIFIED BY '" . getenv('DB_PASSWORD') . "';");
            }
            if ($conn->query("GRANT ALL PRIVILEGES ON " . getenv('DB_DATABASE') . ".*   TO " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . "  WITH GRANT OPTION") === TRUE) {
                $this->info("GRANT ALL PRIVILEGES ON " . getenv('DB_DATABASE') . ".*   TO " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . "  WITH GRANT OPTION;");
            }
            if ($conn->query("ALTER USER " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . " IDENTIFIED WITH mysql_native_password BY '" . getenv('DB_PASSWORD') . "'") === TRUE) {
                $this->info("ALTER USER " . getenv('DB_USERNAME') . "@" . getenv('DB_HOST') . " IDENTIFIED WITH mysql_native_password BY '" . getenv('DB_PASSWORD') . "';");
            }
        }
        // closing connection
        $conn->close();
    }

}