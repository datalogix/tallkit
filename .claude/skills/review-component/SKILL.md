---
name: review-component
description: Review a single TALLKit component for bugs, accessibility, performance, unneeded code and API. Produces a verified diagnosis first and only implements approved items. Use when asked to review, audit or improve a component (e.g. "review the modal", "revisa o accordion").
argument-hint: <component> [bugs|a11y|perf|code|api] [full]
---

# Review component

Arguments: `$ARGUMENTS`

The first word is the component name (e.g. `accordion`). The optional second word limits the
review to one dimension: `bugs`, `a11y`, `perf`, `code` or `api`. Without it, cover all five,
with the quick performance check only; the full one runs when `perf` is asked for by name.
The word `full`, anywhere after the name, ignores the record of previous reviews (step 1).
If no component was given, ask which one and stop.

Review one component per run. If asked for several, do the first and list the rest as pending.

## What counts as a finding

The goal is to tell whether the component has a problem worth the user's time, not to produce a
list. Most components here have already been reviewed, so **"no important issues" is the
expected result of many runs and a complete, valid answer**. A run that reports nothing is not a
failed run; a run that reports something minor to avoid coming back empty is.

A finding is reported only if all of these hold:

- It has a concrete failure scenario: specific props, state or user action that leads to a wrong
  result someone would notice. "Could be more robust", "might break if" and "would be cleaner"
  are not scenarios.
- It happens in a supported use of the component, not in misuse or in a combination the
  component never claimed to handle. When `docs/<component>.md` exists, it says what is
  supported.
- It is high or medium severity:
  - **high**: breaks the default usage, loses user input, or blocks keyboard or screen-reader
    users from operating the component
  - **medium**: wrong behavior in a supported but less common case (a prop combination, nested
    instances, after a Livewire morph), or something the APG pattern requires that is missing
    or wrong in the default usage without blocking anyone
  - anything below that (style, naming, duplicated code, micro-optimizations, optional ARIA,
    defensive checks) is not reported
- It is not already a deliberate choice. Check the memory notes and nearby code comments for
  intentional exceptions, and read the component's section in `declined.md` (next to this file):
  what is listed there was already proposed and rejected, so it is not raised again. The only
  exception is when the code changed since and there is a new failure scenario; then say it was
  declined before and what is different now.

Cleanups (`code`) and suggestions (`api`) are not findings: each has its own bar in step 2.

The checklists in step 2 say where to look. They are not a quota: a dimension with nothing to
report is left empty.

## 1. Map the scope

Find the component's files yourself and list them before anything else:

- Blade views in `resources/views/<component>/`
- Scripts in `resources/js/components/` (the script name may differ from the view name,
  e.g. accordion uses `disclosure.js` and `disclosure-group.js`)
- Mixins in `resources/js/mixins/` and utils in `resources/js/utils/` that it imports
- PHP it depends on in `src/`
- Its styles in `resources/css/`
- Its callers: the other components whose views render this one, and what each passes to it
  (props, actions, slots). Those are supported uses, and the ones a review most easily misses.

Mark each file as **own** (used only by this component) or **shared** (used by others; grep to
confirm, including aliased imports). Never propose a change to a shared file without listing
who else uses it and what the impact is.

Do not touch other components. The one exception is a fix that introduces a util (step 6);
the report must then list every other file it would change, so it is approved knowingly.

### Skip what was already reviewed

`reviewed.md` (next to this file) records, per component, the dimensions the last review
covered and the content hash of each mapped file at that point. Re-auditing code that has not
changed only produces a different opinion about the same code, so after mapping, hash every
mapped file (own and shared) with `git hash-object <file>`, which hashes the working tree
content whatever is staged or committed, and compare:

- Every hash matches and every requested dimension is recorded: report "No changes in
  `<component>` since the review of <date>" and stop
- Every hash matches but a requested dimension is not recorded: run only the missing ones
- Some hash differs: review the files that changed and the behavior they affect; the unchanged
  files are context to read, not code to audit again
- No entry, or `full` was given: review everything

## 2. Diagnose, without editing anything

