# Reviewed components

Last completed review of each component: date, dimensions covered and the content hash
(`git hash-object`) of each mapped file. The `review-component` skill compares these hashes to
skip code that has not changed since.

## clearable
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/clearable/clearable.blade.php 32b70fce8bbd8fd88cbea7e9c0d390aebbab51fa
- resources/js/components/clearable.js 98caafba575b9472cb129e0c58f92cd5e1d261d4
- resources/js/utils/field.js f707891cd412e0dd19fe58cb945b5935bb79a06e
- resources/js/utils/event.js f295b31976a4328d200c481a9e99a05a0057911f
- resources/js/utils/bind.js 5d28baa81ca0aa4980d74eccd8361000ae469052
- resources/js/utils/livewire.js eb61b7d3c9c399f25fdfcfaf5de4981a76d89299
- resources/views/button/button.blade.php b7a7fc10e4cb10bbdb068f711de7c3744b4cd636
- resources/views/field/control.blade.php c48417ee0daa690614f5a5b0eeded179fb7ecc18
- resources/views/input/input.blade.php 64ac5cf9c378b528d94e303044d63bbf827ea5ab
- resources/views/listbox/search.blade.php dd5a2389dca030f82934416cd53fc55a9dae302b
- resources/views/color-picker/color-picker.blade.php 40f66f79b0cbf845a60ff6ca351dfc0d640438f3
- resources/views/color-picker/button.blade.php 421a691925923711eb6ee96213ef29d521971009
- resources/views/date-picker/date-picker.blade.php c6d93a13f4ad0b703738d4a56e65d4d3099b1aab

## accordion
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/accordion/accordion.blade.php 468c26357fc944e494c92936f67105ed687f91e6
- resources/views/accordion/item.blade.php 348991a9dbdc9a32236b5db6132a70bcc1913622
- resources/views/accordion/heading.blade.php b5ca091a308128d7cf59d55b17e01252ce3d2697
- resources/views/accordion/content.blade.php e51496735f88dd594e96d5b517182f540be0475f
- resources/js/components/disclosure.js e4df1f6cac8f5c88e1283d8ca674438287ee9867
- resources/js/components/disclosure-group.js 128aab700d641ca848a5534f9737b3097f080a2a
- resources/js/mixins/toggleable.js 60b9cc6965eeeda53dce4d7ef255e5da3e3564e8
- resources/js/utils/bind.js 5d28baa81ca0aa4980d74eccd8361000ae469052
- resources/js/utils/naming.js 4b31db3a261f902b73b61e27c890c22e6e7bfe39
- resources/js/utils/event.js f295b31976a4328d200c481a9e99a05a0057911f
- resources/js/utils/livewire.js eb61b7d3c9c399f25fdfcfaf5de4981a76d89299
- resources/css/alpine.css 42c57de093dbe8b9dc6396be5a6465b77f541a4d
- resources/css/animation.css 50d1b223b1e3a0e3a38b3518cdaf6a6e6f7a540d
- src/View/Compilers/ComponentTagCompiler.php ddc4bc87f4cb7b493699bd294f83779f4e6a151b
- resources/views/button/button.blade.php b7a7fc10e4cb10bbdb068f711de7c3744b4cd636
- resources/views/nav/group.blade.php 6abe645b8c61a80b1f3095eddef2e718fe959207

## alert
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/alert/alert.blade.php 5a377096c9a279bea90c04c6d3de364dc757e7d1
- resources/views/alert/session.blade.php 81b1d556fd84d324f7af92ad9fad8af90ffc7ce8
- resources/views/alert/close.blade.php 4232758c69fff0f4ebd52ed6f4d1433b0e216f83
- resources/js/components/alert-component.js 3d866cc007efd313fb39bcd392ad3ac6d91ab1af
- resources/js/mixins/dismissible.js 504e60329e9981c6afb242f6d7907f7719572163
- resources/js/utils/animation.js bc4f4e7f9e10de75c51dfca029c98154c0fdd726
- resources/js/utils/timer.js 080b767e0650e91fc5647ffd3562c79ac1dc9c75
- resources/js/utils/focus.js b4007d4be337e4bd2d4736c3841a1f38a4be9435
- resources/js/utils/livewire.js eb61b7d3c9c399f25fdfcfaf5de4981a76d89299
- resources/js/utils/event.js f295b31976a4328d200c481a9e99a05a0057911f
- resources/js/utils/bind.js 5d28baa81ca0aa4980d74eccd8361000ae469052
- resources/js/utils/naming.js 4b31db3a261f902b73b61e27c890c22e6e7bfe39
- resources/views/content/content.blade.php a7389cacbd04c6e8405b44ef1b57b125bc216088
- resources/views/progress/progress.blade.php 4ebca9acb06442ecbb72a20b1b45f57000a5aba3
- resources/views/danger-zone/danger-zone.blade.php f78829384fc0da96644dac1e868a1a704af8ab0f
- resources/views/error/group.blade.php 36a65d995c367b5c96e67899942f7cc78ed99d78
- resources/views/form/form.blade.php af10cf895cfcdce70c85fea470a766bdaef42226
- resources/views/form/section.blade.php afe7ee5e42c269f6ad2dcae888d2f1db35698095
- resources/views/form/card.blade.php 53e2711c810ff2daf147580e543457253a613d1a

## address
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/address/address.blade.php f06f253bd798604ca1434c4699ebfe92917765b8
- resources/views/address/fields.blade.php 4b83475835ac99385818e99fa3dc8f24f0780a87
- resources/views/address/form.blade.php 90aca7e621e3d8c2984c37edb0e8d521c870b100
- resources/js/components/address-form.js ef28eb271956d2cbe59da57357d5f880980e227f
- resources/js/utils/naming.js 4b31db3a261f902b73b61e27c890c22e6e7bfe39
- resources/js/utils/bind.js 5d28baa81ca0aa4980d74eccd8361000ae469052
- resources/js/utils/fetch.js 24586d106c3e406ed095ac6f9485894f8c4d895b
- resources/js/utils/timer.js 080b767e0650e91fc5647ffd3562c79ac1dc9c75
- resources/js/utils/field.js f707891cd412e0dd19fe58cb945b5935bb79a06e
- resources/js/utils/cache.js 378ab9c8335adef29e0268d847127178cb6d6e35
- resources/js/utils/storage.js 19d1a9bb893220122be72cb976d3db74d8696635
- resources/js/utils/event.js f295b31976a4328d200c481a9e99a05a0057911f
- resources/js/utils/announce.js c348370bbe836f2507df391f69a90814804c1c92
- resources/js/utils/livewire.js eb61b7d3c9c399f25fdfcfaf5de4981a76d89299
- resources/views/input/input.blade.php 64ac5cf9c378b528d94e303044d63bbf827ea5ab
- resources/views/select/select.blade.php 1692b84ce0d125c903d27f1c7f7cfc782801219e
- resources/views/error/error.blade.php 7b2cbf83126d264efb58c7acfcfb4eb6ca9fc3e6
- resources/views/field/wrapper.blade.php 19cf4e7600ecb680b9b2b1837e9c29a178630ef8
- resources/views/field/control.blade.php c48417ee0daa690614f5a5b0eeded179fb7ecc18
- resources/views/section/section.blade.php 3c8c860e7e0712ac3213d040ed92ee33bf1caee8
- resources/views/card/card.blade.php f117c2583a28bec7d40b755b55b574700e1f222e
- resources/views/text/text.blade.php f0202bc544e105f3999c150f6720e022da2062ba
- src/Concerns/InteractsWithField.php 0753e0ce98140e0633bb13ce09559d7b392ab12a
- src/Concerns/InteractsWithNaming.php 149e3fd9432413f45648316895c712d61936b72f
- src/View/ComponentAttributeBagMixin.php 6e967bdbb5dac82473e4fa9ed6bfd686999841cc
- config/tallkit.php 715c128aef3f77cafff3b9d0a80def1098f214ae

