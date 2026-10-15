# Similar

A yellow warning that lists records similar to the one about to be created, with buttons to create it anyway or cancel, and optionally one to use an existing record instead. Use it in create forms where duplicates with slightly different names are a problem, such as categories, tags or customers. The `TALLKit\Livewire\ConfirmsSimilar` trait runs the save → check → confirm flow on the Livewire side.

## Usage

```blade
<tk:form wire:submit="saveSimilar" :submit="! $similar">
    <tk:input name="name" label="Name" wire:model.live.debounce.400ms="name" />

    <tk:similar />
</tk:form>
```

```php
use Illuminate\Support\Str;
use Livewire\Component;
use TALLKit\Livewire\ConfirmsSimilar;
use TALLKit\Livewire\Similar;

new class extends Component
{
    use ConfirmsSimilar;

    #[Similar]
    public string $name = '';

    protected function rules(): array
    {
        return ['name' => 'required|max:255|unique:categories,name'];
    }

    protected function findSimilar(): iterable
    {
        return $this->querySimilar(Category::query());
    }

    protected function storeSimilar()
    {
        Category::create(['name' => $this->name, 'slug' => Str::slug($this->name)]);

        $this->reset('name');
        $this->toast('Category created.', type: 'success');
    }
};
```

`#[Similar]` marks the field being checked. Submitting calls `saveSimilar`, which collapses repeated spaces in that field, validates and runs `findSimilar`, here with the built-in `querySimilar`. When nothing comes back, `storeSimilar` runs right away. Otherwise `$similar` gets the records and the form shows a box titled "Similar records found" that lists them and asks `Do you want to create "Food" anyway?`, with Create anyway and Cancel buttons. `:submit="! $similar"` hides the form's submit button while the box is open. Create anyway validates again and calls `storeSimilar`. Cancel closes the box. Typing in the field also closes it and clears its error, so the list never refers to a value the user has since changed; if the value changes in the same request as the click on Create anyway (before the debounce fires, or with a deferred `wire:model`), the new value is checked again instead of stored. Inside a Livewire component `tk:similar` reads `$similar` and the `#[Similar]` field by itself. Every default text is translated.

### Using an existing record

```blade
<tk:similar select="useExisting" />
```

```php
public function useExisting(int $id): void
{
    $this->redirectRoute('categories.show', $id, navigate: true);
}
```

Each listed record gets a "Use this" button. Clicking the one next to Foods calls `useExisting(1)` with that record's `id`.

### Finding similar records

```php
protected function findSimilar(): iterable
{
    return $this->querySimilar(
        Category::query()->where('store_id', $this->store->id)->whereKeyNot($this->category->id),
        column: 'title',
        threshold: 80,
    );
}
```

`querySimilar` compares the slug of the `#[Similar]` field with the slug of the column in each record of the query: a record is similar when one slug contains the other ("camisa" and "Camisa polo"), or when `similar_text` gives at least `threshold` percent ("Camisetas" and "Camisas"). Slugs ignore case and accents, so "calcados" and "Calçados" count as the same. Containment only counts when both slugs have at least `minLength` characters (`3` by default), so "iv" is not similar to "Atividades"; pass `minLength: 2` to accept two-letter matches. A value without letters or digits finds nothing. The column defaults to the `#[Similar]` field's name, and only the key and that column are loaded, so nothing else of the record reaches the page. Every record of the query is compared in PHP: narrow the query first on large tables. The `whereKeyNot` above leaves out a record by hand; `#[Similar(record: ...)]` does it for you, see the next example.

### Editing a record

```php
use ConfirmsSimilar;

public Category $category;

#[Similar(record: 'category')]
public string $name = '';

public function mount(Category $category): void
{
    $this->category = $category;
    $this->name = $category->name;
}

protected function rules(): array
{
    return ['name' => ['required', Rule::unique('categories', 'name')->ignore($this->category)]];
}

protected function findSimilar(): iterable
{
    return $this->querySimilar(Category::query());
}

protected function storeSimilar(): void
{
    $this->category->update(['name' => $this->name]);
}
```

`record` names the property holding the model being edited. `querySimilar` leaves that record out of the list, the question becomes `Do you want to save "Camisa" anyway?` and the button Save anyway. Saving without changing the name stores right away, without looking for similar records. When the property holds a model that is not saved yet (`new Category`), everything works as when creating, so one component can do both. The `unique` rule has to ignore the record too, or saving an unchanged name fails validation.

### Other fields as key and label

```blade
<tk:similar item-key="uuid" item-label="email" />
```

`item-key` and `item-label` choose which field identifies each record and which one is shown, read with `data_get`, so the items can be arrays or models. Here each row shows the email, and `select` would receive the `uuid`.

### Without the trait

