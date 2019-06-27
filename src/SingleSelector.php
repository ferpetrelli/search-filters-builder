<?php

namespace Petrelli\SearchInterfaceBuilder;


/**
 *
 * Base Filter class with a single selected element at the time
 *
 */


class SingleSelector
{

    // Filter URL parameter
    protected $parameter;

    // Filter Title
    protected $label;

    // Base Collection Route to build
    protected $route;


    public function __construct($route)
    {

        $this->route = $route;

    }


    public function label()
    {

        return __($this->label);

    }


    public function parameter()
    {

        return $this->parameter;

    }


    public function values()
    {

        return [];

    }


    public function buildRoute($extraParams)
    {

        return route($this->route, request()->except(['page', $this->parameter]) + $extraParams);

    }


    public function activeLabel()
    {

        $link = $this->links()->firstWhere('active', true) ?? $this->links()->first();

        return $link->label;

    }


    public function links()
    {
        $links = collect([]);

        foreach ($this->values() as $key => $label) {

            // Here we can only select one value, so we check for it
            // Instead of using comma separated values.
            $input = request()->input($this->parameter);

            if ( $active = $key == $input ) {
                $input = [];
            } else {
                $input = [ $this->parameter => $key ];
            }

            // Create a new filter item object containing all necessary data
            // To build proper links
            $object = new \StdClass();
            $object->label  = __($label);
            $object->value  = $key;
            $object->active = $active;
            $object->url = $this->buildRoute($input);

            $links->push($object);

        }

        return $links;
    }


}


