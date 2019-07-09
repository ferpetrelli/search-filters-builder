<?php

namespace Petrelli\SearchInterfaceBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;


class CreateSection extends Command
{

    protected $signature = 'search-builder:section {name} {route?}';

    protected $description = 'Create a new section to be used within a section';

    protected $files;

    protected $composer;


    public function __construct(Filesystem $files, Composer $composer)
    {

        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;

    }


    public function handle()
    {

        $name  = $this->argument('name');
        $route = $this->argument('route');

        $className = Str::studly(Str::singular($name));
        $routeName = strtolower($route);

        $this->createSection($className, $routeName);

        $this->composer->dumpAutoloads();

    }


    protected function createSection($className, $routeName)
    {

        /**
         * Create sections directory
         */
        $directory = app_path(join('/', array_filter(['Filters', 'Sections'])));

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0777, true);
        }

        /**
         * Fill up the template with the new class to be created
         */
        $stub = str_replace(
            ['{{className}}', '{{routeName}}'],
            [$className, $routeName],
            $this->files->get(__DIR__ . '/stubs/Section.stub')
        );

        /**
         * Place the files where they belong within your application
         */
        $file = join('/', [$directory, $className . '.php']);

        $this->files->put($file, $stub);

        $this->info("\nThe Section {$className} has been created successfully!");
    }


}
