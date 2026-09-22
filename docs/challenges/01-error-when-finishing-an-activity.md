# Finishing an activity is slow and sometimes fails, and students have to redo it

- **Challenge Type:** System Design Interview
- **Duration:** ~1h 15
- **Use of AI:** Not allowed

## What has been reported

Since a few weeks, finishing an activity has become slow. After pressing
"Submit", students wait several seconds before seeing their result, and some
press the button twice because they think the first press did not work.
Getting the next activity is as fast as it has always been.

Sometimes it is worse than slow: the screen shows "something went wrong", and
when the student goes back the activity they just finished is offered to them
again, so they have to redo it.

It does not happen to everyone, and it is clearly worse in the afternoon. The
teacher says that on Tuesday between 15:00 and 15:20 eleven students in one
class got the error, and the rest of the class waited a long time on every
activity. Nothing changed on their side: same devices, same network, same
itinerary as the previous weeks.

## Steps the teacher followed

1. The student opens the itinerary and gets the next activity.
2. The student answers the exercises and presses "Submit".
3. The result takes several seconds to appear. Sometimes the error appears
   instead, and the student goes back to the itinerary.
4. In that case the same activity is offered again as if it had never been
   submitted.

## Suspicions

A colleague has seen in the monitoring system that the reporting service had
temporary failures during that day.

## Deliverables

Your mission is to:

- Find the problems that are causing this situation
- Propose an evolution of the system that fixes them
- Explain how you would make your solution happen

## About the format

This is a conversation at the whiteboard, where it is important that you
explain out loud the reasons behind your decisions. You can ask all the
questions you need, and we will also ask you questions or make comments when we
see fit.

## What we evaluate

There may be several valid answers, so what matters is that you explain why you
would go for the solution you propose.

We will evaluate:

- Debugging
- Your knowledge of distributed systems
- Scalability, maintainability, reliability and consistency of the system
- How you communicate
- Pragmatism
