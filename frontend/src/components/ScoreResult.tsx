import { Box, Chip, LinearProgress, Paper, Typography } from '@mui/material';
import CheckCircleIcon from '@mui/icons-material/CheckCircle';
import CancelIcon from '@mui/icons-material/Cancel';
import { CompletionResult } from '../types';

interface Props {
  result: CompletionResult;
  activityName: string;
}

export default function ScoreResult({ result, activityName }: Props) {
  const scorePercent = Math.round(result.score * 100);
  const passed = result.passed;

  return (
    <Paper
      elevation={0}
      sx={{
        p: 4,
        borderRadius: 3,
        border: `2px solid ${passed ? '#4caf50' : '#f44336'}`,
        bgcolor: passed ? '#f1f8e9' : '#fce4ec',
        textAlign: 'center',
      }}
    >
      <Box sx={{ mb: 2 }}>
        {passed ? (
          <CheckCircleIcon sx={{ fontSize: 56, color: 'success.main' }} />
        ) : (
          <CancelIcon sx={{ fontSize: 56, color: 'error.main' }} />
        )}
      </Box>

      <Typography variant="h4" fontWeight={700} color={passed ? 'success.dark' : 'error.dark'} gutterBottom>
        {scorePercent}%
      </Typography>

      <LinearProgress
        variant="determinate"
        value={scorePercent}
        color={passed ? 'success' : 'error'}
        sx={{ height: 10, borderRadius: 5, mb: 2, mx: 4 }}
      />

      <Chip
        label={passed ? '✓ Passed — Moving on!' : '✗ Not passed — Try again'}
        color={passed ? 'success' : 'error'}
        sx={{ fontWeight: 700, mb: 2 }}
      />

      <Typography variant="body2" color="text.secondary">
        {activityName}
      </Typography>

      {result.next_activity && passed && (
        <Typography variant="body2" color="text.secondary" sx={{ mt: 1 }}>
          Next: <strong>{result.next_activity.name}</strong> (Difficulty {result.next_activity.difficulty}/10)
        </Typography>
      )}
    </Paper>
  );
}