## apexcharts
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/apexcharts/apexcharts.blade.php ecf525bd1f4ee033f20a38c469cca9980dccbe64
- resources/js/components/apexcharts.js 9281428a18be536fb4cf613c52f06eeb432338bb
- resources/views/chart/chart.blade.php c6731482833fbae88742185d2e4f89d59a2daf3d
- resources/views/loadable/loadable.blade.php 7505db3f5ba344d00c52da8d5087828465331318
- resources/views/fetchable/fetchable.blade.php 9838e59c79ae0fadfe98eb565879dd014ee80c04
- resources/js/components/loadable.js 61ad713e6b7f03b1fd61edea1827fedcb083928c
- resources/js/mixins/data-options.js f279c061e61853fcbd3083673191fd1e52d90baa
- resources/js/mixins/server-options.js 8d8406940b38e7a3718e61b0c16da0fb117e3ee7
- resources/js/utils/assets.js b87cb67922e492b0bfe1da28a6dc04cc809e3885
- resources/js/utils/color-scheme.js 291d769723cd3518755e7c6d0b95eeeb3d95631f
- resources/js/utils/event.js f295b31976a4328d200c481a9e99a05a0057911f
- resources/js/utils/livewire.js eb61b7d3c9c399f25fdfcfaf5de4981a76d89299

## appearance
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/appearance/appearance.blade.php 3b3dfd41434dd5674809ca46a6b6ed5afe8f4b78
- resources/views/appearance/toggle.blade.php 8c9b93122cd5bab3efa45c04f5ecaafcbd5a0e2f
- resources/views/appearance/selector.blade.php 50a17fe984417f5eb6c067df61bf034b41b4692c
- resources/views/appearance/menu.blade.php b943660fc0378f6a96ffbf75fc09e1a29cf1f36f
- resources/views/appearance/menu-item.blade.php 6ddb999736eb8e835150b71826d442179dde10be
- resources/views/appearance/view.blade.php 903ae7b09620cbab6461b6d77f8b699653204061
- resources/js/appearance.js 750149a70755b3fdff83b9b9fa599e695f780367
- resources/js/components/appearance-selector.js 4ef0bee694f3f92d747ba624123b2e0e870adafd
- resources/css/appearance.css d81756d914faec4f758b6af590134dd71017d1a6
- resources/js/core.js 039a4a68ed60933f6448b4f3b4f1d52613f73e53
- resources/js/alpine.js 7ff27a8a4d14f323b6357815d596bef973453320
- resources/js/components/menu.js ce6c49ce3a19ff7ffce514ff391659c2f5468312
- resources/js/utils/storage.js 19d1a9bb893220122be72cb976d3db74d8696635
- resources/js/utils/animation.js bc4f4e7f9e10de75c51dfca029c98154c0fdd726
- resources/js/utils/bind.js 5d28baa81ca0aa4980d74eccd8361000ae469052
- resources/js/utils/naming.js 4b31db3a261f902b73b61e27c890c22e6e7bfe39
- resources/views/button/button.blade.php b7a7fc10e4cb10bbdb068f711de7c3744b4cd636
- resources/views/button/group.blade.php 3ef435e8014dd5096419349eee1da4e78f71ded8
- resources/views/menu/item.blade.php e2d4ed4e84eeca49e0f95923a57f631410fa3caf
- resources/views/menu/menu.blade.php 6bc50b35199d1c30d6edf9063526c8a28350c879
- resources/views/avatar/menu.blade.php a0f0c9fe2607f09d5d6b0ef56cd365bb83bfbf6b
- resources/views/html/html.blade.php fbc71420c6875078416f435ad3d01bdddde0b5f3
- resources/views/layout/auth.blade.php 404ea12000483c3463a1e8b9476229393a46d162
- resources/views/layout/header.blade.php cfe5fc4d0c5fd361f8d77a7eed7eac7e6de7d6bd
- resources/views/layout/sidebar.blade.php 2854cb77655edad355b6fe884a6f9ba781619bf9

## aside
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/aside/aside.blade.php 833db89486befd63b14208b5b0b1c6b6f23db0df
- resources/js/components/aside.js a9f50fafa0674a6cd382255af3f6a24006d95632
- resources/js/mixins/stickable.js 14e65e3fa684f1dbe014b904581267862caab279
- resources/css/layout.css d6562868be2efa11d4344a7da197a25356905025
- resources/views/layout/sidebar.blade.php 2854cb77655edad355b6fe884a6f9ba781619bf9

## autocomplete
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/autocomplete/autocomplete.blade.php da366c1db4e987a108387f29e1292745a7d073b2
- resources/js/components/autocomplete.js f512e584aafae40b8990b23f26a85ea0e52abfed
- resources/js/components/listbox.js 53bea0c3730c152912e7f9b7d96297b0bacc63c6
- resources/js/components/popover.js 90255c9c67ffffa6ee1b8e86b6d69008aad70b84
- resources/js/mixins/toggleable.js 60b9cc6965eeeda53dce4d7ef255e5da3e3564e8
- resources/js/utils/announce.js c348370bbe836f2507df391f69a90814804c1c92
- resources/js/utils/bind.js 5d28baa81ca0aa4980d74eccd8361000ae469052
- resources/js/utils/field.js f707891cd412e0dd19fe58cb945b5935bb79a06e
- resources/js/utils/escape-key.js e113b7d630ec7450fbcf36052d5ddf26604b0ed0
- resources/js/utils/string.js 8cd067cfca0cbc84cbe20b964fcf9d25f889a20d
- resources/views/listbox/items.blade.php e21c76ab3ee53b73748232133aeb291647ea8f87
- resources/views/listbox/item.blade.php 4ca333d169b84d30729c59ea953e74064429ef02
- resources/views/popover/popover.blade.php e8b028613d5c39c7448937ea17e4723857582c31
- resources/views/input/input.blade.php 64ac5cf9c378b528d94e303044d63bbf827ea5ab
- src/Concerns/InteractsWithOptions.php 94ff7260e9ea2e00f6c20e4c9c47dd0911910867

## avatar
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/avatar/avatar.blade.php 63d9b5c833f6664fde13985980393a5828bea89f
- resources/views/avatar/group.blade.php 039583b3b011a06a132ba7124859914708be7ba8
- resources/views/avatar/menu.blade.php a0f0c9fe2607f09d5d6b0ef56cd365bb83bfbf6b
- resources/views/avatar/profile.blade.php 6eaa61c073b585fccf5ea48421005b76e7b2f4a0
- src/Concerns/InteractsWithAvatar.php c1ed21d4df5ea722713e7b6d09413ac131e339d1
- src/Concerns/InteractsWithUser.php 0d5a2ac1d1c9e382febdc4f5cb9e7ca8a999559d
- resources/views/element/element.blade.php 0c5f2e5bf11b9ebccef1839e258d02d768e1c1fd
- resources/views/icon/icon.blade.php f26bc817db28e4dab2a2ec95551c26bb49dc535f
- resources/views/content/content.blade.php a7389cacbd04c6e8405b44ef1b57b125bc216088
- resources/css/focus.css 0ab8749ed26aefdd329e67cd86d16ff3aabff92b
- src/Concerns/InteractsWithAttributes.php 8d3d0ac9b24b61504e97f1aa57f14b4ee35e9967
- src/Concerns/InteractsWithElement.php 542f26daf13b8429dad3e327f256c06b63a94c65
- resources/views/appearance/menu.blade.php b943660fc0378f6a96ffbf75fc09e1a29cf1f36f
- resources/views/notification/item.blade.php a8b1ad34523f1a98ddd213341a95fe17f2053edd

