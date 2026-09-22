# Domain and architecture context

The system is made of two applications, each with its own codebase and database:

- **Student platform** (`apps/student-platform`): the application students use to do the activities of an itinerary.
- **Reporting** (`apps/reporting`): the application that keeps the statistics of the activities, fed by the student platform.

## Student platform

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

## Reporting

Reporting exposes the **activity report**: the global statistics of each activity, computed over all the attempts from all students:

- How many times it has been attempted
- How many of those attempts were passed
- The average score
- The average time spent
