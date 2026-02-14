---
name: bt-tables-docs-maintenance
description: Update documentation and AI integrations when adding or modifying Beartropy Tables components
version: 1.0.0
author: Beartropy
tags: [beartropy, tables, docs, maintenance, internal, mcp, skills]
---

# Beartropy Tables Docs & Integrations Maintenance

You are maintaining the Beartropy Tables documentation and AI integration layer. Component knowledge is exposed through **two independent channels** that must stay in sync:

| Channel | Mechanism | Audience |
|---|---|---|
| **Skills** (`beartropy:skills`) | Artisan command discovers `docs/llms/*.md` and generates a component skill | AI agents with skill files installed |
| **MCP** (Laravel Boost) | MCP tools auto-registered when Boost is present — agents call them on demand | AI agents connected to the app's Boost MCP server |

---

## File Locations

```
docs/
├── llms/{name}.md          <- LLM reference (architecture, props, slots, presets)
└── components/{name}.md    <- User reference (usage examples, prop table, tips)

src/
├── Mcp/Tools/
│   ├── ComponentDocs.php   <- Reads docs/llms + docs/components dynamically
│   └── ListComponents.php  <- Hardcoded CATEGORIES const
└── YATProvider.php         <- Registers tools with Laravel Boost
```

---

## When Adding a New Component

### 1. Create both doc files

**`docs/llms/{name}.md`** — Use the template in `docs/llms/_template.md`

**`docs/components/{name}.md`** — Use the template in `docs/components/_template.md`

Use existing doc files as reference — pick a component with similar complexity.

### 2. Update MCP category map

Edit `src/Mcp/Tools/ListComponents.php` and add the component name to the appropriate category in the `CATEGORIES` constant. Keep each list in **alphabetical order**.

### 3. Skills — no code change needed

`InstallSkills::buildComponentSkill()` auto-discovers all `docs/llms/*.md` files via glob. Creating the LLM doc file is sufficient.

### 4. Checklist

- [ ] `docs/llms/{name}.md` created
- [ ] `docs/components/{name}.md` created
- [ ] `src/Mcp/Tools/ListComponents.php` — component added to `CATEGORIES`
- [ ] `vendor/bin/pest` — all integrity tests pass

---

## When Modifying an Existing Component

1. Update `docs/llms/{name}.md` — keep props table, architecture section, and examples accurate
2. Update `docs/components/{name}.md` — update the user-facing prop table and examples
3. No other code changes needed (MCP reads files dynamically, skills regenerate on next install)

---

## When Removing a Component

1. Delete `docs/llms/{name}.md`
2. Delete `docs/components/{name}.md`
3. Remove from `CATEGORIES` in `src/Mcp/Tools/ListComponents.php`

---

## Quick Reference

| Action | docs/llms | docs/components | ListComponents::CATEGORIES | InstallSkills | ComponentDocs |
|---|---|---|---|---|---|
| Add component | Create | Create | Add entry | Auto (glob) | Auto (file read) |
| Modify component | Update | Update | No change | Auto (glob) | Auto (file read) |
| Remove component | Delete | Delete | Remove entry | Auto (glob) | Auto (file read) |