```blade
<tk:similar
    :items="$matches"
    title="This customer may already exist"
    message="Register a new customer anyway?"
    confirm="forceCreate"
    cancel="clearMatches"
    confirm:label="Register"
/>
```

The component only renders: `confirm` and `cancel` take the names of any Livewire actions, and the texts can all be replaced. With no items it renders nothing.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array \| Collection | the component's `$similar` | The similar records. When empty, nothing is rendered. |
| `value` | string | the `#[Similar]` field | The value being created, quoted in the default question. |
| `item-key` | string | `id` | The field of each item used for its `wire:key` and passed to `select`. |
| `item-label` | string | `name` | The field of each item shown in the list. |
| `select` | string | `null` | A Livewire action called with the item key, e.g. `useExisting` → `useExisting(1)`. Adds a "Use this" button per item; without it there is no button. |
| `confirm` | string | `confirmSimilar` | The Livewire action of the Create anyway button, labeled Save anyway when editing a `record`. |
| `cancel` | string | `cancelSimilar` | The Livewire action of the Cancel button. |
| `message` | string | `Do you want to create ":value" anyway?` | The question under the list, translated. When editing a `record` the default is `Do you want to save ":value" anyway?`, and without a `value` it is `Do you want to continue anyway?`. |
| `title` | string | `Similar records found` | The heading of the box, translated. |
| `size` | string | `null` | The size of the box and of the Create anyway and Cancel buttons. |

Other attributes go to the [alert](alert.md) the box is built on, so `icon`, `variant`, `border` and `closable` work as there.

### `ConfirmsSimilar` trait

| Member | Description |
|--------|-------------|
| `#[Similar(record: ?string)]` | Attribute for the field being checked. Editing it clears `$similar` and the field's error. `record` names the property with the model being edited. |
| `public array $similar` | The records found by the last `saveSimilar`. Locked: the browser cannot change it. |
| `saveSimilar()` | Runs `Str::squish` on the `#[Similar]` field, validates, runs `findSimilar()` and calls `storeSimilar()` when nothing is similar. When the field still equals the record's saved value, it skips `findSimilar()`. |
| `confirmSimilar()` | Validates again, clears `$similar` and calls `storeSimilar()`. When `$similar` is already empty because the `#[Similar]` field changed, it runs `saveSimilar()` instead. |
| `cancelSimilar()` | Clears `$similar`. |
| `findSimilar(): iterable` | Abstract: return the records similar to the current input. |
| `querySimilar($query, $column, $threshold, $minLength)` | Returns the records of an Eloquent query (or relation) similar to the `#[Similar]` field, as `[key, column]` arrays, leaving out the `record` being edited. `$column` defaults to the field name, `$threshold` to `75`, `$minLength` (the shortest slug that matches by containment) to `3`. |
| `storeSimilar()` | Abstract: create the record. What it returns (a redirect, for instance) is returned by `saveSimilar` and `confirmSimilar`. A `UniqueConstraintViolationException` thrown here becomes the `validation.unique` error on the `#[Similar]` field; without the attribute it is thrown again. |

Validation runs only when the component has rules (`rules()`, `$rules` or `#[Validate]`).

## Customization

Classes and attributes on the tag go to the box. To reach an inner element, prefix the attribute:

| Prefix | Goes to |
|--------|---------|
| `items:` | The list. |
| `item:` | Each list item. |
| `select:` | Each "Use this" button. |
| `actions:` | The row with the Create anyway and Cancel buttons. |
| `confirm:` | The Create anyway button. |
| `cancel:` | The Cancel button. |

```blade
<tk:similar
    :items="$similar"
    :value="$name"
    select="useExisting"
    select:label="Open"
    confirm:label="Create category"
    confirm:variant="primary"
    items:class="divide-y"
/>
```

## Accessibility

The box is a warning [alert](alert.md): it has `role="alert"` and `aria-live="assertive"`, so a screen reader announces the list as soon as it appears. All actions are regular buttons, reached with Tab.

## Behavior notes

- `title` and `message` take plain text only; a slot with that name is not used.
- Item labels are printed as they are, never passed through the translator, so a record named "Cancel" is not shown as "Cancelar".
- Only the `#[Similar]` field closes the box when it changes; other fields of the form leave it open. Without the attribute nothing closes it but Cancel and Create anyway.
- The unchanged check compares the field with the attribute of the same name on the record (`getOriginal`), not with `querySimilar`'s `column`, and only exact strings count: changing only the case still looks for similar records.
- `items` and `value` passed as props win over the ones read from the component, so a second `tk:similar` with its own data can sit in the same component.
- The exact-duplicate check belongs in the rules (a `unique` rule, for example): since `confirmSimilar` validates again, an exact duplicate stays blocked even after the user confirms.

## Related

- [Danger zone](danger-zone.md): confirmation before a destructive action, in a dialog.
- [Alert](alert.md): a plain message box, without the list or the actions.
