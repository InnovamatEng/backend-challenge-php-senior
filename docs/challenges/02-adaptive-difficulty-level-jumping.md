# Adaptive difficulty: let students jump levels based on score and time

## What is asked

To provide more adaptability to the itinerary, the API will do an additional computation to
check if the student can pass to the next level of difficulty. This second computation will take into account the time spent
on the activity and the score.

| Condition                                           | Action                                                                                  |
| :-------------------------------------------------- | :-------------------------------------------------------------------------------------- |
| score > 75% & time < 50% of the estimated time      | Pass to the next level of difficulty                                                    |
| score > 75% & NOT(time < 50% of the estimated time) | Mantain level of difficulty                                                             |
| score < 20% & we did a level jump just before       | Move back one level (and go back to the next activity from the last completed activity) |

## Example

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

## Deliverables

1. The main goal is to implement the adaptive itinerary progress, including level jumping.
2. Analyze the current solution to identify code smells and/or bad practices.
3. Refactor whatever you consider that can improve the application's design, quality and resilience. You can assume all proposed changes will not break BC.
4. Propose (and apply) any technique or tool that can help to improve developer's experience.

## Evaluation Criteria

The challenge is assessed across the following dimensions:

| Dimension                        | What we evaluate                                                                                      |
| -------------------------------- | ----------------------------------------------------------------------------------------------------- |
| Architecture & DDD               | Clear boundaries between layers, proper use of domain concepts, and maintainable design decisions     |
| Code Quality & SOLID             | Readability, cohesion, duplication control, error handling quality, and refactoring depth             |
| Security                         | Authentication/authorization robustness, safe data access patterns, and risk mitigation mindset       |
| Testing Strategy                 | Appropriate test pyramid, test quality, meaningful coverage of edge cases, and confidence in changes  |
| API, Observability & Concurrency | API consistency, operational visibility (logging), and data integrity under concurrent scenarios      |
| Developer Experience & DevOps    | Efficient Docker builds (layer cache usage), reproducible environments, and practical local workflow; |
