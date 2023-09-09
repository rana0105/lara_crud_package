<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;

class LaraCrudPackageService
{
    public function createLaraCrudPackage($modelName)
    {
         // create the controller
         $this->createController($modelName);

         // create the model
         $this->createModel($modelName);
 
         // create views
         $this->createViews($modelName);
 
         // create request file
         $this->createRequestFile($modelName);
 
         // create migration file
         $this->createMigration($modelName);
 
         // create routes
         $this->createRoutes($modelName);
    }

    private function createController($modelName)
    {
        $requestName = "{$modelName}Request";
        $controllerName = "{$modelName}Controller";
        $controllerFile = app_path("Http/Controllers/{$controllerName}.php");

        // Define your CRUD methods here
        $crudMethods = $this->createMethods($modelName, $requestName);
        $requestNameImport = "use App\Http\Requests\\{$requestName}";
        // Generate the controller file content
        $controllerContent = <<<EOD
                        <?php

                        namespace App\Http\Controllers;

                        {$requestNameImport};

                        class {$controllerName} extends Controller
                        {
                            {$crudMethods}
                        }
                        EOD;

    // Write the content to the controller file
        file_put_contents($controllerFile, $controllerContent);
    }

    private function createMethods($modelName, $requestName)
    {
        $crudMethods = <<<EOD

            public function index()
            {
                \$data = {$modelName}::all();
                return view("{$modelName}.index", compact('data'));
            }

            public function create()
            {
                return view("{$modelName}.create");
            }

            public function store({$requestName} \$request)
            {
                {$modelName}::create(\$request->all());
                return view("{$modelName}.index");
            }

            public function show(\$id)
            {
                \$data = {$modelName}::find(\$id);
                return view("{$modelName}.show", compact('data'));
            }

            public function edit(\$id)
            {
                \$data = {$modelName}::findOrFail(\$id);
                return view("{$modelName}.edit", compact('data'));
            }

            public function update({$requestName} \$request, \$id)
            {
                \$data = {$modelName}::findOrFail(\$id);
                \$data->update(\$request->all());
                return view("{$modelName}.index");
            }

            public function destroy(\$id)
            {
                \$data = {$modelName}::findOrFail(\$id);
                \$data->delete();
                return view("{$modelName}.index");
            }

            EOD;
        return $crudMethods;
    }

    private function createModel($modelName)
    {
        $modelFile = app_path("Models/{$modelName}.php");

        $attributes = "'name', 'email', 'phone'";

        // Generate the model file content
        $modelContent = <<<EOD
        <?php

        namespace App;

        use Illuminate\Database\Eloquent\Model;

        class {$modelName} extends Model
        {
            protected \$fillable = [{$attributes}];
        }
        EOD;

        // Write the content to the model file
        file_put_contents($modelFile, $modelContent);
    }

    private function createViews($modelName)
    {
        $viewsDirectory = resource_path("views/{$modelName}");

        // Check if the directory already exists
        if (!file_exists($viewsDirectory)) {
            // Create the directory if it doesn't exist
            mkdir($viewsDirectory, 0755, true);

            $views = ['create', 'edit', 'index', 'show'];

            foreach ($views as $view) {
                $viewFile = "{$viewsDirectory}/{$view}.blade.php";
                // Generate the view file content
                $viewContent = "<!-- Content for {$view} view -->";
                // Write the content to the view file
                file_put_contents($viewFile, $viewContent);
            }
        }
    }


    private function createRequestFile($modelName)
    {
        $requestName = "{$modelName}Request";
        $requestDirectory = app_path("Http/Requests");

        // Create the directory if it doesn't exist
        if (!file_exists($requestDirectory)) {
            mkdir($requestDirectory, 0755, true);
        }

        $requestFile = "{$requestDirectory}/{$requestName}.php";

        // Generate the request file content
        $requestContent = <<<EOD
        <?php

        namespace App\Http\Requests;

        use Illuminate\Foundation\Http\FormRequest;

        class {$requestName} extends FormRequest
        {
            public function authorize()
            {
                return true;
            }

            public function rules()
            {
                return [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users',
                    'phone' => 'required|numeric|digits:10',
                ];
            }
        }
        EOD;

        // Write the content to the request file
        file_put_contents($requestFile, $requestContent);
    }


    private function createMigration($modelName)
    {
        \Artisan::call('make:migration', [
            'name' => "create{$modelName}_table"
        ]);
    }

    private function createRoutes($modelName)
    {
        $routeContent = "\nRoute::resource('" . strtolower($modelName) . "', '{$modelName}Controller');";

        $webRoutesFile = base_path('routes/web.php');

        // Append the route content to the web.php file
        file_put_contents($webRoutesFile, $routeContent, FILE_APPEND);
    }
}
