<<<<<<< HEAD
# stray_rescue

### Introduction

**stray_rescue** is a web application designed to address the critical problem of stray and
abandoned animals in **Bangladesh**. The platform bridges the gap between animal rescue efforts,
volunteer networks, and animal shelters through a centralized, location-based digital solution.
Currently, there is no organized system for reporting stray animals, notifying nearby volunteers,
and coordinating rescue efforts, resulting in delayed responses and unnoticed animals in
distress. Stray_Rescue solves this by providing real-time notifications to volunteers based on
geographical proximity, enabling faster rescue operations and better care coordination. The
system ensures that no stray animal goes unnoticed while fostering community engagement
and improving overall animal welfare.

### Objectives

Our core objective at **stray_rescue**, is focusing on making animal rescue faster, more
organized, and accessible to everyone in the community. Here's what we aim to achieve:
- **Make Reporting Easy and Detailed:** Users can provide comprehensive information
including the exact location (via GPS coordinates), animal species, estimated age, and
current health condition—giving rescue teams all the information they need to respond
effectively.
- **Activate Volunteers in Real-Time:** Our notification system automatically alerts nearby
volunteers via email whenever a stray animal is reported, ensuring that compassionate
volunteers closest to the location can respond quickly and save precious time.
- **Empower the Volunteer Community:** Volunteers are the backbone of animal rescue.
We provide them with a complete management system where they can register their
profiles, set their availability based on location, track their rescue activities, and feel
recognized for their life-saving work. And the work of volunteers will be recognized via
online leader board system
- **Managing Shelter Animals:** Our platform will provide powerful tools to efficiently
manage animal inventory, maintain detailed health and medical records, and showcase
adoptable animals to find them loving forever homes.
- **Visualize the Big Picture:** Knowledge is power. An interactive map brings the entire
rescue ecosystem to life, showing stray animal locations, nearby shelters, and volunteer
networks in one intuitive view, making coordination seamless and transparent.
- **Measure Impact and Improve:** Data-driven decisions save lives. We provide
comprehensive analytics dashboards that track rescue statistics, measure volunteer
participation, monitor shelter capacity, and identify areas for improvement—enabling the
community to become more effective with time.
- **Build a Compassionate Community:** Animal rescue isn't a solitary effort. We foster
genuine community engagement by enabling users to share tips, leave encouraging
comments, offer temporary foster care, and celebrate rescue successes
together—turning strangers into a unified force for animal welfare.
- **Preference based forever home Matching:** Our matching system takes inspiration
from preference based matching systems of dating applications. Using user / animal
characteristics data our system will show users with those animals which matches their
preferences and lifestyle.
- **An online pet grooming item shop:** Our platform will provide us with a platform where
pet owners/potential owners can buy grooming items for their pets.

### Scope of the Project

#### Problems Addressed:
- Lack of organized reporting system for stray animals in Bangladesh.
- Delayed emergency response due to poor communication between users and
volunteers.
- Fragmented/inhumane shelter management and adoption processes
- Lack of preference based animal to owner matching system
#### Features Included:
- User registration and authentication
- Stray animal report submission with location (GPS coordinates), species, age, and
health status
- Volunteer registration and location-based profile management
- Automated email notifications to nearby volunteers
- Animal shelter management dashboard
- Interactive map with real-time stray animal reports and local shelter locations
- Health monitoring and medical record tracking
- Adoption listings and foster care coordination
- Community engagement features (tips, comments, reviews)
- Analytics dashboard for rescue statistics and performance metrics
- User preference-based matchmaking with foster animals to better match animals with
owners.
- An in-house shop filled with pet grooming products.
- Foster animal notices made by admin , users can see these posts , comment and make
adoption decisions based on these posts. The post will include animals species , gender
, health condition , age , how much active the animal is
#### Features NOT Included:
- Mobile app version (Phase 1 focuses on web application)
- Integration with third-party payment gateways for adoptions
- SMS notifications (email only)
- Machine learning based ideas.

### Tools & Technologies
#### Database Management System:
• MySQL 8.0+ (Relational Database management system)
#### Frontend Technologies:
• HTML5
• CSS3 for responsive design and styling
• JavaScript for interactive features and client-side logic
#### Backend Technologies:
• Xampp for server side logic implementation
• PHPMailer/other mailing library/services for automated email notifications
• RESTful API for communication between frontend and backend
#### Additional Tools:
• Git for version control
• phpMyAdmin for database administration
• Postman for API testing and documentation
=======
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
>>>>>>> 44548974e0c5bc28d889a7eee090ff5108341002