## badge
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/badge/badge.blade.php 769daaa7546320c9edf8170b5fede331852b6d07
- resources/views/badge/close.blade.php f3911c09e28540c9db60ec5bebb7d9b170d7d278
- resources/js/components/badge.js 0d764fb052f07567a63424c51b95333dfee447de
- resources/js/mixins/dismissible.js 504e60329e9981c6afb242f6d7907f7719572163
- resources/js/utils/animation.js bc4f4e7f9e10de75c51dfca029c98154c0fdd726
- resources/js/utils/livewire.js eb61b7d3c9c399f25fdfcfaf5de4981a76d89299
- resources/views/element/element.blade.php 0c5f2e5bf11b9ebccef1839e258d02d768e1c1fd
- resources/views/button/button.blade.php b7a7fc10e4cb10bbdb068f711de7c3744b4cd636
- resources/css/focus.css 0ab8749ed26aefdd329e67cd86d16ff3aabff92b
- resources/css/forced-colors.css 043fb34cd18c5f7457d37d52ef736e27488e4184
- resources/css/colors.css 61811f12d6f189fa8f2b382a1ff615fecf04721c
- src/Concerns/InteractsWithColor.php 6e62530232ad3df964426e03eec0c10707f3e063
- src/Concerns/InteractsWithElement.php 542f26daf13b8429dad3e327f256c06b63a94c65
- resources/views/combobox/combobox.blade.php 7a552ecf4db28cae40c9835c9779677987a687c2

## brand
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/brand/brand.blade.php 51e8f894bb3784d3b399d63ffd224deb77912de2
- resources/views/element/element.blade.php 0c5f2e5bf11b9ebccef1839e258d02d768e1c1fd
- resources/views/heading/heading.blade.php 59d7ebce534166d3fed4de59e93ca1881a8020a9
- src/Concerns/InteractsWithUrl.php c6432181bcbd88d04c53028ba1986afbee907961
- src/Concerns/InteractsWithElement.php 542f26daf13b8429dad3e327f256c06b63a94c65
- src/helpers.php e638ce9cc2b7d673248f1ac71a5b106b64289895
- resources/css/focus.css 0ab8749ed26aefdd329e67cd86d16ff3aabff92b
- resources/css/colors.css 61811f12d6f189fa8f2b382a1ff615fecf04721c
- resources/views/layout/header.blade.php cfe5fc4d0c5fd361f8d77a7eed7eac7e6de7d6bd
- resources/views/layout/sidebar.blade.php 2854cb77655edad355b6fe884a6f9ba781619bf9
- resources/views/layout/auth.blade.php 404ea12000483c3463a1e8b9476229393a46d162
- resources/views/layout/error.blade.php 4a0fa6cfde989a03428c97aba84c768210d75252

## breadcrumb
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/breadcrumb/breadcrumb.blade.php 0b99ddf851b9feaeffbd9790498f4cf0c1b1cc8d
- resources/views/breadcrumb/item.blade.php 23aea04807eebbf4caf2db97cd42ee07ea24a184
- resources/views/breadcrumb/separator.blade.php e5009e68e50aae74e6ebda5f56c809aeb5a4ece3
- resources/views/element/element.blade.php 0c5f2e5bf11b9ebccef1839e258d02d768e1c1fd
- resources/views/icon/icon.blade.php f26bc817db28e4dab2a2ec95551c26bb49dc535f
- src/Concerns/InteractsWithAttributes.php 8d3d0ac9b24b61504e97f1aa57f14b4ee35e9967
- src/Concerns/InteractsWithNaming.php 149e3fd9432413f45648316895c712d61936b72f
- src/Concerns/InteractsWithSize.php fd068734112a40bee131f1ba81bfaa58c7c594fe
- src/Concerns/InteractsWithElement.php 542f26daf13b8429dad3e327f256c06b63a94c65
- src/Concerns/InteractsWithUrl.php c6432181bcbd88d04c53028ba1986afbee907961
- src/View/ComponentAttributeBagMixin.php 6e967bdbb5dac82473e4fa9ed6bfd686999841cc
- src/helpers.php e638ce9cc2b7d673248f1ac71a5b106b64289895

## button
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/button/button.blade.php b7a7fc10e4cb10bbdb068f711de7c3744b4cd636
- resources/views/button/group.blade.php 3ef435e8014dd5096419349eee1da4e78f71ded8
- resources/views/button/menu.blade.php ea73da44f2b66a9cd8d5bc97d62c2862d9b982eb
- resources/views/element/element.blade.php 0c5f2e5bf11b9ebccef1839e258d02d768e1c1fd
- resources/views/loading/loading.blade.php ad1410c50653d5aa222aab3db474e9922a98888c
- resources/views/submit/submit.blade.php fa9e546656253602f60d4f1f6b642f07ee2e5572
- resources/views/dropdown/dropdown.blade.php d0cfdd17d3809a572fb9168605d0f508529c0583
- resources/views/menu/menu.blade.php 6bc50b35199d1c30d6edf9063526c8a28350c879
- resources/js/utils/submit.js a3322cf2c2a63fc2b8964f0c2e9a7537e60d4a9b
- resources/css/focus.css 0ab8749ed26aefdd329e67cd86d16ff3aabff92b
- resources/css/forced-colors.css 043fb34cd18c5f7457d37d52ef736e27488e4184
- resources/css/colors.css 61811f12d6f189fa8f2b382a1ff615fecf04721c
- resources/css/fields.css 5132543fa78dce8f15b3119a872749d4f17d5bb4
- src/Concerns/InteractsWithSize.php fd068734112a40bee131f1ba81bfaa58c7c594fe

## button/menu
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/button/menu.blade.php ea73da44f2b66a9cd8d5bc97d62c2862d9b982eb
- resources/views/dropdown/dropdown.blade.php d0cfdd17d3809a572fb9168605d0f508529c0583
- resources/views/menu/menu.blade.php 6bc50b35199d1c30d6edf9063526c8a28350c879
- resources/views/button/button.blade.php b7a7fc10e4cb10bbdb068f711de7c3744b4cd636

## button/group
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/button/group.blade.php 3ef435e8014dd5096419349eee1da4e78f71ded8
- resources/views/button/button.blade.php b7a7fc10e4cb10bbdb068f711de7c3744b4cd636
- resources/css/fields.css 5132543fa78dce8f15b3119a872749d4f17d5bb4

## calendar
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/calendar/calendar.blade.php 9c454010782cfcdc39c6c99653a5e1fed15559a2
- resources/js/components/calendar.js 4fb370f07b3a386d92a8b06d986e544a9650c88d
- resources/js/mixins/bindable-field.js 9fcb738ae167f5bc8ff57893dbb9645a0570b491
- resources/css/forced-colors.css 043fb34cd18c5f7457d37d52ef736e27488e4184
- src/Concerns/InteractsWithField.php 0753e0ce98140e0633bb13ce09559d7b392ab12a
- resources/views/date-picker/date-picker.blade.php c6d93a13f4ad0b703738d4a56e65d4d3099b1aab
- resources/js/components/date-picker.js 43879a20545c1861001559f7058ad244aebc4909

