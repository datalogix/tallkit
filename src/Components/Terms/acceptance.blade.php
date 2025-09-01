<x-dynamic-component
    :$attributes
    :component="$checkbox ? 'tallkit-checkbox' : 'tallkit-toggle'"
>
    {!! match(true) {
        $termsOfService && !$privacyPolicy => __('I agree to the :terms-of-service', [
            'terms-of-service' => '<a class="underline" href="'.$termsOfService.'" target="_blank">'.__('Terms of Service').'</a>'
        ]),
        $termsOfService && $privacyPolicy => __('I agree to the :terms-of-service and :privacy-policy', [
            'terms-of-service' => '<a class="underline" href="'.$termsOfService.'" target="_blank">'.__('Terms of Service').'</a>',
            'privacy-policy' => '<a class="underline" href="'.$privacyPolicy.'" target="_blank">'.__('Privacy Policy').'</a>'
        ]),
        $termsOfUse && !$privacyPolicy => __('I agree to the :terms-of-use', [
            'terms-of-use' => '<a class="underline" href="'.$termsOfUse.'" target="_blank">'.__('Terms of Use').'</a>'
        ]),
        $termsOfUse && $privacyPolicy => __('I agree to the :terms-of-use and :privacy-policy', [
            'terms-of-use' => '<a class="underline" href="'.$termsOfUse.'" target="_blank">'.__('Terms of Use').'</a>',
            'privacy-policy' => '<a class="underline" href="'.$privacyPolicy.'" target="_blank">'.__('Privacy Policy').'</a>'
        ]),
        default => __('I accept the terms and conditions'),
    } !!}
</x-dynamic-component>
