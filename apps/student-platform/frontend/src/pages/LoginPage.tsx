import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  Box,
  Button,
  Card,
  CardContent,
  CircularProgress,
  FormControl,
  InputLabel,
  MenuItem,
  Select,
  TextField,
  Typography,
  Alert,
} from '@mui/material';
import SchoolIcon from '@mui/icons-material/School';
import apiClient from '../api/client';
import { Student, Itinerary } from '../types';

export default function LoginPage() {
  const navigate = useNavigate();
  const [students, setStudents] = useState<Student[]>([]);
  const [itineraries, setItineraries] = useState<Itinerary[]>([]);
  const [selectedStudent, setSelectedStudent] = useState<Student | null>(null);
  const [selectedItinerary, setSelectedItinerary] = useState<Itinerary | null>(null);
  const [password, setPassword] = useState('password123');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    // A previous session's token would be sent along and rejected by the API
    localStorage.removeItem('jwt_token');
    localStorage.removeItem('current_student');
    localStorage.removeItem('current_itinerary');

    Promise.all([
      apiClient.get('/students'),
      apiClient.get('/itineraries'),
    ]).then(([studentsRes, itinerariesRes]) => {
      setStudents(studentsRes.data);
      setItineraries(itinerariesRes.data);
    }).catch(() => {
      setError('Could not load data. Make sure the backend is running.');
    });
  }, []);

  const handleLogin = async () => {
    if (!selectedStudent || !selectedItinerary) return;
    setLoading(true);
    setError(null);

    try {
      const response = await apiClient.post('/login', {
        email: selectedStudent.email,
        password,
      });

      localStorage.setItem('jwt_token', response.data.token);
      localStorage.setItem('current_student', JSON.stringify(selectedStudent));
      localStorage.setItem('current_itinerary', JSON.stringify(selectedItinerary));
      navigate('/itinerary');
    } catch {
      setError('Login failed. Check credentials.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Box
      sx={{
        minHeight: '100vh',
        background: 'linear-gradient(135deg, #0a2240 0%, #e31e46 100%)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        p: 2,
      }}
    >
      <Card sx={{ maxWidth: 440, width: '100%', borderRadius: 3, boxShadow: 8 }}>
        <CardContent sx={{ p: 4 }}>
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 1.5, mb: 3 }}>
            <SchoolIcon sx={{ fontSize: 36, color: 'primary.main' }} />
            <Typography variant="h5" fontWeight={700} color="secondary.main">
              Innovamat Learning
            </Typography>
          </Box>

          <Typography variant="body2" color="text.secondary" mb={3}>
            Select a student and itinerary to begin the session.
          </Typography>

          {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

          <FormControl fullWidth sx={{ mb: 2 }}>
            <InputLabel>Student</InputLabel>
            <Select
              value={selectedStudent?.id ?? ''}
              label="Student"
              onChange={(e) => {
                const s = students.find(s => s.id === e.target.value);
                setSelectedStudent(s ?? null);
              }}
            >
              {students.map((s) => (
                <MenuItem key={s.id} value={s.id}>
                  {s.name} ({s.email})
                </MenuItem>
              ))}
            </Select>
          </FormControl>

          <FormControl fullWidth sx={{ mb: 2 }}>
            <InputLabel>Itinerary</InputLabel>
            <Select
              value={selectedItinerary?.id ?? ''}
              label="Itinerary"
              onChange={(e) => {
                const i = itineraries.find(i => i.id === e.target.value);
                setSelectedItinerary(i ?? null);
              }}
            >
              {itineraries.map((i) => (
                <MenuItem key={i.id} value={i.id}>
                  {i.name}
                </MenuItem>
              ))}
            </Select>
          </FormControl>

          <TextField
            fullWidth
            label="Password"
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            sx={{ mb: 3 }}
          />

          <Button
            fullWidth
            variant="contained"
            size="large"
            disabled={!selectedStudent || !selectedItinerary || loading}
            onClick={handleLogin}
            sx={{ borderRadius: 2, py: 1.5, fontWeight: 700 }}
          >
            {loading ? <CircularProgress size={24} color="inherit" /> : 'Start Session'}
          </Button>

          <Typography variant="caption" color="text.secondary" sx={{ mt: 2, display: 'block', textAlign: 'center' }}>
            Default password: password123
          </Typography>
        </CardContent>
      </Card>
    </Box>
  );
}
