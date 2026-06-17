<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MakeModule extends BaseCommand
{
    protected $group = 'Generators';
    protected $name = 'make:module';
    protected $description = 'Generates a new Module with the standard MVC-S structure.';
    protected $usage = 'make:module [name]';
    protected $arguments = ['name' => 'The name of the module to create (PascalCase).'];

    public function run(array $params)
    {
        $moduleName = array_shift($params);
        if (empty($moduleName)) {
            $moduleName = CLI::prompt('Module Name (PascalCase)', null, 'required');
        }
        $moduleName = ucfirst($moduleName);
        $modulePath = APPPATH . 'Modules/' . $moduleName;

        if (is_dir($modulePath)) {
            CLI::error("Module '$moduleName' already exists.");
            return;
        }

        CLI::write("Creating module: $moduleName", 'yellow');

        $directories = [
            'Config', 'Controllers', 'Database/Migrations', 'Database/Seeds',
            'Entities', 'Libraries', 'Models', 'Views',
        ];

        foreach ($directories as $dir) {
            $path = $modulePath . '/' . $dir;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        $this->_createRoutesFile($modulePath, $moduleName);
        $this->_createControllerFile($modulePath, $moduleName);
        $this->_createEntityFile($modulePath, $moduleName);
        $this->_createServiceFile($modulePath, $moduleName);
        $this->_createModelFile($modulePath, $moduleName);
        $this->_createViewFile($modulePath, $moduleName);
        $this->_updateAutoload($moduleName);

        CLI::write("Module '$moduleName' created successfully!", 'green');
    }

    private function _createRoutesFile($path, $name)
    {
        $lower = strtolower($name);
        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\Modules\\$name\Config;\n\nuse CodeIgniter\Router\RouteCollection;\n\n/**\n * @var RouteCollection \$routes\n */\n\$routes->group('$lower', ['namespace' => 'App\Modules\\$name\Controllers'], static function (\$routes) {\n    \$routes->get('/', '{$name}Controller::index', ['as' => '$lower.index']);\n});\n";
        file_put_contents($path . '/Config/Routes.php', $content);
    }

    private function _createControllerFile($path, $name)
    {
        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\Modules\\$name\Controllers;\n\nuse App\Controllers\BaseController;\nuse CodeIgniter\HTTP\ResponseInterface;\n\nclass {$name}Controller extends BaseController\n{\n    public function index(): string|ResponseInterface\n    {\n        return view('App\Modules\\$name\Views\index', ['pageTitle' => '$name Module']);\n    }\n}\n";
        file_put_contents($path . '/Controllers/' . $name . 'Controller.php', $content);
    }

    private function _createEntityFile($path, $name)
    {
        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\Modules\\$name\Entities;\n\nuse CodeIgniter\Entity\Entity;\n\nclass $name extends Entity\n{\n    protected \$datamap = [];\n    protected \$dates = ['created_at', 'updated_at', 'deleted_at'];\n}\n";
        file_put_contents($path . '/Entities/' . $name . '.php', $content);
    }

    private function _createServiceFile($path, $name)
    {
        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\Modules\\$name\Libraries;\n\nclass {$name}Service\n{\n    public function __construct()\n    {\n        // Initialize dependencies\n    }\n}\n";
        file_put_contents($path . '/Libraries/' . $name . 'Service.php', $content);
    }

    private function _createModelFile($path, $name)
    {
        $lower = strtolower($name);
        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\Modules\\$name\Models;\n\nuse CodeIgniter\Model;\nuse App\Modules\\$name\Entities\\$name;\n\nclass {$name}Model extends Model\n{\n    protected \$table = '{$lower}_table';\n    protected \$returnType = $name::class;\n    protected \$useTimestamps = true;\n    protected \$allowedFields = [];\n}\n";
        file_put_contents($path . '/Models/' . $name . 'Model.php', $content);
    }

    private function _createViewFile($path, $name)
    {
        $content = "<?= \$this->extend('layouts/default') ?>\n\n<?= \$this->section('content') ?>\n<div class=\"container my-5\">\n    <h1>$name Module</h1>\n</div>\n<?= \$this->endSection() ?>\n";
        file_put_contents($path . '/Views/index.php', $content);
    }

    private function _updateAutoload($name)
    {
        $file = APPPATH . 'Config/Autoload.php';
        $content = file_get_contents($file);
        $line = "        'App\\\\Modules\\\\$name' => APPPATH . 'Modules/$name',";

        if (strpos($content, $line) === false) {
            $pattern = '/(\$psr4\s*=\s*\[)(.*?)(\];)/s';
            if (preg_match($pattern, $content)) {
                $newContent = preg_replace($pattern, "$1$2$line\n        $3", $content);
                file_put_contents($file, $newContent);
            }
        }
    }
}
