<?php

namespace Tomodo531\FilterableFilters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class FilterableFilters extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'filterable-filters';

    private $storedFields = [];

    public function __construct($model)
    {
        $this->meta = [
            'model' => $model,
        ];
    }

    public function fields($fields = [])
    {
        foreach ($fields as $key => $field) {
            if (!is_array($field)) {
                $fields[$field] = $field;
                unset($fields[$key]);
            }
        }

        $this->storedFields = $fields;

        $this->withMeta([
            'fields' => $fields
        ]);

        return $this;
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        ray($value);
        foreach ($value as $key => $selectedValue) {
            if (!empty($selectedValue)) {
                if ( is_array($this->storedFields[$key])) {
                    $key = $this->storedFields[$key]['foreignkey'];
                }
                $query = $query->where($key, $selectedValue);
            }
        }

        return $query;
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [];
    }
}
