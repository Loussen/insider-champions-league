# Insider Champions League

A Laravel 12 application that simulates a Premier League season with match predictions and real-time table updates.

## Features

- League table management with automatic updates
- Match simulation based on team strengths
- Championship predictions using multiple factors
- Fixture generation
- Match-by-match or full season simulation
- Manual match result editing
- Modern UI with Tailwind CSS

## Requirements

- PHP 8.2 or higher
- MySQL 5.7 or higher
- Composer
- Node.js & NPM (for Tailwind CSS)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/Loussen/insider-champions-league
cd league
```

2. Install PHP dependencies:
```bash
composer install
```

3. Create and configure .env file:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in .env:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=league
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations and seed the database:
```bash
php artisan migrate:fresh --seed
```

6. Start the development server:
```bash
php artisan serve
```

7. Visit http://localhost:8000 in your browser

## Project Structure

### Database Tables

- `teams`: Stores team information (name, strength)
- `matches`: Stores match results and fixtures
- `league_tables`: Stores league standings and statistics

### Key Components

#### Models
- `Team`: Manages team data and relationships
- `GameMatch`: Handles match data and team relationships
- `LeagueTable`: Manages league standings

#### Services
- `LeagueService`: Core business logic for:
  - Fixture generation
  - Match simulation
  - Table updates
  - Championship predictions

#### Controllers
- `LeagueController`: Handles:
  - League table display
  - Match simulation
  - Fixture management
  - Result updates

## How It Works

1. **Team Setup**
   - Teams are created with different strength ratings
   - Initial league table entries are generated

2. **Fixture Generation**
   - Balanced home/away fixtures
   - Random team pairing for each week

3. **Match Simulation**
   - Based on team strength ratings
   - Realistic score generation
   - Automatic table updates

4. **Championship Predictions**
   - Uses team strength (40%)
   - Current points (40%)
   - Goal difference (20%)

## Unit Tests
    php artisan test
