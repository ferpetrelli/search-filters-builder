# Search Interface Builder

Build and maintain filters for your listings.

Everything will be automatically managed. URL's, Values, Labels, etc. by just defining a few classes.

# Live Example

Please [visit here](https://fernandopetrelli.com/filters) and click around.

The complete view for this example is a just few lines:

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


# Performing an actual search

The way an actual search is triggered on your data sources is entirely up to you.

This package will provide you with a simple and flexible way to build your filters and sorters, but connecting the generated URL's will depend on your application.

## Executing scopes

To close this gap, I recommend using the [Scoped Controller](https://github.com/ferpetrelli/scoped-controller) package.

It's solely functionality is to execute scopes over your query builders depending on your URL's, which makes it a perfect fit.

Your controllers and views will always be decluttered this way.


# Installation

Include it in your composer.json file calling

```
composer require petrelli/search-interface-builder
```

Or add:

```
"petrelli/search-interface-builder": "^0.0.2@alpha"
```

And run `composer update`.

## Service provider

Only if you have disabled Laravel's auto-discover, you'll have to manually add the service provider to your `config/app.php` file.

```php
'providers' => [
    //...
    Petrelli\SearchInterfaceBuilder\ServiceProvider::class,
    //...
]
```

# Important Concepts

## Section

A section is a collection of filters and/or a sorter. You can define as many sections as you want.

![A section](/docs/images/bar.png "Section")


## Filter

A Filter is a specific category to filter your collection (for example, Department and Location on the previous image).

You can reutilize filters across your sections.


# Directory structure

By default everything will be located under `App/Filters`.

* `App/Filters/Definitions`: Filters classes and sorters (Location, Year, ByPrice, etc.).
* `App/Filters/Sections`: Main search, staff search, events search, etc.


# Usage

## 1. Create a new empty Section.

```
php artisan search-builder:section [name] [route.name?]
```

This will generate a new Section class inside `App\Filters\Sections`.

If you don't specify a route please open the generated file and update it.


## 2.1 Create Filters


```
php artisan search-builder:filter [name]
```

This will generate a new Filter class inside `App\Filters\Definitions`.


```php
namespace App\Filters\Definitions;

class Location extends Petrelli\SearchInterfaceBuilder\MultipleSelector
{

    protected $parameter = 'filter_location';

    protected $label     = 'Location';


    public function values()
    {

        //... Always return a [value => label ...] associative array
        return [
            'ny' => __('New York'),
            'nb' => __('Nairobi'),
            'fr' => __('France'),
        ];

    }

}
```

Here you can adjust 3 things:

* `$parameter`: The URL parameter to be used for this filter.

* `$label`: Label to be printed.

* `values()`: array of `['value' => 'label', ...]` elements. You can load these values from the database, an API, hardcoded, etc.

Optional:

* `$asArray`: Send this URL parameter as an array instead of a single string with a separator. By defaut is `false`.

* `$separator`: character used to separate values on the URL. By defaut is `,`.


## 2.2 Create a Sorter (optional)


A sorter is actually a filter, so the step to create it is the same as `2.1`.

The only difference is that we should only allow one selected option at a time.

To acomplish this you have to inherit from `SingleSelector` instead of a `MultipleSelector`.


## 3. Add those filters to the section

The following example Section contains two filters (Department and Location), and a sorter (SortStaff).

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

## 4. Use the Section object in your view

From the controller we send the object to the view:

```php
return view('staff.index', [
    'filtering' => app(StaffsFiltering::class),
    // ...
]);

```

And from them we can print everything in our views.


## Usage: Print filter titles

Used to print the names of each filter to build the top section:

![A section](/docs/images/filters.png "Section")


```php
@foreach ($filtering->filters() as $filter)
  <span>{{ $filter->label() }}</span>
@endforeach
```

## Usage: Print all filter options

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

Each option object contains the following attributes:

```php
$option->label   // Label
$option->value   // Value
$option->active  // Boolean that indicates if is active
$option->urlRoot // URL with no filters at all
$option->url     // URL that contains or not the value depending if it's active (url-present) or not
```

For 99% of use cases you will only have to use `url`, `active`, and `label`.

## Usage: Print a list with the selected filters

![A section](/docs/images/selected-list.png "Section")

As you will see at the live example after selecting a few filters, that list can be automatically generated.

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

## Usage: Create a filter that allows only one selected element

To acomplish this you have to inherit from `SingleSelector` instead of `MultipleSelector`.

```php
class Location extends Petrelli\SearchInterfaceBuilder\SingleSelector
```

## Usage: Build a custom route for a specific filter

Just overload the `buildRoute` function inside your filter class.

```php
public function buildRoute($extraParams)
{

    return route($this->route, request()->except(['page', $this->parameter]) + $extraParams);

}
```

# TODO

* Improve documentation
* Examples
* Tests

# License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
