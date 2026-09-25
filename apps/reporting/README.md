# Reporting service

Receives the activity attempts from the student platform and serves the
activity report: the statistics of each activity, computed over the attempts
of all students.

| Method | URL                  | Description                                               |
| ------ | -------------------- | --------------------------------------------------------- |
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
