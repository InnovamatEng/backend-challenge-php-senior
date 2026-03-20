import { useNavigate } from 'react-router-dom';
import { Box, Button, Typography, Paper } from '@mui/material';
import EmojiEventsIcon from '@mui/icons-material/EmojiEvents';

export default function CompletedPage() {
  const navigate = useNavigate();
  const student = JSON.parse(localStorage.getItem('current_student') || '{}');
  const itinerary = JSON.parse(localStorage.getItem('current_itinerary') || '{}');

  const handleRestart = () => {
    localStorage.removeItem('jwt_token');
    localStorage.removeItem('current_student');
    localStorage.removeItem('current_itinerary');
    navigate('/');
  };

  return (
    <Box
      sx={{
        minHeight: '100vh',
        background: 'linear-gradient(135deg, #0a2240 0%, #1a4a7a 100%)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        p: 3,
      }}
    >
      <Paper sx={{ maxWidth: 500, width: '100%', p: 5, borderRadius: 3, textAlign: 'center' }}>
        <EmojiEventsIcon sx={{ fontSize: 80, color: '#f9a825', mb: 2 }} />
        <Typography variant="h4" fontWeight={700} color="secondary.main" gutterBottom>
          Congratulations!
        </Typography>
        <Typography variant="h6" color="text.secondary" gutterBottom>
          {student.name}
        </Typography>
        <Typography variant="body1" color="text.secondary" sx={{ mb: 4 }}>
          You have successfully completed the{' '}
          <strong>{itinerary.name}</strong> itinerary!
        </Typography>
        <Button
          variant="contained"
          size="large"
          onClick={handleRestart}
          sx={{ borderRadius: 2, px: 5, fontWeight: 700 }}
        >
          Start New Session
        </Button>
      </Paper>
    </Box>
  );
}
