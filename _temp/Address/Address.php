<?php

namespace TALLKit\Components\Address;

use TALLKit\View\BladeComponent;

class Address extends BladeComponent
{
    public function __construct(
        public ?array $data = null,
        public ?string $zipcode = null,
        public ?string $address = null,
        public ?string $number = null,
        public ?string $complement = null,
        public ?string $neighborhood = null,
        public ?string $city = null,
        public ?string $state = null,
    ) {
        $this->zipcode ??= data_get($this->data, 'zipcode');
        $this->address ??= data_get($this->data, 'address');
        $this->number ??= data_get($this->data, 'number');
        $this->complement ??= data_get($this->data, 'complement');
        $this->neighborhood ??= data_get($this->data, 'neighborhood');
        $this->city ??= data_get($this->data, 'city');
        $this->state ??= data_get($this->data, 'state');
    }
}
