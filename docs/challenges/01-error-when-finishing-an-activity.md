# Finishing an activity fails and students lose their progress

- **Challenge Type:** System Design Interview
- **Duration:** ~1h 15
- **Use of AI:** Not allowed

## What has been reported

Yesterday afternoon a teacher reported that students could not finish their
activities. When a student pressed "Submit", the page waited a few seconds and
then showed:

> Something went wrong. Please try again.

When the student retried, or went back to the itinerary, the same activity
was offered again as if it had never been submitted, so they had to redo it.

Nothing changed on the school side: same devices, same network, same
itinerary as the previous weeks.

## Also reported

That same afternoon, teachers opening the activity reports page saw this
error instead of the report:

> Something went wrong. Please try again.

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