### Bugs
- Inconsistent state (open/closed, selected, disabled, loading) and prop combinations
- Each caller found in step 1: follow what it passes through the component, one by one
- Initial state, nested instances, several instances on the same page
- State lost or duplicated after a Livewire morph; missing `wire:ignore` / `wire:ignore.self`
- Listeners, observers and timers not cleaned up on destroy
- Form integration: reset, `wire:model`, hidden fields

### Accessibility
Identify the matching WAI-ARIA APG pattern (accordion, dialog, combobox, listbox, tabs, menu...)
and compare the component against it. Only what the pattern requires counts; optional keys and
optional attributes are not findings.
- Roles, states and properties (`aria-expanded`, `aria-controls`, `aria-labelledby`...)
- Keyboard interaction required by the pattern
- Focus management and visible focus; hidden content out of the tab order
- Accessible name for icon-only controls
- `prefers-reduced-motion`

### Performance
- Repeated work in the Blade render
- Init cost per instance and per item
- Per-item listeners where delegation would do
- Layout thrashing (interleaved reads and writes), animations on layout properties

A performance finding needs a number, and `perf-baseline.md` (next to this file) has the
recorded ones.

- **Quick check, every run**: measure the server render with `bench.php`, as described in
  `perf-baseline.md`, and compare with the component's rows. It takes seconds and changes
  nothing. The other three points are only read in the code; do not build a measuring page.
- **Full check, only with `perf`**: also measure the Alpine init in the browser with
  `measureInit` from the `browser-test` skill, on a few hundred instances.

If the component has no row yet, add the row: a first measurement is a baseline, not a
finding, unless it is far out of line with comparable components. If it is in line with the
baseline, or the gain would be too small for a user to notice, there is no finding. When the
code reading suggests an init problem the quick check cannot confirm, say so in "Checked" and
suggest a run with `perf`; do not report it as a finding.

### Unneeded code
Go through every variable, property, method and helper function the component's own files
declare (JS and Blade) and ask whether it has to exist. These are cleanups, not defects, so
they follow their own bar instead of the severity scale. List one only when it is:

- Unused: never read or called. Grep before saying so, including Blade and Alpine attributes
  (`x-on`, `x-bind`, `x-init`), mixins, other components and aliased imports
- Redundant state: a property that mirrors something already available (another property, a
  DOM attribute, a prop) and has to be kept in sync by hand
- A pass-through: a variable that only renames an expression used once or twice, or a function
  that only forwards to another call
- A single-use helper whose body is a few lines and reads fine inline at its only call site

The first two are facts and are always listed. The last two are judgment calls: list them only
when the case is obvious, the kind nobody would argue to keep; when in doubt, it stays. At most
five items in total, the unused and redundant ones first.

Keep what earns its place: state the component really holds, handler references needed to
remove a listener, values read many times or costly to compute, and anything other files or
the public API rely on. A name that makes a hard expression readable also stays. Duplicated
code is not a reason to list anything here.

For each item give `file:line`, what it is, where it is used, and the code after removing or
inlining it. If everything declared is needed, say so in "Checked" and list nothing.

### API / DX
Suggestions, not defects, so they follow their own bar instead of the severity scale. Suggest
only what a developer using the component would clearly gain from:

- It breaks a convention the other components follow (a prop, slot or event named or shaped
  differently from its siblings); name the components it diverges from
- A common use needs a workaround today (overriding classes, wrapping, extra markup or JS);
  show the workaround and what the usage would look like instead
- A prop or slot is misleading: its name or default suggests a behavior it does not have

Each suggestion states who benefits and shows current versus proposed usage. If you cannot
write that example, drop the suggestion. Renames by taste, extra props "for flexibility" and
options nobody needed yet are not suggestions. At most three, strongest first; none is fine.

Never implement these without approval.

## 3. Verify every finding

Before a finding goes into the report, confirm it: re-read the exact code path end to end, or
reproduce it. Actively try to disprove it first: look for the guard, the default, the caller or
the shared mixin that already handles the case.

