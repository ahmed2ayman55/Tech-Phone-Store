# TechStore - E-commerce Platform

## Overview

TechStore is a full-stack e-commerce platform for selling smartphones and accessories. The application provides a complete shopping experience including product browsing, cart management, checkout with order placement, order tracking, and an admin panel for product management. Authentication is handled through Replit's OpenID Connect integration.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
- **Framework**: React with TypeScript
- **Routing**: Wouter (lightweight router)
- **State Management**: 
  - TanStack React Query for server state (API data fetching/caching)
  - Zustand for client state (cart management with persistence)
- **UI Components**: Shadcn/ui component library built on Radix UI primitives
- **Styling**: Tailwind CSS with custom design tokens (CSS variables for theming)
- **Forms**: React Hook Form with Zod validation

### Backend Architecture
- **Runtime**: Node.js with Express
- **Language**: TypeScript (ESM modules)
- **API Design**: RESTful endpoints defined in `shared/routes.ts` with Zod schemas for validation
- **Session Management**: Express sessions with PostgreSQL store (connect-pg-simple)

### Data Storage
- **Database**: PostgreSQL
- **ORM**: Drizzle ORM with Zod schema generation (drizzle-zod)
- **Schema Location**: `shared/schema.ts` contains all table definitions
- **Tables**: users, sessions, products, orders, order_items, reviews

### Authentication
- **Provider**: Replit Auth (OpenID Connect)
- **Session Storage**: PostgreSQL-backed sessions
- **Implementation**: Passport.js with OpenID Client strategy
- **Protected Routes**: Middleware checks `req.isAuthenticated()` for protected endpoints

### Build System
- **Development**: Vite dev server with HMR, proxied through Express
- **Production**: 
  - Client: Vite builds to `dist/public`
  - Server: esbuild bundles to `dist/index.cjs`
- **Database Migrations**: Drizzle Kit (`db:push` command)

### Shared Code Architecture
The `shared/` directory contains code used by both frontend and backend:
- `schema.ts`: Database table definitions and TypeScript types
- `routes.ts`: API route definitions with Zod schemas for type-safe API calls
- `models/auth.ts`: User and session table schemas

## External Dependencies

### Database
- **PostgreSQL**: Primary data store, connection via `DATABASE_URL` environment variable

### Authentication
- **Replit Auth**: OpenID Connect provider (`ISSUER_URL` defaults to `https://replit.com/oidc`)
- **Required Environment Variables**: 
  - `DATABASE_URL`: PostgreSQL connection string
  - `SESSION_SECRET`: Secret for session encryption
  - `REPL_ID`: Replit deployment identifier

### UI Libraries
- **Radix UI**: Accessible component primitives (dialogs, dropdowns, forms, etc.)
- **Lucide React**: Icon library
- **Embla Carousel**: Carousel component
- **Recharts**: Charting library (available but not heavily used)

### Development Tools
- **Vite**: Frontend build tool with React plugin
- **Replit Plugins**: Runtime error overlay, cartographer, dev banner (development only)