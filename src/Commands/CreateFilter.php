<?php

namespace Petrelli\SearchInterfaceBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;


class CreateFilter extends Command
{

    protected $signature = 'search-builder:filter {name}';

    protected $description = 'Create a new filter to be used within a section';

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

        $name = $this->argument('name');

        $className = Str::studly(Str::singular($name));

        $this->createStatic($className);

        $this->composer->dumpAutoloads();

    }


    protected function createStatic($className)
    {

        /**
         * Create filters directory
         */
        $directory = app_path(join('/', array_filter(['Filters', 'Definitions'])));
        $filterParameter = join('_', ['filter', Str::snake($className)]);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0777, true);
        }

        /**
         * Fill up the template with the new class to be created
         */
        $stub = str_replace(
            ['{{className}}', '{{filterParameter}}'],
            [$className, $filterParameter],
            $this->files->get(__DIR__ . '/stubs/Filter.stub')
        );

        /**
         * Place the files where they belong within your application
         */
        $file = join('/', [$directory, $className . '.php']);

        $this->files->put($file, $stub);

        $this->info("\nThe Filter {$className} has been created successfully!");
    }


}
