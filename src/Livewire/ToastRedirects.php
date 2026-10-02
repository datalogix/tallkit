<?php

namespace TALLKit\Livewire;

use TALLKit\TALLKit;

use function Livewire\on;
use function Livewire\store;

/** Actions that call a method are dropped: their component is on the page that went away. */
class ToastRedirects
{
    public static function register()
    {
        on('dehydrate', function ($component, $context) {
            if (! store($component)->get('redirect')) {
                return;
            }

            $isToast = fn ($js) => is_array($js) && ($js['expression'] ?? null) === '$tallkit.toast';
            $withoutToasts = fn ($items) => array_values(array_filter((array) $items, fn ($js) => ! $isToast($js)));

            // The store keeps every toast even after Livewire copies it to the `xjs` effect.
            $js = (array) store($component)->get('js', []);
            $pending = array_filter($js, $isToast);

            if ($pending === []) {
                return;
            }

            store($component)->set('js', $withoutToasts($js));

            if (isset($context->effects['xjs'])) {
                $context->effects['xjs'] = $withoutToasts($context->effects['xjs']);
            }

            $names = array_map(fn ($parameter) => $parameter->getName(), (new \ReflectionMethod(TALLKit::class, 'toast'))->getParameters());

            foreach ($pending as $item) {
                $params = array_values((array) ($item['params'] ?? []));
                $count = min(count($params), count($names));

                $toast = count($params) === 1 && is_array($params[0]) && ! array_is_list($params[0])
                    ? $params[0]
                    : array_combine(array_slice($names, 0, $count), array_slice($params, 0, $count));

                if (isset($toast['actions'])) {
                    $toast['actions'] = array_values(array_filter((array) $toast['actions'], fn ($action) => ! isset($action['method'])));
                }

                app(TALLKit::class)->flashToast(array_filter($toast, fn ($value) => $value !== null && $value !== []));
            }
        });
    }
}
