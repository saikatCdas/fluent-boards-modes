# Fluent Boards Modes

A WordPress plugin that adds a menu system for Fluent Boards modes, including Focus Modes.

## Features

- Vue 3 based menu component
- Renders inside the Fluent Boards `fl_app` container
- Multiple menu items with child items
- Focus Modes menu with sub-items (Deep Work, Pomodoro, Distraction Free)
- Built with Vite, Vue 3, and Tailwind CSS

## Installation

1. Install dependencies:
```bash
npm install
```

2. Build for development:
```bash
npm run dev
```

3. Build for production:
```bash
npm run build
```

## Development

The plugin uses:
- **Vue 3** for the frontend components
- **Vite** for build tooling
- **Tailwind CSS** for styling

### File Structure

```
fluent-boards-modes/
├── app/
│   └── Vite/
│       └── Vite.php          # Vite asset enqueuer
├── boot/
│   └── app.php               # Plugin initialization
├── resources/
│   └── admin/
│       ├── bootstrap/
│       │   ├── app.js        # Entry point
│       │   └── ModesMenu.vue # Menu component
│       └── styles/
│           └── app.css        # Tailwind CSS
├── assets/                   # Built files (generated)
├── vite.config.js
├── tailwind.config.js
├── package.json
└── fluent-boards-modes.php   # Main plugin file
```

## Usage

The menu will automatically appear in Fluent Boards admin pages. It renders at the top of the `.fl_app` container with:

- **Focus Modes** (with Deep Work, Pomodoro, Distraction Free sub-items)
- **View Modes** (with Kanban, List, Calendar sub-items)
- **Filter Modes** (with Today, This Week, Assigned to Me sub-items)

## Customization

You can customize the menu items by editing `resources/admin/bootstrap/ModesMenu.vue` and modifying the `modes` array in the component's `data()` method.
