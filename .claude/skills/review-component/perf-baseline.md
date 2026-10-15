# Performance baseline

## How to measure

- Server render: from `../app`, run
  `XDEBUG_MODE=off php -dopcache.enable_cli=1 ../tallkit/.claude/skills/review-component/bench.php '<template>' ...`
  with one Blade template per argument. It warms up, then prints the best ms per render of
  five runs of 1000. Herd's PHP has Xdebug on by default, which inflates timings about 8x.
- Run it twice and discard the first: right after views are recompiled the first process is
  2 to 3x slower. Compare before and after under the same conditions.
- Browser, only in a run with `perf`: `measureInit` from the `browser-test` skill, on a
  wrapper with a few hundred instances, and again without the component to get the cost per
  instance.

## Recorded numbers

| Component | Case | Result | Date |
|-----------|------|--------|------|
| button | `<tk:button>Save</tk:button>` | 0.12 ms | 2026-10-13 |
| button | `icon="close"` | 0.17 ms | 2026-10-13 |
| button | `tooltip="Hi"` | 0.15 ms | 2026-10-13 |
| badge | `<tk:badge>New</tk:badge>` | 0.15 ms | 2026-10-13 |
| badge | `<tk:badge closable>Tag</tk:badge>` | 0.47 ms | 2026-10-13 |
| badge | `<tk:badge href="#x" closable>Tag</tk:badge>` | 0.54 ms | 2026-10-13 |
| input | `<tk:input name="a" />` | 0.39 ms | 2026-10-13 |
| input | `<tk:input />` | 0.16 ms | 2026-10-13 |
| button + input | 300 buttons and 30 inputs, Alpine init | ~15 ms | 2026-10-02 |
| button + input | 300 buttons and 30 inputs, script | ~14 ms | 2026-10-02 |
| clearable | `<tk:clearable />` | 0.24 ms | 2026-10-13 |
| clearable | `<tk:input name="a" clearable />` | 0.65 ms | 2026-10-13 |
| address | `<tk:address.form />` | 4.21 ms | 2026-10-13 |
| address | `<tk:address.form name="billing" title="Billing" card />` | 4.69 ms | 2026-10-13 |
| address | `<tk:address zipcode="01001000" address="Praça da Sé" number="100" city="São Paulo" state="SP" />` | 0.28 ms | 2026-10-13 |
| alert | `<tk:alert message="Hi" />` | 0.34 ms | 2026-10-13 |
| alert | `type="danger" title="Title" message="Hi" closable` | 0.72 ms | 2026-10-13 |
| alert | `message="Hi" duration="3000" progress closable` | 0.82 ms | 2026-10-13 |
| appearance | `<tk:appearance.toggle />` | 0.30 ms | 2026-10-13 |
| appearance | `<tk:appearance.selector />` | 0.79 ms | 2026-10-13 |
| appearance | `<tk:appearance />` | 0.03 ms | 2026-10-13 |
| appearance | `<tk:appearance.menu name="Ada" :items="[...]" />` with 1 item | 3.08 ms | 2026-10-13 |
| autocomplete | `<tk:autocomplete name="a" />` | 0.57 ms | 2026-10-13 |
| autocomplete | `:options` with 5 items | 2.12 ms (~0.3 ms per item) | 2026-10-13 |
| autocomplete | `:options` with 100 items | 30.55 ms (~0.3 ms per item) | 2026-10-13 |
| clearable | 300 inputs with `x-model` and clearable, `Alpine.initTree` | ~60 ms (~25 ms without clearable) | 2026-10-13 |
| accordion | `<tk:accordion>` with 1 item with `label` | 0.37 ms | 2026-10-13 |
| accordion | `<tk:accordion>` with 5 items with `label` | 1.77 ms (~0.35 ms per item) | 2026-10-13 |
| apexcharts | `<tk:apexcharts />` | 0.31 ms (`<tk:loadable>x</tk:loadable>` alone: 0.27 ms) | 2026-10-13 |
| apexcharts | `:options` with 1 series of 3 points | 0.31 ms | 2026-10-13 |
| aside | `<tk:aside>x</tk:aside>` | 0.04 ms | 2026-10-13 |
| aside | `<tk:aside sticky aria-label="Related">x</tk:aside>` | 0.05 ms | 2026-10-13 |
| avatar | `<tk:avatar name="Maria Silva" :user="false" />` | 0.12 ms | 2026-10-13 |
| avatar | `name src color="auto" tooltip :user="false"` | 0.16 ms | 2026-10-13 |
| avatar | `<tk:avatar.profile name email :user="false" />` | 0.54 ms | 2026-10-13 |
| avatar | `<tk:avatar.group>` with 3 items (`name`, `:user="false"`) | 0.41 ms (~0.13 ms per item) | 2026-10-13 |
| brand | `<tk:brand />` (logo file found) | 0.19 ms | 2026-10-13 |
| brand | `logo logo-dark name="Acme"` | 0.26 ms | 2026-10-13 |
| breadcrumb | `<tk:breadcrumb :items>` with 1 item (`label`, `href`) | 0.29 ms | 2026-10-13 |
| breadcrumb | `<tk:breadcrumb :items>` with 3 items | 0.76 ms (~0.25 ms per item) | 2026-10-13 |
| breadcrumb | `<tk:breadcrumb>` with 3 `<tk:breadcrumb.item label href />` | 0.92 ms | 2026-10-13 |
| breadcrumb | `<tk:breadcrumb.separator />` | 0.06 ms | 2026-10-13 |
| button | `<tk:button wire:click="save">Save</tk:button>` (with loading indicator) | 0.25 ms | 2026-10-13 |
| button | `<tk:button.group>` with 2 buttons | 0.27 ms | 2026-10-13 |
| button/menu | `<tk:button.menu :items>` with 1 item | 0.55 ms (same parts written by hand: 0.51 ms) | 2026-10-13 |
| button/menu | `<tk:button.menu :items>` with 3 items | 0.93 ms (~0.19 ms per item) | 2026-10-13 |
| button/group | `<tk:button.group>x</tk:button.group>` (wrapper alone) | 0.02 ms | 2026-10-13 |
| calendar | `<tk:calendar name="a" />` | 0.71 ms | 2026-10-14 |
| calendar | `range months="2" today selectable-header week-numbers` | 1.15 ms | 2026-10-14 |
| card | `<tk:card>x</tk:card>` | 0.11 ms | 2026-10-14 |
| card | `title subtitle image alt icon badge` + footer slot | 0.72 ms | 2026-10-14 |
| carousel | `<tk:carousel :slides>` with 3 images | 0.78 ms | 2026-10-14 |
| carousel | `autoplay progress thumbnails` with 3 images | 1.06 ms | 2026-10-14 |
| carousel | 3 `<tk:carousel.slide>` in the slot | 0.67 ms | 2026-10-14 |
| chartjs | `<tk:chartjs />` | 0.32 ms | 2026-10-14 |
| chartjs | `:options` with 1 dataset of 3 points | 0.32 ms | 2026-10-14 |
| checkbox | `<tk:checkbox name="a" label="Accept" />` | 0.39 ms | 2026-10-14 |
| checkbox | `<tk:checkbox.group name="c" :options>` with 3 items | 1.52 ms (~0.4 ms per item) | 2026-10-14 |
| checkbox | `<tk:checkbox.all />` | 0.45 ms | 2026-10-14 |
| color-picker | `<tk:color-picker name="a" />` | 1.20 ms | 2026-10-14 |
| color-picker | `<tk:color-picker name="a" trigger="button" />` | 1.20 ms | 2026-10-14 |
| color-picker | `<tk:color-picker.button name="a" />` | 0.77 ms | 2026-10-14 |
| combobox | `<tk:combobox name="a" />` | 1.59 ms | 2026-10-14 |
| combobox | `:options` with 5 items | 2.05 ms | 2026-10-14 |
| combobox | `:options="range(1, 100)"` | 4.35 ms (~0.03 ms per item, stamped template) | 2026-10-14 |
| combobox | `trigger="input" multiple` with 5 items | 1.89 ms | 2026-10-14 |
| command | `<tk:command>` with 3 `<tk:listbox.item>` | 1.56 ms | 2026-10-14 |
| command | `<tk:command.modal name="c">` with 1 item in `content` | 1.56 ms | 2026-10-14 |
| radio | `<tk:radio name="a" label="A" />` | 0.33 ms | 2026-10-14 |
| radio | `<tk:radio.group name="c" :options>` with 3 items | 1.34 ms (~0.4 ms per item) | 2026-10-14 |
| composer | `<tk:composer name="m" />` | 0.53 ms | 2026-10-14 |
| composer | `submit` + `actionsTrailing` slot with 1 button | 0.73 ms | 2026-10-14 |
| container | `<tk:container>x</tk:container>` | 0.03 ms | 2026-10-14 |
| container | `<tk:container.wrapper container="sm">x</tk:container.wrapper>` | 0.05 ms | 2026-10-14 |
| content | `<tk:content title="T" />` | 0.16 ms | 2026-10-14 |
| content | `title description icon` + slot + `actions` | 0.46 ms | 2026-10-14 |
| context | `<tk:context>` with a div and a `tk:menu` of 2 items | 0.460 ms | 2026-10-14 |
| copyable | `<tk:copyable content="abc" />` | 0.35 ms | 2026-10-14 |
| copyable | `<tk:input name="a" copyable />` | 0.79 ms (input alone: 0.39 ms) | 2026-10-14 |
| credit-card | `<tk:credit-card />` | 0.10 ms | 2026-10-14 |
| danger-zone | `<tk:danger-zone modal:confirm="remove" />` (alert + modal.confirm) | 1.989 ms | 2026-10-14 |
| date-picker | `<tk:date-picker name="d" />` | 1.82 ms | 2026-10-14 |
| date-picker | `range presets confirm inputs` (10 presets) | 3.78 ms | 2026-10-14 |
| date-picker | `trigger="input"` | 1.61 ms | 2026-10-14 |
| display | `<tk:display label="Total" value="R$ 10,00" />` | 0.33 ms | 2026-10-14 |
| dropdown | `<tk:dropdown>` + `dropdown.button` + `tk:menu` with 2 items | 0.743 ms | 2026-10-14 |
| dropdown | `<tk:dropdown.button label="Options" />` | 0.322 ms | 2026-10-14 |
| echarts | `<tk:echarts />` | 0.32 ms | 2026-10-14 |
| echarts | `:options` with 1 series of 3 points | 0.32 ms | 2026-10-14 |
| editorjs | `<tk:editorjs name="body" label="Body" />` | 0.381 ms | 2026-10-14 |
| element | `<tk:element>Text</tk:element>` | 0.035 ms | 2026-10-14 |
| element | `href icon badge kbd` | 0.383 ms | 2026-10-14 |
| element | `<tk:text>Hi</tk:text>` (element.wrapper) | 0.135 ms | 2026-10-14 |
| error | `<tk:error name="email" />` without errors | 0.02 ms | 2026-10-14 |
| error | `<tk:error>Custom</tk:error>` | 0.08 ms | 2026-10-14 |
| error | `<tk:error.group />` without errors | 0.02 ms | 2026-10-14 |
| fetchable | `<tk:fetchable url="/x" />` (JSON view) | 0.592 ms | 2026-10-14 |
| fetchable | `chart="chartjs"` | 0.595 ms | 2026-10-14 |
| field | `<tk:field>x</tk:field>` | 0.019 ms | 2026-10-14 |
| field | `<tk:field.group label prefix>` with a plain input | 0.433 ms | 2026-10-14 |
| field | `<tk:input>` with `prefix` and `suffix` | 0.801 ms (input alone: 0.39 ms) | 2026-10-14 |
| fieldset | `<tk:fieldset>x</tk:fieldset>` | 0.03 ms | 2026-10-14 |
| fieldset | `label description` | 0.35 ms | 2026-10-14 |
| footer | `<tk:footer>x</tk:footer>` | 0.041 ms | 2026-10-14 |
| footer | `container sticky variant="inverse"` | 0.086 ms | 2026-10-14 |
| form | `<tk:form action="/s">` with 1 input (submit added) | 0.727 ms | 2026-10-14 |
| form | `<tk:form.card title>` with 1 input | 1.135 ms | 2026-10-14 |
| frappe-charts | `<tk:frappe-charts />` | 0.293 ms | 2026-10-14 |
| frappe-charts | `:options` with 1 dataset | 0.296 ms | 2026-10-14 |
| full-calendar | `<tk:full-calendar />` | 0.28 ms | 2026-10-14 |
| full-calendar | `:options` with 1 event | 0.28 ms | 2026-10-14 |
| google | `<tk:google.fonts families="Inter:wght@400;700" />` | 0.063 ms | 2026-10-14 |
| google | `<tk:google.gtag id="G-TEST" />` | 0.029 ms | 2026-10-14 |
| google | `<tk:google.gtm id="GTM-TEST" />` | 0.029 ms | 2026-10-14 |
| header | `<tk:header>x</tk:header>` | 0.060 ms | 2026-10-14 |
| header | `sticky container` | 0.080 ms | 2026-10-14 |
| heading | `<tk:heading level="2">Title</tk:heading>` | 0.13 ms | 2026-10-14 |
| highlightjs | `<tk:highlightjs language="php" code="echo 1;" />` | 0.296 ms | 2026-10-14 |
| html | `<tk:html>x</tk:html>` (once per page: toast, appearance, scripts) | 3.449 ms | 2026-10-14 |
| html | `meta google-fonts="Inter"` | 3.591 ms | 2026-10-14 |
| icon | `<tk:icon icon="home" />` | 0.028 ms | 2026-10-14 |
| icon | `tooltip="Home"` | 0.048 ms | 2026-10-14 |
| input | `type="password"` (viewable) | 0.672 ms | 2026-10-14 |
| input | `mask="(99) 99999-9999"` | 0.382 ms | 2026-10-14 |
| kbd | `<tk:kbd>Ctrl K</tk:kbd>` | 0.11 ms | 2026-10-14 |
| label | `<tk:label for="x">Name</tk:label>` | 0.107 ms | 2026-10-14 |
| label | `label-append info` | 0.186 ms | 2026-10-14 |
| layout | `<tk:layout.header>x</tk:layout.header>` | 1.49 ms | 2026-10-14 |
| layout | `<tk:layout.sidebar>x</tk:layout.sidebar>` | 1.27 ms | 2026-10-14 |
| layout | `<tk:layout.auth>x</tk:layout.auth>` | 0.70 ms | 2026-10-14 |
| layout | `<tk:layout.error />` | 0.23 ms | 2026-10-14 |
| legend | `<tk:legend label="Address" />` | 0.16 ms | 2026-10-14 |
| link | `<tk:link href="/x">Go</tk:link>` | 0.21 ms | 2026-10-14 |
| list | `<tk:list :items>` with 3 items | 0.42 ms (~0.13 ms per item) | 2026-10-14 |
| listbox | `<tk:listbox>` with 3 `listbox.item` (search + no-records) | 1.41 ms | 2026-10-14 |
| loadable | `<tk:loadable>x</tk:loadable>` | 0.28 ms | 2026-10-14 |
| loading | `<tk:loading />` | 0.053 ms | 2026-10-14 |
| loading | `variant="dots" :announce="false"` | 0.059 ms | 2026-10-14 |
| main | `<tk:main>x</tk:main>` | 0.03 ms | 2026-10-14 |
| menu | `<tk:menu>` with 3 items | 0.549 ms | 2026-10-14 |
| menu | `<tk:menu.radio.group>` with 2 items | 0.920 ms | 2026-10-14 |
| menu | `<tk:menu.submenu>` with 1 item | 0.527 ms | 2026-10-14 |
| modal | `<tk:modal name title>x</tk:modal>` | 0.601 ms | 2026-10-14 |
| modal | `<tk:modal.confirm>` with trigger | 1.255 ms | 2026-10-14 |
| money | `<tk:money name="p" currency="BRL" />` | 0.458 ms | 2026-10-14 |
| nav | `<tk:nav :items>` with 3 items + indicator | 0.631 ms | 2026-10-14 |
| notification | `<tk:notification :items>` with 2 items (dropdown, tabs) | 2.742 ms | 2026-10-14 |
| otp | `<tk:otp name="code" />` (6 boxes) | 1.697 ms | 2026-10-14 |
| otp | `format="999-999"` (2 groups) | 2.050 ms | 2026-10-14 |
| page | `<tk:page.auth.login />` (with oauth) | 2.498 ms | 2026-10-14 |
| page | `<tk:page.auth.sign-up />` | 3.748 ms | 2026-10-14 |
| page | `<tk:page.error code="404" />` | 0.472 ms | 2026-10-14 |
| page | `<tk:page :menu>` with 2 items (nav + select) | 0.826 ms | 2026-10-14 |
| pagination | LengthAwarePaginator, 95 items, page 5 (10 page links) | 3.684 ms | 2026-10-14 |
| password | `<tk:password />` (viewable button) | 0.73 ms | 2026-10-14 |
| popover | `<tk:popover>x</tk:popover>` | 0.081 ms | 2026-10-14 |
| pretty-print-json | `<tk:pretty-print-json :data>` | 0.278 ms | 2026-10-14 |
| progress | `<tk:progress value="40" />` | 0.201 ms | 2026-10-14 |
| quill | `<tk:quill name="body" label="Body" />` | 0.419 ms | 2026-10-14 |
| section | `<tk:section title subtitle>x</tk:section>` | 0.383 ms | 2026-10-14 |
| select | `<tk:select :options>` with 3 options | 0.469 ms | 2026-10-14 |
| separator | `<tk:separator />` | 0.019 ms | 2026-10-14 |
| separator | `label="or"` | 0.025 ms | 2026-10-14 |
| sidebar | `<tk:sidebar sticky stashable>x</tk:sidebar>` (with backdrop) | 0.055 ms | 2026-10-14 |
| sidebar | `<tk:sidebar.toggle />` | 0.270 ms | 2026-10-14 |
| skeleton | `<tk:skeleton.group animate="shimmer">` with circle + 2 lines | 0.09 ms | 2026-10-14 |
| slider | `<tk:slider name="v" />` | 0.345 ms | 2026-10-14 |
| slider | `show-value :ticks` with 3 ticks | 0.891 ms | 2026-10-14 |
| spacer | `<tk:spacer />` | 0.03 ms | 2026-10-14 |
| status | `<tk:status value title description />` | 0.49 ms | 2026-10-14 |
| stepper | `<tk:stepper :items>` with 3 steps | 0.837 ms | 2026-10-14 |
| submit | `<tk:submit />` (with loading indicator) | 0.29 ms | 2026-10-14 |
| switch | `<tk:switch name label />` | 0.315 ms | 2026-10-14 |
| switch | `label-on label-off` | 0.438 ms | 2026-10-14 |
| switch | `<tk:switch.all />` | 0.332 ms | 2026-10-14 |
| tab | `<tk:tab.group>` with 3 tabs and 3 panels | 0.773 ms | 2026-10-14 |
| table | 3 rows x 1 col (plus id) | 0.535 ms | 2026-10-14 |
| table | same with row-selection toggleable resizable draggable pinnable | 1.777 ms | 2026-10-14 |
| terms | `<tk:terms.acceptance name terms-of-service privacy-policy />` | 0.371 ms | 2026-10-14 |
| text | `<tk:text>Hello</tk:text>` | 0.13 ms | 2026-10-14 |
| textarea | `<tk:textarea name="bio" label="Bio" />` | 0.358 ms | 2026-10-14 |
| textarea | `maxlength="100" rows="2" max-rows="6" copyable` | 0.837 ms | 2026-10-14 |
| time | `<tk:time :value="now()->subHours(3)" />` | 0.113 ms | 2026-10-14 |
| time-picker | `<tk:time-picker name="t" label="Start" />` | 0.858 ms | 2026-10-14 |
| tinymce | `<tk:tinymce name="body" label="Body" />` | 0.376 ms | 2026-10-14 |
| tiptap | `<tk:tiptap name="body" label="Body" />` | 9.118 ms (toolbar="none": 9.099 ms; 22 tooltip buttons ~0.22 ms each, 2 color-pickers ~1 ms each, size dropdown) | 2026-10-14 |
| toast | `<tk:toast />` (once per page; 6 position templates) | 3.353 ms | 2026-10-14 |
| toggle | `<tk:toggle name="a" icon="star">Bold</tk:toggle>` | 0.290 ms | 2026-10-14 |
| toggle | `icon="star" tooltip="Bold"` (icon-only) | 0.286 ms | 2026-10-14 |
| transition | `<tk:transition animation="fade">x</tk:transition>` | 0.031 ms | 2026-10-14 |
| two-factor | `<tk:two-factor.qr svg secret uri />` (2 copyables) | 1.061 ms | 2026-10-14 |
| two-factor | `<tk:two-factor.recovery-codes>` with 2 codes | 0.829 ms | 2026-10-14 |
| upload | `<tk:upload name="f" label="File" />` (dropzone, tile template, preview modal) | 4.253 ms | 2026-10-14 |
| upload | `multiple variant="list"` | 3.688 ms | 2026-10-14 |
| similar | `<tk:similar :items="[]" />` (renders nothing) | 0.021 ms | 2026-10-15 |
| similar | 2 items, `value="Food"` (alert + 2 buttons) | 1.131 ms | 2026-10-15 |
| similar | 2 items, `select="useExisting"` (+ 2 buttons) | 1.674 ms | 2026-10-15 |
