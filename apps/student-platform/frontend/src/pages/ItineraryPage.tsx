import { useState, useEffect, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  Alert,
  Box,
  Button,
  Chip,
  CircularProgress,
  Container,
  Divider,
  LinearProgress,
  Paper,
  Typography,
} from '@mui/material';
import LogoutIcon from '@mui/icons-material/Logout';
import apiClient from '../api/client';
import { Activity, CompletionResult, Itinerary, Student } from '../types';
import ActivityCard from '../components/ActivityCard';
import ScoreResult from '../components/ScoreResult';

export default function ItineraryPage() {
  const navigate = useNavigate();
  const student: Student = JSON.parse(localStorage.getItem('current_student') || '{}');
  const itinerary: Itinerary = JSON.parse(localStorage.getItem('current_itinerary') || '{}');

  const [activity, setActivity] = useState<Activity | null>(null);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [lastResult, setLastResult] = useState<CompletionResult | null>(null);
  const [elapsedSeconds, setElapsedSeconds] = useState(0);
  const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);

  const fetchNextActivity = async () => {
    setLoading(true);
    setError(null);
    setLastResult(null);
    try {
      const response = await apiClient.get(
        `/getNextActivity?itinerary=${itinerary.slug}&student_id=${student.id}`
      );

      if (response.data.completed) {
        navigate('/completed');
        return;
      }

      setActivity(response.data);
      setElapsedSeconds(0);
      if (timerRef.current) clearInterval(timerRef.current);
      timerRef.current = setInterval(() => setElapsedSeconds(s => s + 1), 1000);
    } catch (e: unknown) {
      const msg = (e as { response?: { data?: { error?: string } } })?.response?.data?.error;
      setError(msg ?? 'Error loading activity');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (!student.id || !itinerary.slug) {
      navigate('/');
      return;
    }
    fetchNextActivity();
    return () => {
      if (timerRef.current) clearInterval(timerRef.current);
    };
  }, []);

  const handleSubmit = async (answers: string[]) => {
    if (!activity) return;
    if (timerRef.current) clearInterval(timerRef.current);
    setSubmitting(true);
    setError(null);

    try {
      const response = await apiClient.post('/completeActivity', {
        activity_id: activity.identifier,
        student_id: student.id,
        answers: answers.join('_'),
        time_spent: Math.ceil(elapsedSeconds / 60),
      });

      setLastResult(response.data);

      if (response.data.itinerary_completed) {
        navigate('/completed');
      }
    } catch {
      setError('Something went wrong. Please try again.');
    } finally {
      setSubmitting(false);
    }
  };

  const handleContinue = () => {
    fetchNextActivity();
  };

  const handleLogout = () => {
    localStorage.clear();
    navigate('/');
  };

  const formatTime = (seconds: number) => {
    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
  };

  const difficultyColor = (d: number) => {
    if (d <= 3) return 'success';
    if (d <= 6) return 'warning';
    return 'error';
  };

  return (
    <Box sx={{ minHeight: '100vh', bgcolor: '#f5f7fa' }}>
      <Box sx={{ bgcolor: 'secondary.main', color: 'white', py: 1.5, px: 3, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <Typography fontWeight={700} fontSize={18}>
          Innovamat — {itinerary.name}
        </Typography>
        <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
          <Typography variant="body2">{student.name}</Typography>
          <Button size="small" color="inherit" startIcon={<LogoutIcon />} onClick={handleLogout}>
            Logout
          </Button>
        </Box>
      </Box>

      <Container maxWidth="md" sx={{ py: 4 }}>
        {loading && (
          <Box sx={{ display: 'flex', justifyContent: 'center', mt: 8 }}>
            <CircularProgress />
          </Box>
        )}

        {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

        {!loading && activity && !lastResult && (
          <>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
              <Box sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
                <Chip
                  label={`Difficulty: ${activity.difficulty}/10`}
                  color={difficultyColor(activity.difficulty) as 'success' | 'warning' | 'error'}
                  size="small"
                />
                <Chip label={`Est. ${activity.estimated_time}s`} variant="outlined" size="small" />
              </Box>
              <Paper elevation={0} sx={{ px: 2, py: 0.5, bgcolor: elapsedSeconds > activity.estimated_time ? '#fff3e0' : '#e8f5e9', borderRadius: 2 }}>
                <Typography fontWeight={700} color={elapsedSeconds > activity.estimated_time ? 'warning.dark' : 'success.dark'}>
                  ⏱ {formatTime(elapsedSeconds)}
                </Typography>
              </Paper>
            </Box>

            {activity.estimated_time > 0 && (
              <LinearProgress
                variant="determinate"
                value={Math.min((elapsedSeconds / activity.estimated_time) * 100, 100)}
                sx={{ mb: 3, height: 6, borderRadius: 3 }}
                color={elapsedSeconds > activity.estimated_time ? 'warning' : 'primary'}
              />
            )}

            <ActivityCard
              activity={activity}
              onSubmit={handleSubmit}
              submitting={submitting}
            />
          </>
        )}

        {lastResult && activity && (
          <>
            <ScoreResult result={lastResult} activityName={activity.name} />
            <Divider sx={{ my: 3 }} />
            <Box sx={{ display: 'flex', justifyContent: 'center' }}>
              <Button
                variant="contained"
                size="large"
                onClick={handleContinue}
                sx={{ borderRadius: 2, px: 5, fontWeight: 700 }}
              >
                {lastResult.passed ? 'Next Activity →' : 'Try Again'}
              </Button>
            </Box>
          </>
        )}
      </Container>
    </Box>
  );
}
