<?php

namespace Petrelli\SearchInterfaceBuilder;


/**
 *
 * Base Filter that will allow multiple selected elements at the same time
 *
 */


class MultipleSelector
{

    // Filter URL parameter
    protected $parameter;

    // Filter Title
    protected $label;

    // Base Collection Route to build
    protected $route;

    // Instead of using a separator, build parameters as an array
    protected $asArray = false;

    // Separator used to split each selected value
    protected $separator = ',';


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

    public function buildRootRoute()
    {

        return route($this->route);

    }


    public function links()
    {
        $links = collect([]);

        foreach ($this->values() as $key => $label) {

            if ($this->asArray) {
                // Values will come as an array
                $input = collect(request()->input($this->parameter, []));
            } else {
                // Values will be separated in the same string
                $input = collect(explode($this->separator, request()->input($this->parameter)));
            }

            if ($active = $input->contains($key)) {
                // If this value is contained in the input, remove it
                // This way we can build the link to remove this filter

                $input = $input->forget($input->search($key))->filter();
            } else {
                // If this value is not contained, let's just add it so
                // We build a link that includes it to be selected

                $input = $input->push($key)->filter();
            }

            // Re-build parameters as separated values (by separator attribute)
            if ($input->isEmpty()) {
                $extraParams = [];
            } else {
                if ($this->asArray) {
                    $extraParams = [$this->parameter => $input->toArray()];
                } else {
                    $extraParams = [$this->parameter => join($this->separator, $input->toArray())];
                }
            }

            // Create a new filter item object containing all necessary data
            // To build proper links
            $object = new \StdClass();
            $object->label  = __($label);
            $object->value  = (string) $key;
            $object->active = $active;
            $object->url = $this->buildRoute($extraParams);
            $object->urlRoot = $this->buildRootRoute();

            $links->push($object);
        }

        return $links;
    }


}

