# Innovamat coding challenge

As a part of the MVP of the Innovamat application, it is needed to develop a functionality to provide the students a set of activities of an area (for example additions) so they can have a productive learning process.

The activities are grouped in itineraries, and each activity has a difficulty associated. The difficulty (D) is a
natural number between 1 and 10.

An itinerary may include multiple activities with the same difficulty. There is an absolute order (0) in each itinerary.
The order satisfies that if two activities have difficulties D*i* and D*j* respectively with D*i* < D*j*, then
O*i* < O*j*, where O is the position of the activity in the order. There cannot be repeated activities in the same itinerary.

The activities are characterized by their name and an identifier. An activity can include several exercises.
To evaluate the result, the activities also have the solution of every exercise and an estimation of the total time
that should be spent to complete it.

**The application works in the following way:**

(Assuming that there is a set of activities in the itinerary, with at least one activity for each level of difficulty.)

After logging into the application, the student asks for an activity to the API.

- If the student has not started the itinerary, the API will return the first activity in the itinerary.
- If the student has completed the itinerary, that is to say, the student has solved the last activity of the
  itinerary correctly, the API will return a response saying that there are no more available activities, since the itinerary
  has already been completed.

Once the student gets the next activity to do, he/she will do all the required exercises through the application.
When done, the application will send the result to the API, specifying the following parameters:

- The identifier of the activity obtained in the previous step.
- The identifier of the student who has solved the activity.
- A string with the ordered results of the exercises done. For example, for an activity with 4 exercises:
  `"1_34_-5_'none'"`.
- The time it took the student to do the activity.

When the API receives the request to complete the activity, it will process it and it will return the proper result to indicate
if it has been registered correctly or not. To consider that an activity is correctly completed, it is necessary to
give an answer for every exercise in the activity.

When an activity is completed, the API will process which is the next activity that should be returned to the student
when he/she asks for the next activity. To do it, the last activity result, and the policy exposed in the next table will be used:

| Score of the last activity | Action          |
| :------------------------- | :-------------- |
| score < 75%                | Repeat activity |
| 75% <= score               | Next activity   |

If the activity done is the last activity of the itinerary and it is correctly completed, no computation will be done.

The score is computed comparing the given answer with the solution of the activity.

_Bonus:_ Additionaly, and to provide the adaptative factor to the itinerary, the API will do an additional computation to
check if the student can pass to the next level of difficulty. This second computation will take into account the time spent
on the activity and the score.

| Condition                                           | Action                                                                                  |
| :-------------------------------------------------- | :-------------------------------------------------------------------------------------- |
| score > 75% & time < 50% of the estimated time      | Pass to the next level of difficulty                                                    |
| score > 75% & NOT(time < 50% of the estimated time) | Mantain level of difficulty                                                             |
| score < 20% & previous level jump                   | Move back one level (and go back to the next activity from the last completed activity) |

## Example:

### Itinerary:

| Activity    | Identifier | Position (order) | Difficulty | Time | Solution             |
| ----------- | ---------- | ---------------- | ---------- | ---- | -------------------- |
| Activity 1  | A1         | 1                | 1          | 120  | "1_0_2"              |
| Activity 2  | A2         | 2                | 1          | 60   | "-2_40_56"           |
| Activity 3  | A3         | 3                | 1          | 120  | "1_0"                |
| Activity 4  | A4         | 4                | 1          | 180  | "1*0_2*-5_9"         |
| Activity 5  | A5         | 5                | 2          | 120  | "1_0_2"              |
| Activity 6  | A6         | 6                | 2          | 120  | "1_0_2"              |
| Activity 7  | A7         | 7                | 3          | 120  | "1*-1*'Yes'\_34\_-6" |
| Activity 8  | A8         | 8                | 3          | 120  | "1_2"                |
| Activity 9  | A9         | 9                | 4          | 120  | "1_0_2"              |
| Activity 10 | A10        | 10               | 5          | 120  | "1_0_2"              |
| Activity 11 | A11        | 11               | 6          | 120  | "1_0_2"              |
| Activity 12 | A12        | 12               | 7          | 120  | "1_0_2"              |
| Activity 13 | A13        | 13               | 8          | 120  | "1_0_2"              |
| Activity 14 | A14        | 14               | 9          | 120  | "1_0_2"              |
| Activity 15 | A15        | 15               | 10         | 120  | "1_0_2"              |

