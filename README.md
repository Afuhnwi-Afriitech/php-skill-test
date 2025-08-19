# PHP Skills Test — Inventory (AJAX + JSON/XML)

**How to run**
1. Extract the zip into any PHP-enabled server directory (e.g., `htdocs` or your local PHP server root).
2. Visit `index.php` in your browser.

**What it does**
- Form: Product name, Quantity in stock, Price per item.
- Saves submitted data to **JSON** (`data/data.json`) and mirrors to **XML** (`data/data.xml`) with valid syntax.
- Displays all submitted rows ordered by *Datetime submitted* (most recent first).
- Shows **Total value number** per row (Quantity × Price) and a final **Sum total** across all rows.
- Includes inline **Edit** functionality via a Bootstrap modal.
- Uses **AJAX** (Fetch API) for adding and editing without page reload.
- UI built with **Twitter Bootstrap 5** (CDN).

**Files**
- `index.php` — UI, Bootstrap, table, form, modal.
- `api.php` — Backend endpoints: `list`, `add`, `edit`; persists JSON and XML; file locking via `flock`.
- `assets/script.js` — AJAX + DOM rendering.
- `assets/style.css` — Minor styles.
- `data/data.json` — Data store (auto-created).
- `data/data.xml` — XML mirror (auto-created).

**Notes**
- Datetimes are stored as ISO 8601 (UTC) and displayed in the browser’s local time.
- Prices are rounded to two decimals server-side.
- No database required.