## checkbox
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/checkbox/all.blade.php e628d1bbe7e87593f240e854ce7a5a0f1c2f8105
- resources/views/checkbox/checkbox.blade.php cb5c4491bfb7fafa98408de10f6f35400c39b311
- resources/views/checkbox/group.blade.php c91acdb6fbfb5daab7bd886dfceb011d64e694ea
- resources/js/components/checkbox-all.js 5878baf5a0d7cc0b1b18f24b1f30c87e4d478e61
- resources/js/mixins/check-all.js 0f9eb9a00fa63dc35a5c66a7e4f3fb3c8e6095cb
- resources/css/forced-colors.css 043fb34cd18c5f7457d37d52ef736e27488e4184

## color-picker
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/color-picker/color-picker.blade.php 40f66f79b0cbf845a60ff6ca351dfc0d640438f3
- resources/views/color-picker/button.blade.php 421a691925923711eb6ee96213ef29d521971009
- resources/js/components/color-picker.js f42233891d3e877d69d7a9bab7214c2d725b3356
- resources/js/mixins/bindable-field.js 9fcb738ae167f5bc8ff57893dbb9645a0570b491
- resources/views/tiptap/tiptap.blade.php e934d0053fd842a2283f6f68e8e9554cf2a162a5

## combobox
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/combobox/combobox.blade.php 7a552ecf4db28cae40c9835c9779677987a687c2
- resources/views/combobox/option.blade.php 026ec9e3c1af0ba2e08cd54aea2346d539c92d06
- resources/js/components/combobox.js a747a2d47742d65cafc9abb8851dfd48cfc3c4b6
- resources/js/components/popover.js 90255c9c67ffffa6ee1b8e86b6d69008aad70b84
- resources/js/components/listbox.js 53bea0c3730c152912e7f9b7d96297b0bacc63c6
- resources/js/mixins/bindable-field.js 9fcb738ae167f5bc8ff57893dbb9645a0570b491
- resources/css/fields.css 5132543fa78dce8f15b3119a872749d4f17d5bb4

## command
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/command/command.blade.php 2ae834753a1481620f7ee48fc93d35a3718fc8c2
- resources/views/command/modal.blade.php ee3cc7b28eb57d27cdd18b5e33595d169197a321
- resources/views/listbox/listbox.blade.php 18e222dc1811236095717624a4abb38ea1ca08ec
- resources/js/components/listbox.js 53bea0c3730c152912e7f9b7d96297b0bacc63c6
- resources/views/modal/modal.blade.php 2685c784852337482b97770d74b0722ffd3e577a

## card
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/card/card.blade.php f117c2583a28bec7d40b755b55b574700e1f222e
- resources/views/section/section.blade.php 3c8c860e7e0712ac3213d040ed92ee33bf1caee8

## carousel
- 2026-10-14 — bugs, a11y, perf (quick), code, api (full)
- resources/views/carousel/arrows.blade.php f8c5a3d6275e73333de42c01302f051f2223cbb9
- resources/views/carousel/carousel.blade.php 0a909230584085bbf56015c09dc6cc97383e2297
- resources/views/carousel/controls.blade.php 2041cf51031f592cb3c79cd422d7c100557bba6f
- resources/views/carousel/indicators.blade.php c6412f2dd1bd758d8a6a2dbc93e46a21bc9156b9
- resources/views/carousel/slide.blade.php a5d114ccc048fa83a85401b696ae7a24913ae2da
- resources/views/carousel/thumbnails.blade.php cb90526b12c66bb87e39c79263034b78759bfb21
- resources/js/components/carousel.js 262a3385cf65655b0e1072d0f38634b7f50898f6
- resources/css/animation.css 50d1b223b1e3a0e3a38b3518cdaf6a6e6f7a540d

## chart
- 2026-10-13 — bugs, a11y, perf, code, api (covered by the apexcharts review)
- resources/views/chart/chart.blade.php c6731482833fbae88742185d2e4f89d59a2daf3d
- resources/views/loadable/loadable.blade.php 7505db3f5ba344d00c52da8d5087828465331318
- resources/views/fetchable/fetchable.blade.php 9838e59c79ae0fadfe98eb565879dd014ee80c04
- resources/js/components/loadable.js 61ad713e6b7f03b1fd61edea1827fedcb083928c
- resources/js/mixins/data-options.js f279c061e61853fcbd3083673191fd1e52d90baa
- resources/js/mixins/server-options.js 8d8406940b38e7a3718e61b0c16da0fb117e3ee7

## chartjs
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/chartjs/chartjs.blade.php abfd40a249e9b5d075da7a08090003032861f549
- resources/js/components/chartjs.js 6ae223b0b17717392904b14c063629a773731940
- resources/views/chart/chart.blade.php c6731482833fbae88742185d2e4f89d59a2daf3d
- resources/views/loadable/loadable.blade.php 7505db3f5ba344d00c52da8d5087828465331318
- resources/views/fetchable/fetchable.blade.php 9838e59c79ae0fadfe98eb565879dd014ee80c04
- resources/js/components/loadable.js 61ad713e6b7f03b1fd61edea1827fedcb083928c
- resources/js/mixins/data-options.js f279c061e61853fcbd3083673191fd1e52d90baa
- resources/js/mixins/server-options.js 8d8406940b38e7a3718e61b0c16da0fb117e3ee7
- resources/js/utils/color-scheme.js 291d769723cd3518755e7c6d0b95eeeb3d95631f

## radio
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/radio/radio.blade.php b1d4246ca7a959416e4d406e1c9823697466e079
- resources/views/radio/group.blade.php 57c2e5ee59e3d80bc09fe6bcffbc32675b24e9a4
- resources/css/forced-colors.css 043fb34cd18c5f7457d37d52ef736e27488e4184

## composer
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/composer/composer.blade.php de437f064999d4e2b632d42e4d06f0940c680dba
- resources/js/components/composer.js ae2adfc147b0c2567df0466b102044cd6d903cd1
- resources/js/mixins/bindable-field.js 9fcb738ae167f5bc8ff57893dbb9645a0570b491
- resources/views/textarea/textarea.blade.php d4683b5b023a5e085daee7e4763480752eb630b3

## container
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/container/container.blade.php 35e224ad9d9d50d1013dca08989099ef7b23afdc
- resources/views/container/wrapper.blade.php 8a48729c35d47cbd39c460609f65a07907a71869
- src/Concerns/InteractsWithSize.php fd068734112a40bee131f1ba81bfaa58c7c594fe

## content
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/content/content.blade.php a7389cacbd04c6e8405b44ef1b57b125bc216088

## context
- 2026-10-14 — bugs, a11y, perf (quick), code, api (re-check of doc findings: item click, re-open, morph focus)
- resources/views/context/context.blade.php 48cc7f6fa1334b99c2d3841a13317347d2d55cda
- resources/views/dropdown/dropdown.blade.php d0cfdd17d3809a572fb9168605d0f508529c0583
- resources/js/components/popover.js 90255c9c67ffffa6ee1b8e86b6d69008aad70b84

## copyable
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/copyable/copyable.blade.php 64a13da157de783ce73352afad21c8fb28ec6808
- resources/js/components/copyable.js 72ad6a0a06910f410066e508b0585da0b76c0307
- resources/views/element/element.blade.php 0c5f2e5bf11b9ebccef1839e258d02d768e1c1fd

