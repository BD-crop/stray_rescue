# StrayRescue Client

Frontend client for the `StrayRescue` platform, built with React and Vite. This app presents a modern rescue-focused landing experience plus role-based authentication screens for animal lovers, volunteers, and shelter admins.

## Overview

`stray_rescue_client` is the UI layer for the broader StrayRescue project. It currently focuses on:

- A polished landing page for the rescue platform
- Section-based navigation for key user journeys
- Login and registration flows for multiple roles
- Reusable UI primitives for buttons, cards, animations, and layout

At the moment, this client is primarily a frontend prototype and presentation layer. Authentication form submission and backend API integration are not wired up yet.

## Tech Stack:

- React 19
- Vite 8
- Tailwind CSS 4 via `@tailwindcss/vite`
- Framer Motion
- Lucide React
- ESLint

## Current Experience

The app currently includes:

- `Home` landing page with hero, stats, rescue process, stories, testimonials, shelters, and call-to-action sections
- `Login` screen
- `Register` flow with role selection:
  - Animal Lover
  - Volunteer
  - Shelter Admin
- Responsive navigation for desktop and mobile
- Motion-aware reveal animations with reduced-motion support

## Project Structure

```text
stray_rescue_client/
|-- public/
|-- src/
|   |-- assets/
|   |-- components/
|   |   |-- Button.jsx
|   |   |-- Card.jsx
|   |   |-- Footer.jsx
|   |   |-- ImageWithFallback.jsx
|   |   |-- MotionReveal.jsx
|   |   |-- Navbar.jsx
|   |   `-- StatCard.jsx
|   |-- pages/
|   |   |-- AuthPage.jsx
|   |   `-- LandingPage.jsx
|   |-- App.jsx
|   |-- App.css
|   |-- index.css
|   `-- main.jsx
|-- eslint.config.js
|-- index.html
|-- package.json
`-- vite.config.js
```

## Getting Started

### 1. Install dependencies

```bash
npm install
```

### 2. Start the development server

```bash
npm run dev
```

Vite will print a local URL, usually:

```text
http://localhost:5173
```

### 3. Build for production

```bash
npm run build
```

### 4. Preview the production build

```bash
npm run preview
```

## Available Scripts

- `npm run dev` starts the Vite development server
- `npm run build` creates a production build in `dist/`
- `npm run preview` serves the production build locally
- `npm run lint` runs ESLint

## How Navigation Works

This app currently uses local component state in [`src/App.jsx`](./src/App.jsx) instead of React Router.

- `home`, `login`, and `register` render full-page views
- Other navigation targets such as `adopt`, `report`, `community`, and `shelters` scroll to matching landing page sections

This keeps the client lightweight for now, but it also means:

- URLs do not change between views
- Browser back/forward navigation is limited
- Deep-linking to specific screens is not yet supported
