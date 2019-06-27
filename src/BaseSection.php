<?php

namespace Petrelli\SearchInterfaceBuilder;


class BaseSection
{

    /**
     *
     * Filters classes to be processed.
     *
     */
    protected $filters = [];


    /**
     *
     * Sorting classes
     *
     */
    protected $sorter;


    /**
     *
     * Base route for this section. We assume all results will be presented
     * using the same URL.
     *
     * You can modify how to build URL's on each Filter class overloading
     * the buildRoute method
     *
     */
    protected $route;


    /**
     *
     * Generate and return a collection of filter objects
     *
     */
    public function filters()
    {
        $filters = collect([]);

        foreach($this->filters as $klass) {
            if (class_exists($klass)) {
                $filters->push(new $klass($this->route));
            }
        }

        return $filters;
    }


    /**
     *
     * Generate and return a sorter object
     *
     */
    public function sorter()
    {

        if ($klass = $this->sorter) {
            if (class_exists($klass)) {
                return new $klass($this->route);
            }
        }

    }


    /**
     *
     * Return active filters links to build a menu
     *
     */
    public function activeFilters()
    {
        $activeFilters = collect([]);

        // Walk through filters and extract all active ones
        foreach ($this->filters() as $filter) {
            foreach ($filter->links() as $item) {
                if ($item->active) {
                    $activeFilters->push($item);
                }
            }
        }

        return $activeFilters;
    }


}
