<?php

namespace Marshmallow\Zipcode\Nova;

use Closure;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class Zipcode extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'zipcode';

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @param  callable|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $name_2, $attribute_2 = null, $field_connections = [], callable $resolveCallback = null)
    {
        $this->name = $name;
        $this->name_2 = $name_2;
        $this->attribute_2 = $attribute_2 ?? str_replace(' ', '_', Str::lower($name_2));
        $this->resolveCallback = $resolveCallback;

        $this->default(null);

        $this->withMeta([
            'name_2' => $name_2,
            'attribute_2' => $attribute_2 ?? str_replace(' ', '_', Str::lower($name_2)),
            'field_connections' => $field_connections,
        ]);

        if (
            $attribute instanceof Closure ||
            (is_callable($attribute) && is_object($attribute))
        ) {
            $this->computedCallback = $attribute;
            $this->attribute = 'ComputedField';
        } else {
            $this->attribute = $attribute ?? str_replace(' ', '_', Str::lower($name));
        }

        $this->street('street')
            ->city('city')
            ->province('province')
            ->country('country')
            ->latitude('latitude')
            ->longitude('longitude');
    }

    public function street($field)
    {
        return $this->withMeta(['street' => $field]);
    }

    public function city($field)
    {
        return $this->withMeta(['city' => $field]);
    }

    public function province($field)
    {
        return $this->withMeta(['province' => $field]);
    }

    public function country($field)
    {
        return $this->withMeta(['country' => $field]);
    }

    public function latitude($field)
    {
        return $this->withMeta(['latitude' => $field]);
    }

    public function longitude($field)
    {
        return $this->withMeta(['longitude' => $field]);
    }

    protected function resolveAttribute($resource, $attribute)
    {
        $street = data_get($resource, str_replace('->', '.', $attribute));
        $number = data_get($resource, str_replace('->', '.', $this->attribute_2));
        return [
            $street, $number
        ];
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(
        NovaRequest $request,
        $requestAttribute,
        $model,
        $attribute
    ) {

        $value = $request->{$this->attribute};
        $value = explode(',', $value);

        $request->merge([
            $this->attribute => $value[0],
            $this->attribute_2 => $value[1],
        ]);

        if ($request->exists($this->attribute)) {
            $model->{$this->attribute} = $request[$this->attribute];
        }
        if ($request->exists($this->attribute_2)) {
            $model->{$this->attribute_2} = $request[$this->attribute_2];
        }
    }
}
