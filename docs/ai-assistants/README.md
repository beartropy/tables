# AI Assistant Support for Beartropy Tables

Beartropy Tables includes comprehensive AI assistant integration to help you build data tables faster. This guide covers how to use Beartropy Tables with different AI coding assistants.

## Supported AI Assistants

### Claude Code (Full Support)
- Native skills with slash commands
- Context-aware component suggestions
- Complete code examples
- Interactive help

### Cursor (Full Support)
- Custom rules integration
- Component autocomplete context
- Beartropy-specific suggestions

### Other AI Tools (Content Support)
- Universal guide for any AI assistant
- Complete examples and patterns
- Copy-paste ready code

## Directory Structure

```
beartropy/tables/
├── .claude/
│   └── skills/                    # Claude Code skills (slash commands)
│       └── bt-tables-setup/
│
└── docs/
    ├── llms/                      # LLM reference docs per component
    │   ├── yat-base-table.md
    │   ├── column.md
    │   ├── filter.md
    │   └── ...
    ├── components/                # User reference docs per component
    │   └── ...
    └── ai-assistants/
        ├── README.md              # This file
        ├── BEARTROPY_GUIDE.md     # Universal AI guide
        ├── cursor/
        │   └── .cursorrules       # Cursor configuration
        └── examples/              # Code examples
            ├── basic-tables.md
            ├── filters.md
            └── patterns.md
```

## Quick Start by Tool

### Using with Claude Code

**Install Beartropy Tables**, then use built-in skills:

```bash
/bt-tables-setup              # Installation & configuration help
```

Skills are automatically available in the `.claude/skills/` directory.

---

### Using with Cursor

**Option 1: Add Cursor Rules (Recommended)**

Copy `.cursorrules` from `docs/ai-assistants/cursor/` to your project root:

```bash
cp vendor/beartropy/tables/docs/ai-assistants/cursor/.cursorrules .cursorrules
```

Or append to existing `.cursorrules`:

```bash
cat vendor/beartropy/tables/docs/ai-assistants/cursor/.cursorrules >> .cursorrules
```

**Option 2: Reference the Guide**

Tell Cursor to read the universal guide:

```
@docs/ai-assistants/BEARTROPY_GUIDE.md Create a user management table with search and filters
```

---

### Using with Windsurf/Cascade

Reference the universal guide in your prompts:

```
Using the Beartropy Tables guide at vendor/beartropy/tables/docs/ai-assistants/BEARTROPY_GUIDE.md,
create a sortable table with inline editing
```

---

### Using with Cody, Copilot, or Other AI Tools

**Option 1: Read the Universal Guide**

Point your AI assistant to:
```
vendor/beartropy/tables/docs/ai-assistants/BEARTROPY_GUIDE.md
```

**Option 2: Use Examples**

Browse ready-to-use examples in:
```
vendor/beartropy/tables/docs/ai-assistants/examples/
```

---

## Available Resources

### Skills (Claude Code)
- **bt-tables-setup** - Installation, configuration, troubleshooting

### Universal Guide
- **BEARTROPY_GUIDE.md** - Complete component reference
- Works with any AI assistant
- All column types, filter types, and patterns
- Copy-paste ready examples

### Cursor Rules
- **.cursorrules** - Cursor-specific configuration
- Component syntax and best practices
- Automatic suggestions

### Code Examples
- **examples/basic-tables.md** - Model and array table patterns
- **examples/filters.md** - Filter configurations and custom queries
- **examples/patterns.md** - Advanced patterns (bulk, editing, export)

## Tips for Best Results

### 1. Be Specific About Components
- "Create a table" -> "Create a Beartropy Tables component extending YATBaseTable with columns and filters"

### 2. Mention Data Mode
- "Model-based table with User model" (Eloquent-powered)
- "Array-based table from API data" (no database)

### 3. Reference Column and Filter Types
Use exact class names:
- `Column`, `BoolColumn`, `DateColumn`, `LinkColumn`, `ToggleColumn`
- `FilterString`, `FilterSelect`, `FilterBool`, `FilterDateRange`, `FilterSelectMagic`

### 4. Ask for Complete Examples
- "Show me a complete Livewire table with sortable columns, search, and select filters"

## Common Use Cases

### Creating Basic Tables
- **Claude Code**: `/bt-tables-setup`
- **Cursor**: "Create table" (with .cursorrules)
- **Other**: Reference `examples/basic-tables.md`

### Adding Filters
- **Cursor**: "Add string and select filters to table"
- **Other**: Reference `examples/filters.md`

### Advanced Patterns
- **Cursor**: "Add bulk actions and inline editing"
- **Other**: Reference `examples/patterns.md`

## Getting Help

### General Support
- **Documentation**: https://beartropy.com/tables
- **GitHub Issues**: https://github.com/beartropy/tables/issues

## License

These AI assistant resources are part of Beartropy Tables and are provided under the MIT License.

---

**Choose your AI assistant above and start building powerful data tables faster!**