### Example sequence:

0. Next activity: A1
1. A1 + 90s + "1_0_2" -> Score= 100% -> Next activity: A2
2. A2 + 15s + "-2_40_56" -> Score= 100% -> Next activity: A5
3. A5 + 180s + "0_2_1" -> Score= 0% -> Next activity: A3
4. A3 + 100s + "1_1" -> Score= 50% -> Next activity: A3
5. A3 + 80s + "1_0" -> Score= 100% ->Next activity: A4
6. A4 + 100s + "1*0_2*-4_9" -> Score= 80% -> Next activity: A5
7. ...
8. ...
9. A15 + 145s + "1_0_2" -> Score= 100% -> Next activity: ~

## It is asked to:

1. The main goal is to implement the adaptive itinerary progress, including level jumping.
2. Analyze the current solution to identify code smells and/or bad practices.
3. Refactor whatever you consider that can improve the application's design, quality and resilience. You can assume all proposed changes will not break BC.
4. Propose (and apply) any technique or tool that can help to improve developer's experience.

## Evaluation Criteria

The challenge is assessed across the following dimensions:

| Dimension | What we evaluate |
|-----------|------------------|
| Architecture & DDD | Clear boundaries between layers, proper use of domain concepts, and maintainable design decisions |
| Code Quality & SOLID | Readability, cohesion, duplication control, error handling quality, and refactoring depth |
| Security | Authentication/authorization robustness, safe data access patterns, and risk mitigation mindset |
| Testing Strategy | Appropriate test pyramid, test quality, meaningful coverage of edge cases, and confidence in changes |
| API, Observability & Concurrency | API consistency, operational visibility (logging), and data integrity under concurrent scenarios |
| Developer Experience & DevOps | Efficient Docker builds (layer cache usage), reproducible environments, and practical local workflow; |

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
```

### Test Credentials

| Student     | Email                   | Password    |
|-------------|-------------------------|-------------|
| Alice Smith | alice@innovamat.com     | password123 |
| Bob Jones   | bob@innovamat.com       | password123 |

- **Alice**: has not started the itinerary yet
- **Bob**: has completed activity A1 and is currently on A2

### API Endpoints

| Method | URL                             | Description                      |
|--------|---------------------------------|----------------------------------|
| POST   | `/api/login`                    | Authenticate student, get JWT    |
| GET    | `/api/getNextActivity`          | Get the next activity for student|
| POST   | `/api/completeActivity`         | Submit answers for an activity   |
| GET    | `/api/students`                 | List all students                |
| GET    | `/api/itineraries`              | List all itineraries             |

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
# All tests
make test

# Unit tests only
make test-unit

# Integration tests
make test-integration

# Acceptance tests (Behat)
make test-behat
```

### Useful Commands

```bash
make shell          # Open shell in PHP container
make seed           # Reload fixtures (reset student progress)
make migrate        # Run pending migrations
make migration-diff # Generate migration from entity changes
make logs           # Tail container logs
```

### Debugging with Xdebug (macOS)

Xdebug is preconfigured in the PHP Docker container and exposed on port `9003`.

1. Rebuild and restart containers so the extension is installed:

```bash
docker compose build php
docker compose up -d
```

2. In Cursor/VS Code, install the `PHP Debug` extension (`xdebug.php-debug`).
3. Use the included launch config and start **Listen for Xdebug (Docker)**.
4. Set breakpoints in files under `backend/src`.
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
backend/
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

frontend/
├── src/
│   ├── api/
│   ├── components/
│   ├── pages/
│   └── types/
```
