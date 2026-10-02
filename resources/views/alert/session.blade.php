@props([
    'name' => 'status',
    'title' => null,
    'prepend' => null,
    'append' => null,
    'actions' => null,
])
@session($name)
    @foreach (is_array($value) && array_is_list($value) ? $value : [$value] as $alert)
        @php
            $alertAttributes = TALLKit::attributesWithProps($attributes, get_defined_vars(), ['title' => null, 'prepend' => null, 'append' => null, 'actions' => null, 'pauseOnHover' => null])
                ->merge(is_array($alert) ? $alert : ['message' => $alert], false);

            if ($loop->index && $alertAttributes->has('id')) {
                $alertAttributes['id'] = $alertAttributes->get('id').'-'.$loop->iteration;
            }
        @endphp
        <tk:alert :attributes="$alertAttributes">
            {{ $slot }}
        </tk:alert>
    @endforeach
@endsession