Anything that depends on runtime behavior (focus, keyboard, Livewire morph, visual state,
timing, console errors) is reproduced in the browser by you, following the `browser-test`
skill (`.claude/skills/browser-test/SKILL.md`): it has the helper, the demo app in `../app` and
its rules. Do not hand that to the user as a step to test; a case out of reach of a headless
browser stays suspected. Then classify:

- **Confirmed**: you traced it in code that leaves no doubt, or reproduced it in the browser
- **Suspected**: plausible, but not confirmed; say what is missing to confirm

Drop anything that does not survive this step, and anything that fails the bar in "What counts
as a finding". A suspected item is reported only if it would be high severity when true.

## 4. Report

Write the report in the language the user writes in, keeping identifiers and code in English.

Start with a one-line verdict: either the number of important issues, or "No important issues
found in `<component>`".

Then, only if there are findings, list them most severe first. For each:

- `file:line`
- The problem, in one or two sentences
- The failure scenario: what to do to see it, and what goes wrong
- Proposed fix, as the smallest change to the existing code; if it needs a new variable, method
  or helper, say so and why
- Severity: high / medium
- Confirmed or suspected

Close with a short "Checked" section, one line per dimension, naming what was actually verified
(e.g. "a11y: compared against APG dialog; focus trap, Escape and focus return work"). This is
what makes an empty result trustworthy, so name real checks, not the checklist.

Unneeded code and API suggestions, if any, go in their own sections after the findings, kept
apart from the defects and not counted in the verdict.

A problem noticed in another component on the way, if it would pass the bar, gets one line
under "Seen elsewhere" naming the component and the symptom; do not investigate it in this run.

Do not add a list of minor notes, nice-to-haves or future ideas. If there are findings,
cleanups or suggestions, end with the items awaiting approval, then stop and wait. If there are none, stop
after "Checked".

## 5. Record what was declined

When the user answers, add every finding, cleanup or suggestion they explicitly rejected to
`declined.md`, under a `## <component>` heading (create it if missing), one line each:

`- YYYY-MM-DD — <what was proposed, described by behavior> — <the user's reason, if given>`

Describe the item by behavior and file, not by line number, so the entry still matches after
the code moves. Items the user simply did not answer are not recorded. Do this before
implementing, and also when everything was rejected.

## 6. Implement, after approval

- Only the approved items, in small focused changes; no unrelated refactoring
- Follow the project conventions (naming helpers instead of literals, `emit()` for events,
  props forwarding rules, `has_content()` for string|slot props)
- Fix in place, with the smallest edit to the existing lines. Do not add variables, properties,
  methods or helper functions to the component unless the fix cannot work without them: new
  state the component really has to keep, or a handler reference needed to remove a listener.
  A value used once or twice stays inline, and repeating a few lines is fine; do not extract
  them just to avoid duplication or to name a step.
- A new util is the rare case, for logic several components already repeat. If a fix does
  introduce one, replace every similar spot in the same change
- Documentation: if `docs/<component>.md` exists and a fix changed something it describes (a
  prop, slot, event, default or behavior), update only that part of it. Never create the file
  and never write documentation for what did not change; that belongs to another skill.
- When done, run `composer lint`, and `pnpm build` if any JS or CSS changed
- Re-run each fixed scenario in the browser and confirm it no longer fails, the rest of the
  page still works and the console is clean; report what you saw
- Do not commit
- End the reply with a "Changed files" section: one line per file this step edited, with the
  changed lines as clickable `file:line` or `file:start-end` (several ranges comma separated)
  and a few words on what changed. Take the lines from your own edits, not from `git diff`,
  since the tree may hold other uncommitted work. Leave out build output (`dist/`) and this
  skill's records (`declined.md`, `reviewed.md`, `perf-baseline.md`)

## 7. Record the review

Once every reported item was either implemented or declined (or right after the report, if it
had none), update the component's entry in `reviewed.md`. Hash the files as they are now, after
the fixes:

```
## <component>
- YYYY-MM-DD — <dimensions covered, comma separated>
- <file path> <hash>
```

If the hashes are the same as the existing entry, add this run's dimensions to its list;
otherwise replace the entry with this run's dimensions and hashes. While items are still
waiting for an answer, do not record.
