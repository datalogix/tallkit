@aware(['size', 'identifier'])
@props([
    'size' => null,
    'identifier' => null
])
<tk:input
    :attributes="$attributes->merge(['placeholder' => match ($identifier) {
        'cpf' => '000.000.000-00',
        'cnpj' => '00.000.000/0000-00',
        'username' => 'Username',
        'login' => 'Login',
        default => 'email@example.com',
    }])"
    :$size
    :name="match ($identifier) {
        'cpf' => 'cpf',
        'cnpj' => 'cnpj',
        'username' => 'username',
        'login' => 'login',
        default => 'email',
    }"
    :label="match ($identifier) {
        'cpf' => 'CPF',
        'cnpj' => 'CNPJ',
        'username' => 'Username',
        'login' => 'Login',
        default => 'Email',
    }"
    :autocomplete="match ($identifier) {
        'cpf' => null,
        'cnpj' => null,
        'username' => 'username',
        'login' => 'username',
        default => 'email',
    }"
    :mask="match ($identifier) {
        'cpf' => 'cpf',
        'cnpj' => 'cnpj',
        default => null,
    }"
    autofocus
    required
/>
