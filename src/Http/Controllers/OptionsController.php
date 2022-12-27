<?php
namespace Tomodo531\FilterableFilters\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;

class OptionsController
{
    public function options(Request $request)
    {
        $query = $request->input('model')::query();
        $fields = $request->input('fields');

        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                $query = $query->with($key);
            }
        }

        foreach ( $request->input('values') as $key => $value) {
            if (!empty($value)) {
                if ( is_array($fields[$key])) {
                    $key = $fields[$key]['foreignkey'];
                }
                $query = $query->where($key, '=', $value);
            }
        }

        $data = $query->get();

        $sortedOptions = [];

        $data->map(function ($item) use ($fields, &$sortedOptions) {
            return collect($item)->filter(function ($col, $key) use ($fields) {
                return Str::is(array_keys($fields), $key);
            })->map(function ($col, $key) use ($fields, &$sortedOptions) {
                if (is_array($fields[$key])) {
                    $sortedOptions[$key][] =
                        [
                            'value' => $col[$fields[$key]['primarykey']],
                            'label' => $col[$fields[$key]['title']],
                        ];
                } else {
                    $sortedOptions[$key][] =
                        [
                            'value' => $col,
                            'label' => $col,
                        ];
                }          
            });
        });

        $options = [];

        collect($sortedOptions)->map(function ($col, $key) use (&$options) {
            $options[] = [
                'label' => $key,
                'options' => array_unique($col, SORT_REGULAR)
            ];
        });

        return response()->json($options);
    }
}
