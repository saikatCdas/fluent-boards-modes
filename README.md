# Fluent Boards Modes

A WordPress plugin that extends Fluent Boards with productivity-focused modes, including Focus Mode with Pomodoro timer, task management, and notes, plus Para Mode dashboard.

## Demo Video

[![Fluent Boards Modes Demo video](https://drive.google.com/file/d/10bMNfdqfxUJkEPGx60EvQ466VLzw7C5N/view?usp=sharing)](https://drive.google.com/file/d/10bMNfdqfxUJkEPGx60EvQ466VLzw7C5N/view?usp=sharing)

## Features

### Focus Mode
- **Pomodoro Timer**: Built-in timer with multiple duration modes (25/15/45 minutes)
- **Task Management**: View and manage tasks with calendar integration
- **Notes System**: Create, edit, and manage notes with markdown editor
- **Board Integration**: Link notes and tasks to Fluent Boards
- **Customizable Dashboard**: Configure which sections to display (Pomodoro, Tasks, Calendar, Notes, Quick Actions)
- **Calendar View**: Weekly calendar showing tasks by date

### Para Mode
- **PARA Methodology Dashboard**: Organize work by Projects, Areas, Resources, and Archives
- **Quick Actions**: Fast access to create tasks, boards, and stages
- **Navigation System**: Easy navigation between different PARA categories

### Additional Features
- Vue 3 based components with Composition API
- Integrated with Fluent Boards REST API
- Markdown editor for rich note-taking
- Local storage for user preferences
- Responsive design with Tailwind CSS
- Built with Vite for fast development and optimized production builds

## Installation

1. Install dependencies:
```bash
npm install
```

2. Build for development:
```bash
npm run dev
```
<!-- 
3. Build for production:
```bash
npm run build
``` -->

## Development

The plugin uses:
- **Vue 3** for the frontend components
- **Vite** for build tooling
- **Tailwind CSS** for styling

### File Structure

```
fluent-boards-modes/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ApiController.php    # REST API endpoints
│   │   └── Routes/
│   │       └── Api.php              # API routes
│   └── Vite/
│       └── Vite.php                 # Vite asset enqueuer
├── boot/
│   └── app.php                      # Plugin initialization
├── resources/
│   └── admin/
│       ├── bootstrap/
│       │   ├── app.js               # Vue app entry point
│       │   └── App.vue              # Root component
│       ├── components/
│       │   ├── FocusMode.vue        # Focus Mode dashboard
│       │   ├── ParaMode.vue         # Para Mode dashboard
│       │   ├── NotesDrawer.vue      # Notes management drawer
│       │   ├── NotesDrawerMount.vue # Notes drawer mount component
│       │   └── MilkDownEditor/      # Markdown editor components
│       ├── routes.js                # Vue Router configuration
│       ├── Bits/
│       │   └── Rest.js              # REST API utility
│       ├── js/
│       │   └── notes-drawer-mount.js # Notes drawer entry
│       └── styles/
│           └── app.css              # Tailwind CSS
├── assets/                          # Built files (generated)
├── vite.config.js
├── tailwind.config.js
├── package.json
└── fluent-boards-modes.php          # Main plugin file
```

## Usage

### Accessing Modes

After installation, navigate to **Fluent Boards → Modes** in your WordPress admin:

- **Focus Modes**: Access the Focus Mode dashboard with Pomodoro timer, tasks, calendar, and notes
- **Para**: Access the PARA methodology dashboard

### Focus Mode Features

1. **Pomodoro Timer**:
   - Select timer duration (25/15/45 minutes)
   - Start/pause/reset timer
   - Timer persists across page refreshes

2. **Task Management**:
   - View tasks filtered by status (All, In Progress, Completed, etc.)
   - Calendar view showing tasks by date
   - Click tasks to open in Fluent Boards

3. **Notes**:
   - Create notes linked to boards
   - Rich markdown editor with formatting
   - View, edit, and delete notes
   - Search and filter notes

4. **Configuration**:
   - Click "Configure" button to toggle section visibility
   - Preferences saved to local storage

### Para Mode Features

- Organize work using the PARA methodology
- Quick actions for creating tasks, boards, and stages
- Navigation between Projects, Areas, Resources, and Archives

## API Endpoints

The plugin provides REST API endpoints for notes management:

- `GET /wp-json/fluent-boards-modes/v1/notes` - List notes
- `POST /wp-json/fluent-boards-modes/v1/notes` - Create note
- `PUT /wp-json/fluent-boards-modes/v1/notes/{id}` - Update note
- `DELETE /wp-json/fluent-boards-modes/v1/notes/{id}` - Delete note

## Requirements

- WordPress 5.0+
- Fluent Boards plugin (must be installed and activated)
- PHP 7.4+
- Node.js 16+ (for development)

## Development

### Hot Module Replacement (HMR)

When running `npm run dev`, changes to Vue components will automatically reload in the browser.

### Building for Production

Run `npm run build` to create optimized production assets. The build process:
- Compiles Vue components
- Minifies JavaScript and CSS
- Generates manifest file for asset loading
- Outputs to `assets/` directory

### Creating WordPress Plugin Zip

After building, create a zip file excluding development files:

```bash
cd wp-content/plugins
zip -r fluent-boards-modes.zip fluent-boards-modes/ \
  -x "fluent-boards-modes/node_modules/*" \
  -x "fluent-boards-modes/.git/*" \
  -x "fluent-boards-modes/.vite/*" \
  -x "fluent-boards-modes/*.zip"
```
