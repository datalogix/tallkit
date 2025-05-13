<?php

namespace TALLKit\Components\CreditCard;

use TALLKit\Concerns\JsonOptions;
use TALLKit\View\BladeComponent;

class CreditCard extends BladeComponent
{
    use CardTypes;
    // use JsonOptions;

    protected function props()
    {
        return [
            'options' => null,
            'opened' => true,
            'types' => $this->getCardTypes(),
            'holderName' => null,
            'number' => null,
            'type' => null,
            'expirationDate' => null,
            'cvv' => null,
        ];
    }

    /* protected function processed(array $data)
    {
        $this->setOptions(array_replace_recursive([
            'opened' => $this->opened,
            'types' => $this->types,
            'holderName' => $this->holderName,
            'number' => $this->number,
            'type' => $this->type,
            'expirationDate' => $this->expirationDate,
            'cvv' => $this->cvv,
        ], $this->options ?? []));
    }*/
}
