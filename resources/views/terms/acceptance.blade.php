@props([
    'toggle' => null,
    'termsOfService' => null,
    'termsOfUse' => null,
    'privacyPolicy' => null,
])
@php

$termsOfService ??= route_detect(['terms-of-service', 'terms.terms-of-service'], default: null);
$termsOfUse ??= route_detect(['terms-of-use', 'terms.terms-of-use'], default: null);
$privacyPolicy ??= route_detect(['privacy-policy', 'terms.privacy-policy'], default: null);

$link = fn (string $url, string $label) => '<a class="underline" href="'.e($url).'" target="_blank" rel="noopener noreferrer">'.__($label).'</a>';

$text = match (true) {
    $termsOfService && $termsOfUse && $privacyPolicy => __('I agree to the :terms-of-service, :terms-of-use and :privacy-policy', [
        'terms-of-service' => $link($termsOfService, 'Terms of Service'),
        'terms-of-use' => $link($termsOfUse, 'Terms of Use'),
        'privacy-policy' => $link($privacyPolicy, 'Privacy Policy'),
    ]),
    $termsOfService && $termsOfUse => __('I agree to the :terms-of-service and :terms-of-use', [
        'terms-of-service' => $link($termsOfService, 'Terms of Service'),
        'terms-of-use' => $link($termsOfUse, 'Terms of Use'),
    ]),
    $termsOfService && $privacyPolicy => __('I agree to the :terms-of-service and :privacy-policy', [
        'terms-of-service' => $link($termsOfService, 'Terms of Service'),
        'privacy-policy' => $link($privacyPolicy, 'Privacy Policy'),
    ]),
    $termsOfService => __('I agree to the :terms-of-service', [
        'terms-of-service' => $link($termsOfService, 'Terms of Service'),
    ]),
    $termsOfUse && $privacyPolicy => __('I agree to the :terms-of-use and :privacy-policy', [
        'terms-of-use' => $link($termsOfUse, 'Terms of Use'),
        'privacy-policy' => $link($privacyPolicy, 'Privacy Policy'),
    ]),
    $termsOfUse => __('I agree to the :terms-of-use', [
        'terms-of-use' => $link($termsOfUse, 'Terms of Use'),
    ]),
    $privacyPolicy => __('I agree to the :privacy-policy', [
        'privacy-policy' => $link($privacyPolicy, 'Privacy Policy'),
    ]),
    default => __('I accept the terms and conditions'),
};

@endphp
@if ($toggle)
    <tk:toggle :$attributes>{!! $text !!}</tk:toggle>
@else
    <tk:checkbox :attributes="$attributes->except('variant')">{!! $text !!}</tk:checkbox>
@endif
