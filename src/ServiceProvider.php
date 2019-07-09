<?php

namespace Petrelli\SearchInterfaceBuilder;

use Petrelli\SearchInterfaceBuilder\Commands\CreateFilter;
use Petrelli\SearchInterfaceBuilder\Commands\CreateSection;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{


    public function register()
    {

        // Register Commands
        $this->commands([
            CreateFilter::class,
            CreateSection::class
        ]);

    }


    public function boot()
    {
    }


}
