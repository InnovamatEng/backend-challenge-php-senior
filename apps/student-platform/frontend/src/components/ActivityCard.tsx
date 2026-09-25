import { useState } from 'react';
import {
  Box,
  Button,
  Card,
  CardContent,
  CircularProgress,
  TextField,
  Typography,
} from '@mui/material';
import { Activity } from '../types';

interface Props {
  activity: Activity;
  onSubmit: (answers: string[]) => void;
  submitting: boolean;
}

export default function ActivityCard({ activity, onSubmit, submitting }: Props) {
  const [raw, setRaw] = useState('');

  const handleSubmit = () => {
    onSubmit([raw]);
  };

  return (
    <Card elevation={2} sx={{ borderRadius: 3 }}>
      <CardContent sx={{ p: 4 }}>
        <Typography variant="h5" fontWeight={700} gutterBottom color="secondary.main">
          {activity.name}
        </Typography>
        <Typography variant="body2" color="text.secondary" sx={{ mb: 3 }}>
          {activity.identifier} · {activity.exercises_count} exercise{activity.exercises_count > 1 ? 's' : ''}
        </Typography>

        <TextField
          fullWidth
          label="Answers"
          value={raw}
          onChange={(e) => setRaw(e.target.value)}
          onKeyDown={(e) => e.key === 'Enter' && raw.trim() && handleSubmit()}
          placeholder={activity.exercises_count > 1 ? `e.g. 1_0_2 (${activity.exercises_count} values separated by _)` : 'e.g. 42'}
          helperText={activity.exercises_count > 1 ? `Separate each exercise answer with _` : undefined}
          disabled={submitting}
          autoFocus
        />

        <Button
          fullWidth
          variant="contained"
          size="large"
          sx={{ mt: 4, borderRadius: 2, py: 1.5, fontWeight: 700 }}
          disabled={!raw.trim() || submitting}
          onClick={handleSubmit}
        >
          {submitting ? <CircularProgress size={24} color="inherit" /> : 'Submit Answers'}
        </Button>
      </CardContent>
    </Card>
  );
}
