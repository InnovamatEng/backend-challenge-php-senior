export interface Student {
  id: number;
  name: string;
  email: string;
}

export interface Itinerary {
  id: number;
  name: string;
  slug: string;
}

export interface Activity {
  id: number;
  identifier: string;
  name: string;
  difficulty: number;
  estimated_time: number;
  exercises_count: number;
}

export interface CompletionResult {
  score: number;
  passed: boolean;
  itinerary_completed: boolean;
  next_activity: Activity | null;
}

export interface ActivityStats {
  activity_id: string;
  itinerary: string;
  attempts_count: number;
  passed_count: number;
  average_score: number;
  average_time_spent: number;
  last_attempt_at: string;
}

export interface AuthState {
  token: string | null;
  student: Student | null;
}