## credit-card
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/credit-card/credit-card.blade.php 10b24db07102e1d5bf3d788675ddd1c51f518e17
- resources/js/components/credit-card.js 3659d868c000faaf40f75b5095dbd96df21ea9f5

## danger-zone
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/danger-zone/danger-zone.blade.php f78829384fc0da96644dac1e868a1a704af8ab0f
- resources/views/alert/alert.blade.php 5a377096c9a279bea90c04c6d3de364dc757e7d1
- resources/views/modal/confirm.blade.php 9a9e77763d6fdda9f9be88d4d15ccbcf7f28277b

## date-picker
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/date-picker/date-picker.blade.php c6d93a13f4ad0b703738d4a56e65d4d3099b1aab
- resources/js/components/date-picker.js 43879a20545c1861001559f7058ad244aebc4909
- resources/js/components/calendar.js 4fb370f07b3a386d92a8b06d986e544a9650c88d
- resources/js/components/popover.js 90255c9c67ffffa6ee1b8e86b6d69008aad70b84
- resources/js/mixins/bindable-field.js 9fcb738ae167f5bc8ff57893dbb9645a0570b491

## display
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/display/display.blade.php 6443ac8d78985c554981f67f964aae239e2a7709

## dropdown
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/dropdown/dropdown.blade.php d0cfdd17d3809a572fb9168605d0f508529c0583
- resources/views/dropdown/button.blade.php f40e37abca867ec17dd11c0ddc2d21cb0bdfbb37
- resources/js/components/popover.js 6ccaa8db3a4a75361b755a3324568a9ecf337484
- resources/views/popover/popover.blade.php e8b028613d5c39c7448937ea17e4723857582c31

## echarts
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/echarts/echarts.blade.php 7f8a2f88591efb74d9558af0156ff48a6da3061e
- resources/js/components/echarts.js b8f0f05e433f5f204e20228e72dde07a62ecf14b

## editorjs
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/editorjs/editorjs.blade.php cf4c6834ecbf93aed318d7eb9d5ad108d50a597b
- resources/js/components/editorjs.js ab75ddd9ab5f84b1a5f98524ecb8f6ed4c7c3e11
- resources/js/mixins/editor.js 67926dda9c08261dafbe55785132a3b1c5e98d9c
- resources/js/components/loadable.js 61ad713e6b7f03b1fd61edea1827fedcb083928c

## element
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/element/element.blade.php 0c5f2e5bf11b9ebccef1839e258d02d768e1c1fd
- resources/views/element/wrapper.blade.php f3b65a0511f69c537db8243950edf144a4c151d1

## error
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/error/error.blade.php 7b2cbf83126d264efb58c7acfcfb4eb6ca9fc3e6
- resources/views/error/group.blade.php 36a65d995c367b5c96e67899942f7cc78ed99d78
- src/Concerns/InteractsWithErrorBags.php f2d2a1c3413e6da57a8b3c5750a1e7325d4e1e50

## fetchable
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/fetchable/fetchable.blade.php 9838e59c79ae0fadfe98eb565879dd014ee80c04
- resources/js/components/fetchable.js ec0298ad1f7d202eae2ea5551723e2d35468a211
- resources/js/components/loadable.js 61ad713e6b7f03b1fd61edea1827fedcb083928c
- resources/js/components/pretty-print-json.js bdb73e5d174de22f499358276ccd7c4ea59476ff
- resources/views/pretty-print-json/pretty-print-json.blade.php 34741bddee2e981efceb3b38f197cbf0467cc21f

## field
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/field/field.blade.php b45cd6534d7bc0ea1ef0834d197ce8f7a18374a4
- resources/views/field/wrapper.blade.php 19cf4e7600ecb680b9b2b1837e9c29a178630ef8
- resources/views/field/control.blade.php c48417ee0daa690614f5a5b0eeded179fb7ecc18
- resources/views/field/group/group.blade.php 9cd2e1a0db2f5edfb54ea309bd7f1b80968e13c1
- resources/views/field/group/side.blade.php 0f809e07e40b1e44ecb6f4d5db9eff40cd6ed42f
- resources/views/field/group/prefix.blade.php df8cd9b042a36749db08ecba55d774a6e1d2ad97
- resources/views/field/group/suffix.blade.php a6bf075898204423df7ccabf5d86167ed144cf76

## fieldset
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/fieldset/fieldset.blade.php 3ff66a6e79655ec150012469ce15841733ad3c48
- resources/views/legend/legend.blade.php 7168cbcd7213038c52e33f99c94c4a046f91dd00

## footer
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/footer/footer.blade.php 6ecbd7f677898204bb1ed168d94aa4f3a148ed9f
- resources/views/container/wrapper.blade.php 8a48729c35d47cbd39c460609f65a07907a71869
- src/Concerns/InteractsWithColor.php 6e62530232ad3df964426e03eec0c10707f3e063

## form
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/form/form.blade.php af10cf895cfcdce70c85fea470a766bdaef42226
- resources/views/form/card.blade.php 53e2711c810ff2daf147580e543457253a613d1a
- resources/views/form/section.blade.php afe7ee5e42c269f6ad2dcae888d2f1db35698095
- resources/js/components/form.js 799a6c882b4b9c86eef460e2342fac8c2b7376c2
- src/Concerns/InteractsWithUrl.php c6432181bcbd88d04c53028ba1986afbee907961

## frappe-charts
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/frappe-charts/frappe-charts.blade.php 463f4ac3181fbe27b15def02e346ea04132f8db3
- resources/js/components/frappe-charts.js b2b1dac18f79de75affb9eb34a7fc81ed3a2cbee

## full-calendar
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/full-calendar/full-calendar.blade.php 7c8813b49e231b2d76dedaec8ad495890ba5d638
- resources/js/components/full-calendar.js c4038a47f4485a21808ed49983f158a04eb88b38
- resources/js/mixins/server-options.js 8d8406940b38e7a3718e61b0c16da0fb117e3ee7

## google
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/google/consent.blade.php 84e72a952b84c444dbdcb2b594f2646fa1ba74f6
- resources/views/google/fonts.blade.php 7d271ae35ee5dd0b201cbbf5b767d64458e4fba8
- resources/views/google/gtag.blade.php b67d50f983866ce84ad33a5f0d25aa44e08beea9
- resources/views/google/gtm.blade.php 09b9422f7b10f85e03bc5615774172be01286638
- resources/js/consent.js b7ba769e171db3b3ce136ae8910c3bb6efd995a7

## header
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/header/header.blade.php 39809d57ba431f407b8f4df5ebf1fdb5c9593b2c
- resources/js/components/header.js 2ef8248ceba47b4e3a5e4b6c887adc791a7090ec
- resources/js/mixins/stickable.js 14e65e3fa684f1dbe014b904581267862caab279

## heading
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/heading/heading.blade.php 59d7ebce534166d3fed4de59e93ca1881a8020a9
- resources/views/element/wrapper.blade.php f3b65a0511f69c537db8243950edf144a4c151d1

## highlightjs
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/highlightjs/highlightjs.blade.php e3d44c020dc335ee21624d81910f7d82b4a09dec
- resources/js/components/highlightjs.js 3d80e475d4338d45206056f4ba50593f86c01099

