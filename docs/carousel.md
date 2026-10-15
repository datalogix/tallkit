# Carousel

A row of slides shown one at a time (or a few side by side), moved with arrows, dots, thumbnails, swipes or the keyboard, and optionally rotating on its own. Use it for galleries, featured content and product strips where only part of the set needs to be visible at once.

```
tk:carousel
└── tk:carousel.slide

tk:carousel.controls (outside the carousel, linked by name)
```

`tk:carousel.arrows`, `tk:carousel.indicators` and `tk:carousel.thumbnails` are rendered by the carousel itself; their attributes are reached through prefixes (see [Customization](#customization)).

## Usage

```blade
<tk:carousel :slides="[
    'https://picsum.photos/id/10/600/300',
    'https://picsum.photos/id/11/600/300',
    'https://picsum.photos/id/12/600/300',
]" />
```

The first image fills the carousel, with previous and next arrows over it and a row of dots below. The arrows, the dots and the arrow keys move between the images, and going past the last one returns to the first.

### Slides with content

```blade
<tk:carousel label="Highlights">
    <tk:carousel.slide src="https://picsum.photos/id/10/600/300" alt="Mountains at dawn" position="bottom">
        <h3 class="text-xl font-semibold">Mountains</h3>
        <a href="/mountains">See more</a>
    </tk:carousel.slide>

    <tk:carousel.slide src="https://picsum.photos/id/11/600/300" alt="A quiet lake" position="below" content="A quiet lake" />

    <tk:carousel.slide content="No image, just text" />
</tk:carousel>
```

The first slide shows the title and the link in white over a dark gradient at the bottom of the image; the second shows its caption under the image; the third is centered text. With `label`, the carousel is a landmark region named "Highlights".

### Several per view, from data

```blade
<tk:carousel per-view="3" gap="4" advance="page" :wrap="false">
    @foreach ($products as $product)
        <tk:carousel.slide
            :src="$product->image_url"
            :alt="$product->name"
            position="below"
            wire:key="product-{{ $product->id }}"
        >
            {{ $product->name }}
        </tk:carousel.slide>
    @endforeach
</tk:carousel>
```

Three products side by side with a gap between them. Each arrow click moves a whole page of three, and the dots count pages instead of slides. Without `wrap` the previous arrow is marked disabled on the first page and the next arrow on the last. A Livewire update that adds products updates the slide and page count without moving the carousel.

### Binding the current slide

```blade
<tk:carousel wire:model.live="index" :slides="$images" thumbnails />
```

```php
public int $index = 0;

public array $images = [
    'https://picsum.photos/id/20/600/300',
    'https://picsum.photos/id/21/600/300',
];
```

The bound value is the zero-based index of the first visible slide: clicking the second thumbnail sets `$index` to `1`, and setting `$index` to `0` on the server moves the carousel back to the first slide.

### Autoplay

```blade
<tk:carousel autoplay interval="4000" progress="overlay" fade label="News" :slides="$banners" />
```

The slides cross-fade every 4 seconds, with a thin bar along the bottom of the image filling up until the next one. A pause button sits under the dots. Rotation stops while the mouse is over the carousel, while the page or the carousel is out of view, and for good once the user moves keyboard focus into it, until the play button is pressed.

### Controls outside the carousel

```blade
<tk:carousel name="gallery" :arrows="false" :indicators="false" :slides="$images" />

<tk:carousel.controls name="gallery" position="outside" />
```

The carousel renders only its slides; the arrows and dots render wherever `tk:carousel.controls` is placed, and drive the carousel with the same `name`.

## Props

### `tk:carousel`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `slides` | array | `null` | Slides to render from data, after the ones in the default slot. Each item is an image URL or an array with `src`, `alt`, `content`, `position`, `thumbnail` and `name` (see `tk:carousel.slide`); any other key becomes an attribute of the slide. |
| `label` | string | `null` | The accessible name, translated. With a label the carousel is a `region` landmark; without, a `group`. |
| `perView` | int | `1` | How many slides are visible side by side. Ignored with `fade`. |
| `gap` | int \| string | `null` | The space between slides: a number is a Tailwind spacing step (`4` is `1rem`), a string any CSS length. Ignored with `fade`. |
| `advance` | string | `slide` | `slide` moves one slide per step; `page` moves a whole view, and the dots count pages. |
| `wrap` | bool | `true` | Going past the last slide returns to the first, and back from the first to the last. `false` stops at the ends. |
| `fade` | bool | `false` | Cross-fades the slides in place instead of sliding them. |
| `arrows` | bool | `true` | `false` removes the previous and next buttons. |
| `arrowsPosition` | string | over the slides | `overlap` puts the arrows half outside the edges; `outside` puts them in a row under the slides. |
| `indicators` | bool \| string | `true` | `false` removes the dots; `counter` replaces them with a `2 / 5` counter. Off by default when `thumbnails` is on. |
| `thumbnails` | bool | `false` | A scrollable strip of thumbnails under the slides, one per slide: the slide's `thumbnail`, else its first image, else its number. |
| `autoplay` | bool | `false` | Moves to the next slide on its own and adds a pause button. |
| `interval` | int | `5000` | With `autoplay`, the milliseconds each slide stays. |
| `progress` | bool \| string | `false` | With `autoplay`, a bar showing the time until the next slide, under the slides; `overlay` lays it over the bottom edge of the slides. |
| `color` | string | the accent color | The color of the active dot, the active thumbnail ring and the progress bar. |
| `name` | string | a generated id | Links the carousel to a `tk:carousel.controls` with the same name. |
| `pauseLabel` | string | `Pause slides` | The accessible name of the pause button while playing, translated. |
| `playLabel` | string | `Play slides` | The accessible name of the pause button while paused, translated. |

`wire:model` and `x-model` bind the zero-based index of the first visible slide, as a number.

### `tk:carousel.slide`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `src` | string | `null` | An image that fills the slide. |
| `alt` | string | `null` | The image's alternative text, translated. Without it the image is decorative (`alt=""`). |
| `content` | string \| slot | `null` | Text or markup for the slide, rendered before the default slot. A string is translated. |
| `position` | string | `center` | Where the content goes when the slide has an image: `center`, `top` or `bottom` over the image, on a dark backdrop; `above` or `below` the image, outside it. |
| `thumbnail` | string | `null` | The image shown for this slide in the `thumbnails` strip, in place of the slide's own image. |
| `name` | string | a generated id | The slide's `wire:key` inside Livewire. An explicit `wire:key` takes precedence. |

`fade`, `arrows` and `arrowsPosition` are inherited from the carousel.

### `tk:carousel.controls`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `null` | The `name` of the carousel to control. |
| `arrows` | bool | `true` | `false` removes the previous and next buttons. |
| `indicators` | bool \| string | `true` | `false` removes the dots; `counter` replaces them with a counter. |
| `position` | string | over the nearest positioned ancestor | The position of the arrows, as `arrowsPosition` on the carousel. Use `outside` to keep them in the flow. |
| `color` | string | the accent color | The color of the active dot. |

## Slots

| Tag | Slot | Description |
|-----|------|-------------|
| `tk:carousel` | default | `tk:carousel.slide` tags, rendered before the `slides` items. A carousel with no slides here and an empty `slides` renders nothing. |
| `tk:carousel.slide` | default | The slide content. With an image it is placed according to `position`. |

## Events

Events are dispatched on the component's own element and do not bubble: listen on the tag itself.

| Tag | Event | Detail | When |
|-----|-------|--------|------|
| `tk:carousel` | `changed` | `{ index }`, the new zero-based index | The current slide changed by an arrow, a dot, a thumbnail, a key, a swipe or autoplay. Not fired when the index changes through `wire:model` or `x-model`. |

With Alpine:

```blade
<div x-data="{ index: 0 }">
    <tk:carousel @changed="index = $event.detail.index" :slides="$images" />

    <p x-text="index + 1"></p>
</div>
```

Calling a Livewire action:

```blade
<tk:carousel @changed="$wire.trackView($event.detail.index)" :slides="$images" />
```

## Customization

Classes and attributes on `tk:carousel` go to the viewport, the element that clips the slides, and that is also where `changed` is dispatched. `wire:model` and `x-model` go to the root. On the other tags they go to the root element. To reach an inner element, prefix the attribute:

| Prefix | Tag | Goes to |
|--------|-----|---------|
| `container:` | `tk:carousel` | The root element, holding the slides and the controls. |
| `area:` | `tk:carousel` | The element around the viewport, the overlaid arrows and the overlay progress bar. |
| `track:` | `tk:carousel` | The element that moves, holding the slides. |
| `slide:` | `tk:carousel` | Each slide rendered from `slides`, including its own prefixes (`slide:image:class`). Not the slides in the slot. |
| `arrows:` | `tk:carousel`, `tk:carousel.controls` | The element holding the two arrows. |
| `prev:` / `next:` | `tk:carousel.arrows` | The previous / next button. From the carousel: `arrows:prev:` / `arrows:next:`. |
| `indicators:` | `tk:carousel`, `tk:carousel.controls` | The element holding the dots or the counter. |
| `indicator:` | `tk:carousel.indicators` | Each dot. From the carousel: `indicators:indicator:`. |
| `counter:` | `tk:carousel.indicators` | The counter. From the carousel: `indicators:counter:`. |
| `thumbnails:` | `tk:carousel` | The thumbnail strip. |
| `thumbnail:` | `tk:carousel.thumbnails` | Each thumbnail button. From the carousel: `thumbnails:thumbnail:`. |
| `progress:` | `tk:carousel` | The progress bar, with the prefixes of `tk:progress`. |
| `pause-area:` | `tk:carousel` | The element around the pause button. |
| `pause:` | `tk:carousel` | The pause button. |
| `image:` | `tk:carousel.slide` | The image. |
| `content:` | `tk:carousel.slide` | The content block of a slide that has an image. |

```blade
<tk:carousel
    class="rounded-xl"
    container:class="max-w-2xl mx-auto"
    slide:image:class="aspect-video"
    arrows:prev:tooltip="Back"
    indicators:indicator:class="size-8"
    :slides="$images"
/>
```

## Accessibility

Follows the WAI-ARIA carousel pattern. The root has `role="region"` when it has a `label`, otherwise `role="group"`, and `aria-roledescription="carousel"`. Each slide is a `group` with `aria-roledescription="slide"` and a name such as "Slide 2 of 5"; slides out of view are `aria-hidden` and `inert`, so their links cannot be tabbed to. The dots and thumbnails are buttons named "Go to slide 3", in a group named "Slides", with `aria-current` on the active one. The arrows are named by their tooltips ("Previous slide", "Next slide") and get `aria-disabled` at the ends when `wrap` is off. The slides are announced politely when they change, except while autoplay is rotating. The progress bar is hidden from screen readers.

| Key | Action |
|-----|--------|
| Arrow Left / Arrow Right | With focus anywhere inside the carousel, moves to the previous / next slide (or page with `advance="page"`). Reversed in right-to-left pages. Ignored while typing in a field. |

On touch and pen screens a horizontal swipe of at least 50px moves to the next or previous slide; a mostly vertical move scrolls the page. Mouse drags do not swipe.

## Behavior notes

- Autoplay stops for good when focus enters the carousel from the keyboard, until the play button is pressed; hovering, hiding the browser tab or scrolling the carousel out of view only suspends it. With reduced motion requested by the system, autoplay does not run and the pause button and the progress bar are not shown.
- Moving to another slide from the carousel (arrows, dots, keys, swipes) restarts the autoplay countdown.
- With `advance="page"` the last page is aligned to the end, so it may repeat slides of the previous page: five slides two per view show 1-2, 3-4 and 4-5.
- With `perView` and `advance="slide"` the dots count the positions the carousel can stop at, not the slides: four slides two per view have three dots.
- The arrows and dots are hidden when everything fits in one view.
- A value set through `wire:model` or `x-model` is not checked against the number of slides: an index past the last one leaves no slide visible.
- Slides added or removed by a Livewire update are picked up; when the current index no longer exists, the carousel moves to the last valid one.
- The first image of the `slides` items loads with high priority and the others lazily; the images of the slides next to the current one are loaded ahead. Images of slides in the slot load as usual.
- `tk:carousel.controls` placed before its carousel works too: it finds the carousel by name once both are on the page.

## Related

- `tk:tab.group`: panels switched by named tabs, when each panel has a title the user picks from.
- [Accordion](accordion.md): sections stacked vertically that open and close, when all of them should be reachable without moving through them.
