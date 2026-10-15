# Declined review items

Findings and suggestions the user rejected, by component. The `review-component` skill reads
this before reporting and does not raise these again.

Format: `- YYYY-MM-DD — <what was proposed, described by behavior> — <the user's reason, if given>`

## badge
- 2026-10-13 — own `tk:badge.close` written inside a linked badge: the close dismisses the badge but a link with `wire:navigate` (the default) still navigates; proposed making `badge/close.blade.php` stop mousedown/mouseup/click and dispatch the dismiss itself — user chose to keep the partial fix, no reason given (Claude's recommendation: rare case with `closable` as the alternative, not worth extra handlers on every badge close); partial fix kept (`z-1` on the close, `preventDefault()` in `dismissible.js`)

## button/group
- 2026-10-13 — removing the `group/button` class from `button/group.blade.php` because no package view, CSS or JS uses a `group-*/button` variant — removed on approval, then restored the same day after the user questioned it; Claude's reading: it is a public hook for app classes (`group-hover/button:`), like `group/row`, `group/chip` and `group/item`, which the package also declares without using

## copyable
- 2026-10-14 — removing the always-true `if (failedText)` guard in `copyable.js` (the view always passes `__('Copy failed')`) — left unchecked by the user, no reason given

## loadable
- 2026-10-14 — wrap each state of loadable (or the fetchable slot) so a slot with several root elements shows all of them (x-if only clones the first) — user chose to keep a single root element; a display:contents wrapper broke Chart.js sizing (canvas 0x0)

## layout
- 2026-10-14 — a visually hidden "Skip to content" link (href #main-content, shown fixed on focus) as the first element of layout.header and layout.sidebar — user declined
