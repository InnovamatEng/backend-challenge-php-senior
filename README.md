# Innovamat coding challenge

## The Challenge

Read [the domain context](docs/domain-context.md) first: it explains what the application does and how it works.

### Session schedule

| Block                                                                                           | Duration |
| ----------------------------------------------------------------------------------------------- | -------- |
| Introduction and setup check                                                                    | 15 min   |
| [Challenge 1 - System Design Interview](docs/challenges/01-error-when-finishing-an-activity.md) | ~1h 15   |
| Break                                                                                           | 5-10 min |
| [Challenge 2 - Coding exercise](docs/challenges/02-adaptive-difficulty-level-jumping.md)        | ~1h 45   |
| Wrap-up and questions                                                                           | 15 min   |

The durations are an orientation, not a limit per challenge.

---

## Setup & Running

### Requirements

- Docker & Docker Compose

### Quick Start

```bash
# Clone and enter the project
git clone <repo-url>
cd backend-challenge-php-senior

# Full setup in one command (starts containers, installs deps, generates JWT keys, runs migrations, loads fixtures)
make setup

# Access the application
# Backend API:  http://localhost:8080/api
# Frontend:     http://localhost:3000
# Reporting:    http://localhost:8081
```

### Test Credentials

| Student     | Email               | Password    |
| ----------- | ------------------- | ----------- |
| Alice Smith | alice@innovamat.com | password123 |
| Bob Jones   | bob@innovamat.com   | password123 |

- **Alice**: has not started the itinerary yet
- **Bob**: has completed activity A1 and is currently on A2

### API Endpoints

| Method | URL                     | Description                       |
| ------ | ----------------------- | --------------------------------- |
| POST   | `/api/login`            | Authenticate student, get JWT     |
| GET    | `/api/getNextActivity`  | Get the next activity for student |
| POST   | `/api/completeActivity` | Submit answers for an activity    |
| GET    | `/api/students`         | List all students                 |
| GET    | `/api/itineraries`      | List all itineraries              |

### Example API Usage

```bash
# Login
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"alice@innovamat.com","password":"password123"}'

# Get next activity (replace TOKEN)
curl "http://localhost:8080/api/getNextActivity?itinerary=additions&student_id=1" \
  -H "Authorization: Bearer TOKEN"

# Complete activity (time_spent is in minutes)
curl -X POST http://localhost:8080/api/completeActivity \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{"activity_id":"A1","student_id":1,"answers":"1_0_2","time_spent":2}'
```

### Running Tests

```bash
# All tests of every app, from the repository root
make test

# Student platform only
make -C apps/student-platform test
make -C apps/student-platform test-unit
make -C apps/student-platform test-integration
make -C apps/student-platform test-behat

# Reporting service only
make -C apps/reporting test
```

### Useful Commands

The root `Makefile` only orchestrates: `make setup`, `make test`, `make up`, `make down`, `make build`.
Each app has its own `Makefile` with the app-specific targets. Run `make help` in any of them, or from the root:

```bash
make -C apps/student-platform help
make -C apps/student-platform shell           # Open shell in the PHP container
make -C apps/student-platform seed            # Reload fixtures (reset student progress)
make -C apps/student-platform migrate         # Run pending migrations
make -C apps/student-platform migration-diff  # Generate migration from entity changes
make -C apps/student-platform logs            # Tail container logs

make -C apps/reporting help
make -C apps/reporting shell
make -C apps/reporting logs
```

### Debugging with Xdebug (macOS)

Xdebug is preconfigured in the PHP Docker container and exposed on port `9003`.

1. Rebuild and restart containers so the extension is installed:

```bash
docker compose build platform_php
docker compose up -d
```

2. In Cursor/VS Code, install the `PHP Debug` extension (`xdebug.php-debug`).
3. Use the included launch config and start **Listen for Xdebug (Docker)**.
4. Set breakpoints in files under `apps/student-platform/backend/src`.
5. Trigger any API request (for example, with `curl` or the frontend). The debugger will stop on breakpoints.

Optional environment overrides in `docker-compose.yml`:

- `XDEBUG_MODE` (default: `debug,develop`)
- `XDEBUG_START_WITH_REQUEST` (default: `yes`)
- `XDEBUG_CLIENT_HOST` (default on macOS: `host.docker.internal`)
- `XDEBUG_CLIENT_PORT` (default: `9003`)
- `XDEBUG_IDEKEY` (default: `VSCODE`)

---

## Project Structure

```
apps/student-platform/backend/
├── src/
│   ├── Domain/          # Domain layer: models, repository interfaces, domain services
│   │   ├── Model/       # Entities
│   │   ├── Repository/  # Port interfaces
│   │   └── Service/     # Domain services (e.g. ScoreCalculator)
│   ├── Application/     # Application layer: use case services
│   │   └── Service/
│   └── Infrastructure/  # Adapters: Doctrine, HTTP controllers, Fixtures
│       ├── DataFixtures/
│       ├── Http/Controller/
│       └── Persistence/Doctrine/
├── migrations/          # Doctrine migrations
├── tests/
│   ├── Unit/
│   ├── Integration/
│   └── Behat/
└── features/            # Behat .feature files

apps/student-platform/frontend/
├── src/
│   ├── api/
│   ├── components/
│   ├── pages/
│   └── types/

apps/reporting/          # Reporting service (Symfony), receives activity attempts from the platform
├── src/
│   └── Infrastructure/Http/Controller/
└── tests/
```
