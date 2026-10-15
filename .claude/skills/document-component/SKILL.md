---
name: document-component
description: Write or update the documentation of a single TALLKit component in docs/<component>.md (description, usage, props, slots, events, accessibility, behavior notes) and list it in the README. Use when asked to document a component or to bring its docs up to date (e.g. "document the modal", "documenta o accordion").
argument-hint: <component>
---

# Document component

Arguments: `$ARGUMENTS`

The first word is the component name (e.g. `accordion`). If none was given, ask which one and
stop. Document one component per run; if asked for several, do the first and list the rest as
pending.

The output is `docs/<component>.md`, named after the view folder in lowercase. Sub-components
(`accordion.item`, `accordion.content`) are documented in the parent's file, never in files of
their own. The README only lists the components and links to these files.

Documentation is written in English. `example.md` (next to this file) is the model: follow
its structure, depth and tone. It documents a `tk:tree` component that does not exist in the
package, so take the form from it and never the content: every fact in a real doc comes from
that component's code.

The code is the source of truth. Every table is extracted from the component's files, not
written from memory or from the previous version of the doc, and the doc describes what the
code does today, not what it should do. This skill does not change the component: if something
looks wrong while documenting, document the actual behavior and do not report it.

## 1. Read the component

Find and read its files:

- Blade views in `resources/views/<component>/`
- Scripts in `resources/js/components/` and the mixins they spread (the script name may differ
  from the view name, e.g. accordion uses `disclosure.js` and `disclosure-group.js`)
- PHP it depends on in `src/`

Collect, per tag (`tk:<component>` and each sub-component):

- **Props**: every key of `@props`, with its default. The type and the accepted values come
  from how the view and the helpers use the prop (e.g. the `match` inside a `TALLKit::` helper).
  `@aware` keys are props inherited from the parent.
- **Shared prop groups**: `...TALLKit::elementProps()`, `fieldProps()` and
  `fieldControlProps()` are not repeated per component. Name the group in one line under the
  table and link to `docs/shared-props.md`. If that file does not cover the group yet, add the
  group to it (create the file if missing), read from the method in `src/Concerns/`.
- **Prefixed attributes**: each `$attributes->prefixed('x:')` forwards `x:*` attributes to an
  inner element; note which, for the Customization section.
- **Model binding**: `x-modelable`, `wire:model` and `x-model` support and the shape of the
  value.
- **Slots**: the default slot and each named slot, and which props also accept a slot
  (`has_content()`).
- **Events**: every `emit()` reachable from the component, including the ones in its mixins:
  name, detail and when it fires. Events are dispatched on the component's element and do not
  bubble unless `emit()` is called with `bubbles: true`; say which is the case.
- **Accessibility**: roles and ARIA attributes the component sets, the keyboard interaction it
  implements, focus handling, and the WAI-ARIA APG pattern it follows when there is one.

## 2. Write `docs/<component>.md`

Sections, in this order, as in `example.md`. A section with nothing to say is left out
entirely: no "None", no empty table.

1. `# <Name>` and one or two sentences saying what the component is and when to use it. When
   it has sub-components, a small tree of the tags showing what goes inside what
2. `## Usage`: the smallest working example first, then one titled example (`###`) per
   distinct way of using it: model binding, rendering from data, composing sub-components, a
   notable variant. Five at most; an example that only changes a prop value belongs in the
   props table, not here. For each example:
   - follow it with a sentence or two saying what renders and what happens when the user
     interacts, since the reader has no picture
   - when it uses `wire:model`, show the Livewire property in a `php` block and the value it
     receives (`'invoices'`, `['docs', 'invoices']`)
   - when the component repeats items, include one example driven by a `@foreach`, which is
     how it is used in a real app
3. `## Props`: one table per tag (`Prop | Type | Default | Description`), then the one-line
   notes for inherited props, shared groups and model binding
4. `## Slots`
5. `## Events`: the table, then one example listening with Alpine and one calling a Livewire
   action (`$wire`)
6. `## Customization`: where classes and attributes land, a table of the prefixes
   (`Prefix | Tag | Goes to`) and one example
7. `## Accessibility`: the pattern followed and the roles and ARIA attributes in a sentence or
   two, then the keyboard as a table (`Key | Action`)
8. `## Behavior notes`: what a developer would not guess from the tables, such as what wins
   when two props conflict, how it behaves across Livewire updates, what nested instances
   inherit. Only things that hold in the code today, each one checked in step 3
9. `## Related`: one line per similar component, saying when to use that one instead. Link to
   its doc when `docs/<other>.md` exists; otherwise name the tag without a link

Descriptions say what the prop does for the person using it, in a sentence. Defaults are
written as the developer would pass them (`false`, `2`, `md`), and a `null` default that means
"off" or "inherited" is written as that behavior when it is clearer.

### When the file already exists

Compare it against what step 1 collected and change only what differs: a prop added, removed
or with another default, a new event, a behavior that changed. Keep the examples and the
behavior notes that are still true as they are; do not rewrite text that is correct.

## 3. Verify

- **Completeness**: every key of every `@props` appears in a table or in a shared group, and
  nothing in the tables is absent from the code.
- **Examples**: from `../app`, run
  `XDEBUG_MODE=off php ../tallkit/.claude/skills/document-component/render-examples.php ../tallkit/docs/<component>.md`
  It renders each `blade` block and fails on the ones that throw. An example that needs data
  from outside (the `@foreach` one) is reported as skipped: put it on the component's demo page
  in `../app` with real data and check it there.
- **Behavior**: every statement about keyboard, focus, events and behavior notes is checked in
  the browser, following the `browser-test` skill (`.claude/skills/browser-test/SKILL.md`). A
  statement that cannot be checked is removed, or kept only if the code leaves no doubt.

## 4. Update the README

This step is part of every run, including the ones that only update an existing doc: the run
is not finished until `README.md` has been checked against `docs/`.

`README.md` has a `## Components` section with one line per documented component, in
alphabetical order: `- [Accordion](docs/accordion.md)`. Add the component if it is not there,
and fix its line if the name or the path no longer matches the file. Do not put component
documentation in the README itself.

## 5. Report

Say which file was written or what changed in it, and what changed in `README.md` (the line
added or fixed, or that it was already up to date). Then list the statements left unverified,
and why. Do not list problems found in the component: components are reviewed with the
`review-component` skill, not here.

Do not commit.
