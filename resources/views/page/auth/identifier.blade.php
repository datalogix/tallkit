@aware(['size', 'identifier'])
@props([
    'size' => null,
    'identifier' => null
])
<tk:input
    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: match ($identifier) {
        'cpf' => 'cpf:',
        'username' => 'username:',
        'both' => 'login:',
        default => 'email:',
    })"
    :$size
    :name="match ($identifier) {
        'cpf' => 'cpf',
        'username' => 'username',
        'both' => 'login',
        default => 'email',
    }"
    :label="match ($identifier) {
        'cpf' => 'CPF',
        'username' => 'Username',
        'both' => 'Login',
        default => 'Email',
    }"
    :placeholder="match ($identifier) {
        'cpf' => '000.000.000-00',
        'username' => 'Username',
        'both' => 'Login',
        default => 'email@example.com',
    }"
    :autocomplete="match ($identifier) {
        'cpf' => null,
        'username', 'both' => 'username',
        default => 'email',
    }"
    :mask="match ($identifier) {
        'cpf' => '999.999.999-99',
        default => null,
    }"
    autofocus
    required
/>
