# StrayRescue Client

React frontend for the StrayRescue web app, based on the connected Figma Make design. The project is a complete frontend experience for reporting stray animals, browsing adoptions, community updates, rescue maps, shop support, and role-based dashboards.

## Tech Stack

- React 19
- Vite 8
- Tailwind CSS 4
- Framer Motion
- Lucide React
- PHP backend API integration points via `fetch`

## Backend API

The frontend is ready to talk to your PHP backend at:

```text
http://localhost:80
```

The base URL is defined in [`src/services/api.js`](./src/services/api.js). You can override it with:

```env
VITE_API_BASE_URL=http://localhost:80
```

Current frontend API hooks are prepared for:

- Login: `/api/auth/login.php`
- Register: `/api/auth/register.php`
- Rescue report: `/api/reports/create.php`
- Adoption request: `/api/adoptions/request.php`
- Community post: `/api/community/create.php`
- Shop cart: `/api/shop/cart.php`
- Volunteer availability: `/api/volunteers/availability.php`

No PHP backend code is included in this client.

## Project Structure

```text
stray_rescue_client/
|-- public/
|   `-- favicon.svg
|-- src/
|   |-- app/
|   |   `-- rescueStore.js
|   |-- components/
|   |   |-- Button.jsx
|   |   |-- Card.jsx
|   |   |-- Footer.jsx
|   |   |-- ImageWithFallback.jsx
|   |   |-- MotionReveal.jsx
|   |   |-- Navbar.jsx
|   |   `-- StatCard.jsx
|   |-- pages/
|   |   |-- AdminDashboard.jsx
|   |   |-- AdoptionPage.jsx
|   |   |-- AuthPage.jsx
|   |   |-- CommunityPage.jsx
|   |   |-- InteractiveMap.jsx
|   |   |-- LandingPage.jsx
|   |   |-- ReportAnimalPage.jsx
|   |   |-- ShelterDashboard.jsx
|   |   |-- ShopPage.jsx
|   |   |-- UserDashboard.jsx
|   |   `-- VolunteerDashboard.jsx
|   |-- services/
|   |   `-- api.js
|   |-- App.jsx
|   |-- App.css
|   |-- index.css
|   `-- main.jsx
|-- index.html
|-- package.json
`-- vite.config.js
```

## Features

- Figma-inspired responsive landing page
- Animal report form prepared for backend submission
- Adoption cards with request action hooks
- Community page with post action hook
- Interactive rescue map mockup
- Rescue shop with cart action hook
- Role-based dashboards for users, volunteers, shelters, and admins
- Clean shared data module in [`src/app/rescueStore.js`](./src/app/rescueStore.js)
- Branded StrayRescue favicon in [`public/favicon.svg`](./public/favicon.svg)

## Getting Started

Install dependencies:

```bash
npm install
```

Start the development server:

```bash
npm run dev
```

Vite usually serves the app at:

```text
http://localhost:5173
```

Build for production:

```bash
npm run build
```

Preview the production build:

```bash
npm run preview
```

Run lint:

```bash
npm run lint
```

## Notes

- Navigation is currently state-based in [`src/App.jsx`](./src/App.jsx), so URLs do not change between app sections.
- Backend failures are shown as friendly pending messages until your PHP endpoints are available.
- The client intentionally does not include backend PHP files.
