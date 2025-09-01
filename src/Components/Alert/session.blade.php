@session($name)
    <tk:alert
        :attributes="$attributes->merge(is_array($value) ? $value : ['message' => $value], false)"
    />
@endsession
