# Search Interface Builder

With this package you will be able to easily build and manage your search filters.

![A section](/docs/images/selected.png "Section")

You will be provided with helpers to show different filter sections, select/deselect links, and more, all just by defining a few classes.

# One example is worth a thousand words

Please check out [a very simple example](https://fernandopetrelli.com/filters) to illustrate the main funtionalities.

To perform an actual search, you can use it together with [Scoped Controller](https://github.com/ferpetrelli/scoped-controller) to trigger scope calls. This way your controller and your views will always be decluttered.

The complete view for this example is a few dozen lines:

```php
@foreach ($filtering->filters() as $filter)
    <div>
        <h4>{{ $filter->label() }}</h4>

        @foreach($filter->links() as $option)
            <a href="{{$option->url}}" @if($option->active) class="active" @endif>
                {{ $option->label }}
            </a>
        @endforeach
    </div>
@endforeach

@if ($filtering->activeFilters()->isNotEmpty())
    <div>
        <h4>Selected filters:</h4>

        @foreach($filtering->activeFilters() as $option)
            <a href="{{$option->url}}">{{ $option->label }}</a>
        @endforeach

        <a href="{!! route('filters') !!}">Clear all</a>
    </div>
@endif
```

The entire logic to build all URL's is managed automatically by our Sections and Filters classes.


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


# Important Concepts


## Section

A section is a collection of possible filters and/or a sorter. You can define as many sections as you need for your application.

![A section](/docs/images/bar.png "Section")


## Filter

A Filter is a specific category to be used to filter your collection (for example, Location and Department on the previous image).

You can reutilize filters across your sections.


# Directory structure

I recommend placing all classes under `app/Filters`

![A section](/docs/images/sections.png "Section")


* `app/Filters/Definitions`: Here place all of your filters classes (Location, Year, Department, etc.). This way you will be able to reutilize these definitions in any section that needs them.
* `app/Filters/Sections`: Here place all of your sections (main search, staff search, events search, etc.)

# Usage

## 1. Create a new empty Section.

Be sure to inherit from `Petrelli\SearchInterfaceBuilder\BaseSection`

```php

namespace App\Filters\Sections;

class StaffFiltering extends Petrelli\SearchInterfaceBuilder\BaseSection
{

    protected $route = 'staff';

    protected $filters = [];

    protected $sorter;

}

```

## 2. Create Filters for that section.


Following our image, let's create the Location filter (creating the Department filter is the same process).

```php

namespace App\Filters\Definitions;

class Location extends Petrelli\SearchInterfaceBuilder\MultipleSelector
{

    protected $parameter = 'filter_location';

    protected $label     = 'Location';


    public function values()
    {

        return [
            'ny' => __('New York'),
            'nb' => __('Nairobi'),
            'fr' => __('France'),
            //... Add all options
        ];

    }


}

```

Here you need to define 3 things:

* `$parameter`: The URL parameter to be used for this filter.

* `$label`: Label to be printed.

* `values()`: array of `['value' => 'label', ...]` elements. You can load these values from the database, an API, hardcoded, etc.

Optional:

* `$separator`: character used to separate values on the URL. By defaut is `,`.


Let's create the sorter:

```php

namespace App\Filters\Definitions;

class SortStaff extends Petrelli\SearchInterfaceBuilder\SingleSelector
{

    protected $parameter = 'sort_by';

    protected $label     = 'Sory By';


    public function values()
    {

        return [
            'name' => __('Name A-Z'),
            'name_desc' => __('Name Z-A'),
        ];

    }


}

```

A sorter is actually a filter, only that we should always inherit from `SingleSelector` instead of a `MultipleSelector` to allow only one option selected at a time.

Of course you can also create filters from `SingleSelector`. (Year, in our live example link).


## 3. Add those filters to the section

```php

// Filters
use App\Filters\Definitions\Department;
use App\Filters\Definitions\Location;

// Sorter
use App\Filters\Definitions\SortStaff;


class StaffFiltering extends Petrelli\SearchInterfaceBuilder\BaseSection
{

    protected $route = 'staff';

    protected $filters = [
        Department::class,
        Location::class,
    ];

    protected $sorter = SortStaff::class;

}

```

## 4. That's it, now just use the Section object in your view

From the controller we send the object to the view:

```php
return view('staff.index', [
    'filtering' => app(StaffsFiltering::class),
    // ...
]);

```

### Print filter titles

Used to print the names of each filter to build the top section:

![A section](/docs/images/filters.png "Section")


```php
@foreach ($filtering->filters() as $filter)
  <span>{{ $filter->label() }}</span>
@endforeach
```

### Print all filter options

Here we print each one of the options:

![A section](/docs/images/selected.png "Section")

```php
@foreach ($filtering->filters() as $filter)
    <span>{{ $filter->label() }}</span>

    @foreach($filter->links() as $option)
        <a href="{{$option->url}}" class="@if($option->active) is-active @endif">
            {{ $option->label }}
        </a>
    @endforeach

@endforeach
```

Each option object will contain the following attributes:

```php
$option->label   // Label
$option->value   // Value
$option->active  // Boolean that indicates if is active
$option->urlRoot // URL with no filters at all
$option->url     // URL that contains or not the value depending if it's active (url-present) or not
```

For 99% of use cases you will only have to use `url`, `active`, and `label`.

### Print a list of currently selected filters

![A section](/docs/images/selected-list.png "Section")

```php
@if ($filters->activeFilters()->isNotEmpty())
    @foreach($filters->activeFilters() as $filter)
      <a href="{{$filter->url}}">{{ $filter->label }}</a>
    @endforeach

    <a href="{!! route('staff') !!}">Clear all</a>
  </div>
@endif
```

This list will include all selected options within all filters.

# TODO

* Files Generator
* Improve documentation

# License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
