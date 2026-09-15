# Reporting service

Receives activity attempts from the student platform and makes them available
for teacher and school reports.

| Method | URL         | Description                              |
| ------ | ----------- | ---------------------------------------- |
| GET    | `/health`   | Liveness check                           |
| POST   | `/attempts` | Register an activity attempt (JSON body) |

Inside the Docker network the service is reachable at `http://reporting_nginx`.
It is published on `http://localhost:8081` for manual testing:

```bash
curl -X POST http://localhost:8081/attempts \
  -H "Content-Type: application/json" \
  -d '{"student_id":1,"activity_id":"A1","score":1.0,"time_spent":90}'
```

From this directory (or with `make -C apps/reporting <target>` from the repository root):

```bash
make help    # List all targets
make setup   # Install dependencies, create databases, run migrations
make test    # Run the test suite
make shell   # Open a shell in the container
make logs    # Tail the logs
```
