# Search Interface Builder

Search Interface Builder is a Laravel package that will allow to easily manage search filters and sorters, creating url's and all of their options easily, including all links with information about the active/inactive state.

Together with [Scoped Controller](https://github.com/ferpetrelli/scoped-controller) your controller will be always decluttered.


# Installation

Include it in your composer.json file calling

```
composer require petrelli/search-interface-builder
```

Or add:

```
"petrelli/search-interface-builder": "0.0.*"
```

And run `composer update`.


# Usage

1. Create a new Section, each section can contain a collection of filters and/or a sorter. Be sure to inherit from `Petrelli\SearchInterfaceBuilder\BaseSection`

```php

class StaffFiltering extends Petrelli\SearchInterfaceBuilder\BaseSection
{

    protected $route = 'staff';

    protected $filters = [];
    
    protected $sorter;

}

```

2. Create Filters for that section.

As we are going to filter staff, lets filter by location.

```php

class Location extends Petrelli\SearchInterfaceBuilder\MultipleSelector
{

    protected $parameter = 'filter_location';

    protected $label     = 'Location';


    public function values()
    {

        return [
            'office' => __('In site'),
            'remote' => __('Remote'),
            'freelancer' => __('Freelancer'),
        ];

    }


}

```

The `values()` function is self explanatory, you just return an array of `'value' => 'label'` elements.


And also let's sort by name and age:

```php

class SortStaff extends Petrelli\SearchInterfaceBuilder\SingleSelector
{

    protected $parameter = 'sort_by';

    protected $label     = 'Sory By';


    public function values()
    {

        return [
            'name' => __('Name A-Z'),
            'name_desc' => __('Name Z-A'),
            'age' => __('Age'),
        ];

    }


}

```

Notice the class used here is `SingleSelector` instead of `MultipleSelector`.
The reason is that sorters should only use one value at the time.

You could easily create a filter with `SingleSelector` too if you need to filter using 1 value per filter definition.

3. Add those filters to the section

```php

// Filters
use App\Filters\Definitions\Location;

// Sorter
use App\Filters\Definitions\SortStaff;


class StaffFiltering extends Petrelli\SearchInterfaceBuilder\BaseSection
{

    protected $route = 'staff';

    protected $filters = [
        Location::class,
    ];
    
    protected $sorter = SortStaff::class;

}

```

4. Once definitions are set, we just use the object to manage and print our links.

From the controller we send the object to the view:

```php
return view('staff.index', [
    'filtering' => app(StaffsFiltering::class),
    // ...
]);

```

See next section to explore use cases.

## Use Cases

Once you finished your definition let's see what can you do with it.


## Print filter titles

Used to print the names of each filter to build the top section

```php
@foreach ($filters->filters() as $filter)
  <span>{{ $filter->label() }}</span>
@endforeach
```

## Print filter values

Here we print each one of the options

```php
@foreach ($filters->filters() as $filter)
    @foreach($filter->links() as $option)
    <a href="{{$option->url}}" class="@if($option->active) is-active @endif">
        {{ $option->label }}
    </a>
    @endforeach
@endforeach
```

You have more available attributes for each possible option:

`$option->label`   Label
`$option->value`   Value
`$option->active`  Boolean that indicates if is active
`$option->urlRoot` URL with no filters at all
`$option->url`     URL that contains or not the value depending if it's active or not

The last one `$option->url` deserves a special mention because it will include every other selected filter. Printing this value and using `$option->active` to mark it as selected or not will be enough to build the entire panel.

## Get all active filters

This one is used to build a list with all selected filters. The URL will automatically remove itself so we can build the classic list of selected items that can be removed.

```php
@if ($filters->activeFilters()->isNotEmpty())
    @foreach($filters->activeFilters() as $filter)
      <a href="{{$filter->url}}">{{ $filter->label }}</a>
    @endforeach

    <a href="{!! $filters->buildRootRoute() !!}">Clear all</a>
  </div>
@endif
```