## html
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/html/html.blade.php fbc71420c6875078416f435ad3d01bdddde0b5f3
- resources/views/html/meta.blade.php 9ac3a15c03b53be4ffc8493ef8c1a417514d80b7

## icon
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/icon/icon.blade.php f26bc817db28e4dab2a2ec95551c26bb49dc535f

## input
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/input/input.blade.php dcaa05b6d1f6b24fd367f16aacecdda4f2ef3a0a
- resources/views/input/viewable.blade.php a79d80d9bf98713121ead1c21e473b3663e60b2b
- resources/js/components/input-viewable.js 14766af1916ec60ad03046d3980b669fe7aeafab

## kbd
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/kbd/kbd.blade.php 490b6ea9aa9fea5b3d431eed1448d0eb68d93541

## label
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/label/label.blade.php 6914dbd51ce7604a08977ae91c4e16feeceb1cf2
- resources/js/components/label.js 5b04a6df4b082b864b0a3c7fc791daf15915cd53

## layout
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/layout/auth.blade.php 404ea12000483c3463a1e8b9476229393a46d162
- resources/views/layout/error.blade.php 4a0fa6cfde989a03428c97aba84c768210d75252
- resources/views/layout/header.blade.php cfe5fc4d0c5fd361f8d77a7eed7eac7e6de7d6bd
- resources/views/layout/sidebar.blade.php 2854cb77655edad355b6fe884a6f9ba781619bf9
- resources/css/layout.css d6562868be2efa11d4344a7da197a25356905025

## legend
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/legend/legend.blade.php 7168cbcd7213038c52e33f99c94c4a046f91dd00

## link
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/link/link.blade.php b2c6409b354df0796cfd2603ff9336869b184cfe
- resources/views/text/text.blade.php f0202bc544e105f3999c150f6720e022da2062ba
- resources/views/element/element.blade.php 0c5f2e5bf11b9ebccef1839e258d02d768e1c1fd

## list
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/list/list.blade.php f59492213526b2c05eccc3e725bf028f54b28343

## listbox
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/listbox/listbox.blade.php 18e222dc1811236095717624a4abb38ea1ca08ec
- resources/views/listbox/items.blade.php e21c76ab3ee53b73748232133aeb291647ea8f87
- resources/views/listbox/item.blade.php 4ca333d169b84d30729c59ea953e74064429ef02
- resources/views/listbox/search.blade.php dd5a2389dca030f82934416cd53fc55a9dae302b
- resources/views/listbox/no-records.blade.php 8e81a9d315ad6d6808e6863ff426b3b58602da85
- resources/js/components/listbox.js 53bea0c3730c152912e7f9b7d96297b0bacc63c6

## loadable
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/loadable/loadable.blade.php 7505db3f5ba344d00c52da8d5087828465331318
- resources/js/components/loadable.js 61ad713e6b7f03b1fd61edea1827fedcb083928c

## loading
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/loading/loading.blade.php ad1410c50653d5aa222aab3db474e9922a98888c

## main
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/main/main.blade.php 308e582174201b92335684cd4aa3fcbf2fa96aa7

## menu
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/menu/menu.blade.php 6bc50b35199d1c30d6edf9063526c8a28350c879
- resources/views/menu/item.blade.php e2d4ed4e84eeca49e0f95923a57f631410fa3caf
- resources/views/menu/submenu.blade.php e99180d0636b92acf4e9a2b6de507689d17e23fd
- resources/views/menu/group.blade.php be2b574e71a2839ab4153c37952fd7fdcb358af9
- resources/views/menu/heading.blade.php 971c7cd0d37166df4f1790e449ee4854caeca020
- resources/views/menu/separator.blade.php 1e05b80bb2d4487b58f708541ed593bd687778c9
- resources/views/menu/choice.blade.php 97490026ea9f304d1bf2789a2c400d7207f33fb0
- resources/views/menu/choice-group.blade.php 1af53b63c48944c423f50bb514049b45c701bc2e
- resources/views/menu/checkbox/checkbox.blade.php 8f084d1171b01cb56422c5cd789c9fe750d99858
- resources/views/menu/checkbox/group.blade.php 4c966fc436f4aeb68675a458fff8fe371f274923
- resources/views/menu/radio/radio.blade.php ab4851a4f6d0f832f5e7eeb6ac276417dce0e7ad
- resources/views/menu/radio/group.blade.php ec5dcee34039d098fed70ebe61e958bf06251276
- resources/js/components/menu.js ce6c49ce3a19ff7ffce514ff391659c2f5468312
- resources/js/components/submenu.js 474f71cf835de5410279773c796e37dc775054f0
- resources/js/components/menu-checkbox.js de1ae1d2aa276bbc69992db3a48438da02531b9c
- resources/js/components/menu-radio.js 157df5f8b6e2861fca97abd30cf346a4f3e825d4
- resources/js/mixins/menu-item.js cfd5a208f063cd2b2d19cccd87f8b7cdd9c98bb0

## modal
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/modal/modal.blade.php 2685c784852337482b97770d74b0722ffd3e577a
- resources/views/modal/trigger.blade.php a7f263e7188c911ffc9805660c358e5bcf6c34d9
- resources/views/modal/close.blade.php 8d3639329da6fad20a2ebacfbd4f3e66521212a9
- resources/views/modal/confirm.blade.php 9a9e77763d6fdda9f9be88d4d15ccbcf7f28277b
- resources/js/components/modal.js b5db423d88b067e3df31873ee3661bb06406b99f
- resources/js/components/modal-trigger.js 5328edeea2312f9a52eac19558a0494bdd588554
- src/Concerns/InteractsWithModal.php ce4a14e1177272a0264f9fe261e2994e06592feb

## money
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/money/money.blade.php 14341be096e2ab426a06ed7a36a2ca6d709022fa
- resources/js/components/money.js 7d2f6c007fe70d13a4faabc645ac4dc97bea1c12

## nav
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/nav/nav.blade.php 2949964d225b2e3d8fd156f2ced9e6c3f283cccd
- resources/views/nav/item.blade.php 717ec00d431cb5882653697c518a36decc67d9fc
- resources/views/nav/menu.blade.php 665769ccbb9e398572f49f4e711804f9d84a78cb
- resources/views/nav/group.blade.php 6abe645b8c61a80b1f3095eddef2e718fe959207
- resources/views/nav/indicator.blade.php 357fa6828d4205ea6d2fb21201b1a7ce36a1564e
- resources/js/components/nav-indicator.js 028491348f9f4ce109dd351913fc26bbff56e7c9

## notification
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/notification/notification.blade.php 28209a7e7a805fa31bb01c62dda9c810166d1607
- resources/views/notification/panel.blade.php 8b9bf8ea3daf63fbad72c73789794253460b6123
- resources/views/notification/list.blade.php eea3b1d9e2f8776bc63bfac3d2e2bf890515a5a7
- resources/views/notification/item.blade.php a8b1ad34523f1a98ddd213341a95fe17f2053edd
- resources/js/components/notification.js 8a6edd4e184ca5accebd9feb28c11bec88416efb
- src/Livewire/NotificationActions.php dd3f0534e0b11e26ad7eee96058871129dcbbfa4
- src/Livewire/Mixins/NotificationMixin.php af7e45743e5449dcd67f29c2315f69ee8244c392

## otp
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/otp/otp.blade.php d3f111a19d67cdf16f8025643428c9b3a5c9bb13
- resources/views/otp/input.blade.php ee9e27e15b2e9912bae505134c8d784bbe053a72
- resources/views/otp/group.blade.php ee820743a8179fd04fe00058f6157abe3ad2dcc2
- resources/views/otp/separator.blade.php 311054d6b295aa06ba17fe03166deedbf5126429
- resources/js/components/otp.js d6295a9208ccfd5945e46faee2b17dca4931a397

