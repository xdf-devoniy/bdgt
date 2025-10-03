# MoneyFlow

MoneyFlow is a personal finance manager built with PHP, MySQL, and Bootstrap. It helps you capture transactions in seconds, monitor budgets, and analyse your cashflow trends with rich interactive charts — all in soʻm (UZS) by default.

## Getting Started

1. Copy the example environment file and update it with your local credentials:

   ```bash
   cp .env.example .env
   ```

2. Configure your web server (Nginx/Apache) to serve the `public/` directory as the document root.

3. Install PHP dependencies if you plan to use Composer packages:

   ```bash
   composer install
   ```

4. Create the database schema by importing the ready-made SQL in `scripts/schema.sql`.

5. Make sure `.env` points `DEMO_USER_ID` to a valid `users.id` record that already has wallets, categories, transactions, and budgets associated with it.

6. Visit the application in your browser to see the live cashflow metrics rendered on the dashboard.

## Core Features

- **Fast transaction capture:** add incomes and expenses from a Bootstrap modal with keyboard shortcut (`N`), validation, and automatic filtering of categories by type.
- **Smart filters:** slice and dice the ledger by type, wallet, category, date range, or free-text search (notes, merchants, tags).
- **Cashflow insights:** dashboard cards, line and doughnut charts, and budget progress using Chart.js.
- **Soʻm-first:** UZS is the base currency out of the box, with rate support for future multi-currency entries.
- **Responsive UI:** built on Bootstrap 5, Alpine.js, and Chart.js for a modern, mobile-friendly experience.

## Project Structure

```
app/
  Controllers/   # Page controllers (Dashboard, Transactions, Reports, ...)
  Models/        # ORM/Query classes for interacting with MySQL
  Services/      # Infrastructure services such as Database, Importers, Alerts
  Views/         # PHP view templates rendered inside the layout
config/          # Environment configuration loader
public/          # Public assets and the front controller (index.php)
resources/       # Layouts and partials shared across views
scripts/         # Database migrations and seeders (planned)
tests/           # Automated test suites
```

## Frontend

- **Bootstrap 5** with a white theme, rounded surfaces, and subtle shadows
- **Alpine.js** for lightweight interactivity
- **Chart.js** for analytic visualisations (line, doughnut charts included in the starter dashboard)

## Roadmap

- [ ] Transactions CRUD with quick add and CSV import
- [ ] Budgets with overspend alerts and suggestions
- [x] Soʻm (UZS) base currency and keyboard-driven quick add
- [ ] Advanced reports including calendar heatmaps and forecasts
- [ ] Goals tracking with progress visualisations
- [ ] Multi-currency conversions and historical rates

Contributions and feedback are welcome!
