---
name: browser-test
description: Test a TALLKit component in a real browser (headless Chrome) against the demo app in ../app - clicks, keyboard, focus, DOM state, console errors and Alpine init time. Use when asked to test or check a component in the browser, to confirm a change works, or to measure init cost; the review-component and document-component skills use it for their browser checks.
argument-hint: <component> [what to check]
---

# Browser test

Arguments: `$ARGUMENTS`

Test the component yourself and report what you saw. Do not hand the steps to the user to run:
ask them only when the case is out of reach of a headless browser (a real screen reader, a
physical device), and then give the exact steps and the expected result.

## The demo app

`../app` is a Laravel + Livewire app that exists only to test this package. It loads the
package through a composer path repository, so it runs the working tree.

- Base URL: the `APP_URL` of `../app` (`http://app.test`). A component's page is
  `/<component>`, with the route in `../app/routes/web.php` and the view in
  `../app/resources/views/pages/`. Many routes point to a view that does not exist yet, so
  check the view file and create it when missing.
- Edit `../app` freely without asking: add or change examples on the component's page, or
  create the page when the component has none. Give the elements you will act on an `id`. Keep
  the changes to what the test needs and say what you changed.
- The package serves its bundle from `dist/`: after any JS change, run `pnpm build` before
  testing. The Tailwind classes written in the views are compiled by the app instead: after
  changing classes in a view, also run `pnpm build` inside `../app`, or the page is styled by
  stale CSS.
- Every page there is a Livewire page, and some props behave differently inside Livewire
  (`tk:input` ignores `value`, for example). Use `wire:model` or `x-model` for initial values.
  `Livewire.first()` returns the page component's `$wire` (`Livewire.first().$set('name', 1)`,
  `Livewire.first().name`).

## Running a test

`node .claude/skills/browser-test/browser.mjs <url> <steps.mjs>`

It starts headless Chrome, opens the URL, runs the steps and prints `{ result, errors }` as
JSON, where `errors` holds every console error and uncaught exception. It needs no extension
or setup. Write the steps file in the scratchpad:

```js
export default async (page) => {
  await page.click('#t-first button')
  await page.press('ArrowDown')

  page.out.focused = await page.eval('document.activeElement.id')

  return page.out
}
```

| `page` | What it does |
|--------|--------------|
| `click(selector)` | Real mouse events at the element's center, so `mousedown` and focus behave as for a user. Throws when the element is missing or not visible. |
| `press(key, { shift })` | A real key press: `Tab`, `Enter`, `Escape`, `' '` (Space), `Backspace`, `Delete`, the four arrows, `Home`, `End`. |
| `type(text)` | Inserts text into the focused element, firing `input`. |
| `eval(expression)` | Evaluates JavaScript in the page and returns the value; promises are awaited. |
| `waitFor(expression)` | Polls until the expression is truthy, e.g. after a Livewire request. Throws after 5 s. |
| `measureInit(selector, runs)` | Best time in ms of re-initializing Alpine on that element's subtree (see below). |
| `goto(url)`, `wait(ms)`, `screenshot(file)` | Navigation, a fixed pause, a PNG of the page. |
| `out` | An object printed even when a later step throws. Store results in it as you go. |
| `errors` | The console errors collected so far. |

- Exercise the scenario the way a user would (click, type, Tab and arrow keys), then read the
  DOM state and `document.activeElement` with `eval`. Calling `el.click()` or `el.focus()`
  from `eval` skips what a real interaction triggers; use it only to set a scene.
- A step that throws ends the run, so keep partial results in `page.out`.
- `click` takes a CSS selector. For an element with no id, mark it first:
  `page.eval("...find it....setAttribute('data-go', '1')")`, then `click('[data-go]')`.
- Check `errors` at the end: a clean console is part of the result.

## Measuring Alpine init

To know what a component costs in the browser, render a few hundred instances inside one
wrapper on the demo page and measure the wrapper:

```js
export default async (page) => ({ init: await page.measureInit('#t-perf') })
```

`measureInit` destroys the Alpine tree under the element, puts the same HTML back, runs
`Alpine.initTree` and returns the best of several runs. Measure the same wrapper with the
component left out (a query string that switches it off works well) and subtract: the
difference divided by the number of instances is the cost per instance. Numbers are comparable
only between runs made this way.

## Reporting

Say which scenarios ran and what each showed, what you changed in `../app`, and anything you
could not exercise. When a scenario fails, give the steps, what was expected and what
happened.