## page
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/page/page.blade.php 33ca2500625b22a8005cec5ba23732980828a892
- resources/views/page/error.blade.php 5a08156860cb63b6cd446e186d2498804a95babc
- resources/views/page/auth/accept-invitation.blade.php e5bb3797faf264164ebbea8771502203121b131a
- resources/views/page/auth/account.blade.php c00b2bff7ece6b3397ad0eb6a3bdcefbe784d847
- resources/views/page/auth/activity.blade.php 35cbb64573b6fa558386bb546957897a24207d83
- resources/views/page/auth/change-email.blade.php 0d7a366064f1d39b179298901f8acc6664ab931d
- resources/views/page/auth/change-password.blade.php 23ab1ceb5d803e001d45d51c350bae69fb0e75ea
- resources/views/page/auth/confirm-password.blade.php a77959c3d09c2fbf2852bd0b0877061dd3131cca
- resources/views/page/auth/connections.blade.php 8ad430ac799d4d633292f68fdb3f56d00a473b40
- resources/views/page/auth/data-export.blade.php 439af34dcb285ba5237ee898b175b8241f033f0d
- resources/views/page/auth/delete-account.blade.php acfaa278346793abd6eeb9826ee72b3c71560338
- resources/views/page/auth/devices.blade.php 64df0dae09662c973c5966a8f9d36b146295a7aa
- resources/views/page/auth/forgot-password.blade.php 75110d9500c6e51459805775ec15b81da31ed745
- resources/views/page/auth/identifier.blade.php e24f5f6a4bb8a353ba7ab64c1660fce46101a3c4
- resources/views/page/auth/login.blade.php 0a9ad22d6a32b6f568a4e6bdf3d80607574ae8fd
- resources/views/page/auth/email-login.blade.php 17c6e29f703775b07694a634744e37d04b29ef98
- resources/views/page/auth/oauth-complete-registration.blade.php d18eebcb199fe2d302072fc99a95ba29c6172630
- resources/views/page/auth/oauth.blade.php 18983a18d904cd9315dba2765a856b071e52ee33
- resources/views/page/auth/reset-password.blade.php f203f13fc11f2e3f7edb994f9c80ece30cc533dd
- resources/views/page/auth/sessions.blade.php c8451987e252375aa5a3f83f8f70b981e959dcb2
- resources/views/page/auth/sign-up.blade.php e2a900b574093a036c18e768fb6643c3e1e1065b
- resources/views/page/auth/two-factor-setup.blade.php 2fb6ebf0ac64cec419cf6903e5d289391b2d8782
- resources/views/page/auth/two-factor.blade.php 7ae7824f6db33d18b766ff64ddb68283e67579fb
- resources/views/page/auth/verify-email.blade.php 9f29be768299d3dfc2058f937d66ea37046c2114

## pagination
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/pagination/first-page.blade.php 95f75ef5a0941c7e44869547a256798601b8d12e
- resources/views/pagination/last-page.blade.php 3728680422b697270b39d1e369f7952832919c42
- resources/views/pagination/next-page.blade.php 88a5e6d16cb458a33abc53faa3cde6325967efd5
- resources/views/pagination/page.blade.php e5a67d136287db7d5fe4b7aab59e1913f8141c42
- resources/views/pagination/pagination.blade.php a8c5e0db072d8047eadeb44d0d8a49628dbe2e32
- resources/views/pagination/per-page.blade.php 3c3e4094b86a90a36085850668964cf112807b2c
- resources/views/pagination/prev-page.blade.php 12d3b5371e3a3c6dc525adcee4d728b50454bbe4

## password
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/password/password.blade.php dda6fb87d3aab045e8ddc7d79557417fc94518d7

## popover
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/popover/popover.blade.php e8b028613d5c39c7448937ea17e4723857582c31
- resources/js/components/popover.js 6ccaa8db3a4a75361b755a3324568a9ecf337484
- resources/js/mixins/toggleable.js 60b9cc6965eeeda53dce4d7ef255e5da3e3564e8

## pretty-print-json
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/pretty-print-json/pretty-print-json.blade.php 34741bddee2e981efceb3b38f197cbf0467cc21f
- resources/js/components/pretty-print-json.js bdb73e5d174de22f499358276ccd7c4ea59476ff

## progress
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/progress/progress.blade.php b8d837bb84048fda85a8c7783b3f7efec121a5c5
- resources/js/components/progress.js 0c94ad23ba705fb09cf8ee7a6fa56e77a9a6f55c

## quill
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/quill/quill.blade.php 99132f4dc5eece7c47587466d1ccb9e5182fdd54
- resources/js/components/quill.js 8559f2a8ca087b81cd2a94fa764c26d242b804cc
- resources/js/mixins/editor.js 67926dda9c08261dafbe55785132a3b1c5e98d9c

## section
- 2026-10-14 — bugs, a11y, perf (quick), code, api (with card)
- resources/views/section/section.blade.php 3c8c860e7e0712ac3213d040ed92ee33bf1caee8

## select
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/select/select.blade.php 1692b84ce0d125c903d27f1c7f7cfc782801219e
- resources/views/select/option.blade.php 1d7513e552b00352dc5adb15794326571f4db122

## separator
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/separator/separator.blade.php 9becddabc0342db234baa8548e750773491e9c8f

## sidebar
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/sidebar/sidebar.blade.php b799c6f120ad845905b73be5291af1eabe521a7b
- resources/views/sidebar/toggle.blade.php ccc07cc3d9530a1824c6e939011fcee6c83a6007
- resources/views/sidebar/backdrop.blade.php 9b015cebfc27730295fc318a866b7822e5bd5e6d
- resources/js/components/sidebar.js 74c19814969a649418e35baa8e30ef5510edc24a

## skeleton
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/skeleton/box.blade.php b9e038e5698b9277bfb99ad56282167537a5a5b0
- resources/views/skeleton/circle.blade.php 616b3d249ea7ff840d3b8e6637c8cf156106468c
- resources/views/skeleton/group.blade.php ee8b8566b98ceafd201bab3f4a50129abfbd97ef
- resources/views/skeleton/line.blade.php 747e045b8ddbcf1364b531d5150427d2596b03d8
- resources/views/skeleton/skeleton.blade.php 97919d65e5f32f8e283a8b5fca7963a454760c84

## slider
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/slider/slider.blade.php 81b036da92ec62434b735cc2e6f8b589c39a6213
- resources/views/slider/tick.blade.php 070fa5986d4eb1f9cc88cb147ab37d30e14e993d
- resources/js/components/slider.js f8141a31a81b82dcad22bbdfbbf36877e13af076

## spacer
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/spacer/spacer.blade.php adedd10c846e8dc12726162a3c4e7aa83c3145f3

## status
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/status/status.blade.php 8f868b1633044bec594cfa2542db76353301c9f4
- resources/views/status/group.blade.php df5d074f16d6c64f61156b017bd10d49070ecc41

## stepper
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/stepper/stepper.blade.php 108d40e74fda7655aa22615a71e0ef21366d6d0f
- resources/views/stepper/step.blade.php a33e427f260c513f4e10ac090eb3b40f6ad4c7f7
- resources/views/stepper/line.blade.php dcd9c4091d42f9eacfb4e36a7e8bbf78c68dcecd
- src/Concerns/InteractsWithStepper.php aa1ec834d6951161b980188e767e63d5226fdd95

