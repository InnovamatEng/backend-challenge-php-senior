# Reporting service

Receives every activity attempt from the student platform and serves the
reports teachers use to follow their class: for each student, which activities
they have completed, how many attempts each one took and with what score.

Raw attempts are not retained. Each attempt is folded on arrival into the
per-activity statistics that feed the activity dashboard, which keeps the
storage small and the report queries cheap.

| Method | URL                  | Description                                               |
| ------ | -------------------- | --------------------------------------------------------- |
| GET    | `/health`            | Liveness check                                            |
| POST   | `/attempts`          | Register an activity attempt, returns the activity stats  |
| GET    | `/reports/activities`| Statistics of every activity                              |

The service has its own database (`reporting_mysql`). Inside the Docker network
it is reachable at `http://reporting_nginx`, and it is published on
`http://localhost:8081` for manual testing:

```bash
curl -X POST http://localhost:8081/attempts \
  -H "Content-Type: application/json" \
  -d '{"student_id":1,"activity_id":"A1","itinerary":"additions","score":1.0,"passed":true,"time_spent":90,"completed_at":"2026-09-17T10:00:00+00:00"}'

curl http://localhost:8081/reports/activities
```

From this directory (or with `make -C apps/reporting <target>` from the repository root):

```bash
make help    # List all targets
make setup   # Install dependencies, create databases, run migrations
make test    # Run the test suite
make shell   # Open a shell in the container
make logs    # Tail the logs
```