## submit
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/submit/submit.blade.php fa9e546656253602f60d4f1f6b642f07ee2e5572

## switch
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/switch/switch.blade.php 8593cbaa26cca8eb873f5ca4548d17189f2a1dad
- resources/views/switch/all.blade.php d2e9eb1b66e45e5a4900c1b0a8c0deb1dadf755a
- resources/js/components/switch-all.js df952d1792a2d9a9e340c0612ddd40f9e242519c
- resources/js/mixins/check-all.js 0f9eb9a00fa63dc35a5c66a7e4f3fb3c8e6095cb

## tab
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/tab/group.blade.php e96cde0a23b9219b97a91370f2db11ec3713989a
- resources/views/tab/tabs.blade.php 3a36bd3a4447b18f6dd5995e782cd14c95daa69c
- resources/views/tab/tab.blade.php f4160903674a8b2b970d562d455d1b7a19d8e9fe
- resources/views/tab/panels.blade.php bd0c68f4c286ab8076c8d085e49c9ee3669206f9
- resources/views/tab/panel.blade.php 5935885f918bc74bee3a1ace5b78be2458574dbb
- resources/js/components/tab.js 80cb20c08fff618e00f6b5e2b3f3d84080cb3c63

## table
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/table/col.blade.php 0084bfb83f3c47bd5e55c2e45c937c3e853bdd40
- resources/views/table/colgroup.blade.php 23c51b26e1dfba8d6585786aee1f89579b18271d
- resources/views/table/columns.blade.php 7718c3460a142aedd08ee8f575c4266df1b47052
- resources/views/table/footer.blade.php 2a6fa26ea7bdc145ec4e50652f64cb2e28aeedaa
- resources/views/table/rows.blade.php ec2bf8253e0cd8595b538a075f7c7ac5a9ed02f4
- resources/views/table/table.blade.php df388574663e4de157efd5d8cdae960c64b91844
- resources/views/table/cell/cell.blade.php edead98f98373a64f541c8af891bdb59c110324b
- resources/views/table/cell/expanded.blade.php f970b3274e08ae41b3d7be221b3f3842b9dff280
- resources/views/table/cell/selection.blade.php 35ed44b81db60a51c9157e48915de13ea6845ee6
- resources/views/table/column/column.blade.php ca83347cff2a35810be4d4c2b488a4aa70e85e8c
- resources/views/table/column/expanded.blade.php 518f97e45483d3950d60222c030a5da705de958e
- resources/views/table/column/select-all.blade.php cfb1128ea73ecf712dec46deb080b406bca6e7fb
- resources/views/table/columns/row.blade.php cd3ac3f7683b2e5b2fe5abc86118b83f3dfcc474
- resources/views/table/row/expanded.blade.php eb0f982c71eb2dd5546057c835bcbcf249adb851
- resources/views/table/row/no-records.blade.php 7faaf4ab0459c7e04b58cc5edd09bbad3d5c7836
- resources/views/table/row/row.blade.php efb5a599d5771daf6fd479051ff9791c9f475f41
- resources/js/components/table.js d542f7e128c547f1cb1e01b529dccc962b52b2e9

## terms
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/terms/acceptance.blade.php 1c85be6122c92b256744250a4e47f4c92eb21da2

## text
- 2026-10-14 — bugs, a11y, perf (quick), code, api
- resources/views/text/text.blade.php f0202bc544e105f3999c150f6720e022da2062ba

## textarea
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/textarea/textarea.blade.php d4683b5b023a5e085daee7e4763480752eb630b3
- resources/js/components/textarea.js 85facc1cd6ccd5a179b12548055b7c652a86e87e
- resources/js/utils/event.js f295b31976a4328d200c481a9e99a05a0057911f

## time
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/time/time.blade.php 4f843a10cf4cef191e1989d162cb00c4690721bb

## time-picker
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/time-picker/time-picker.blade.php 9172de56285ae609d80ae7135cd6879db4694bfd
- resources/js/components/time-picker.js 6ad50ab1a1f108d3b87f08c693e842d3c9f09f43
- resources/js/components/popover.js 6ccaa8db3a4a75361b755a3324568a9ecf337484
- resources/js/mixins/bindable-field.js 9fcb738ae167f5bc8ff57893dbb9645a0570b491

## tinymce
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/tinymce/tinymce.blade.php 99c40e507a01639d22e1b6e6584c6d03e097a784
- resources/js/components/tinymce.js cb27de5b601f183435e7cb4b285a52793327c44c
- resources/js/mixins/editor.js 67926dda9c08261dafbe55785132a3b1c5e98d9c
- resources/js/utils/assets.js b87cb67922e492b0bfe1da28a6dc04cc809e3885

## tiptap
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/tiptap/tiptap.blade.php e934d0053fd842a2283f6f68e8e9554cf2a162a5
- resources/js/components/tiptap.js 7de762d7427ad3d1a5ff6a1e7d041b6193d57f2a
- resources/js/mixins/editor.js 67926dda9c08261dafbe55785132a3b1c5e98d9c
- resources/js/utils/assets.js b87cb67922e492b0bfe1da28a6dc04cc809e3885

## toast
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/toast/toast.blade.php 87e9fe3efe65fec37655ae7adf1413370d08cf22
- resources/views/toast/flashed.blade.php fa36865a8582ad29f082236f295ca8eda9d3202d
- resources/js/components/toast.js 47a8feefcafb949672c40ea8a001c6a10523137e
- resources/js/toast.js 306d52f010085ab64ce9ef93a459805439f26606

## toggle
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/toggle/toggle.blade.php 6f74fd03eaa321cb07ac151024b3c59a74cd6825
- resources/js/components/toggle.js 300f2b4aca6dbb42514b94d5a14fb538f41f3c83

## transition
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/transition/transition.blade.php 7911f7d915d9e12746b853e6b09ccb12268cf386
- resources/css/animation.css 50d1b223b1e3a0e3a38b3518cdaf6a6e6f7a540d

## two-factor
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/two-factor/qr.blade.php 7f1a698880df7346b05863c13c9231eb28ad3e8c
- resources/views/two-factor/recovery-codes.blade.php 7859301c8b7b4904097a48b7108e1c4ed43c69f9

## upload
- 2026-10-14 — bugs, a11y, perf, code, api
- resources/views/upload/chip.blade.php 76bd7aeb5a9bc67391294c257e07fbc875c20634
- resources/views/upload/dropzone.blade.php d24ff083bf675298a30d6befb84700fb975310f9
- resources/views/upload/hint.blade.php 2ed1a2c775f2131968efb7e4c94b92b76f06e780
- resources/views/upload/list-item.blade.php 0828acfed225a1c3273a56c4b7fabc58a9911179
- resources/views/upload/modal.blade.php 6c1e62fe822224ab7103a5af96cf06291b81eed4
- resources/views/upload/preview.blade.php f3d0f67977c92c7b8c87509e0380ba0938922e62
- resources/views/upload/tile.blade.php 397b761590444e45b113145dba6b4a5a52edea81
- resources/views/upload/type-icon.blade.php d5f8286a7afdaa0c5e4da891d3dbb45fa13d285e
- resources/views/upload/upload.blade.php 2a129937d1002080492b7e2da4d095e73d892e8a
- resources/js/components/upload.js 63d22e7048f302b993af9a5b9dfd606534bbf527
