import { useEffect, useMemo, useState } from 'react';
import { Link as RouterLink } from 'react-router-dom';
import {
  Alert,
  Box,
  Button,
  Chip,
  CircularProgress,
  Container,
  FormControl,
  InputLabel,
  MenuItem,
  Paper,
  Select,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Typography,
} from '@mui/material';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import RefreshIcon from '@mui/icons-material/Refresh';
import reportsClient from '../api/reportsClient';
import { ActivityStats } from '../types';

const ALL_ITINERARIES = 'all';

const formatPercent = (ratio: number) => `${Math.round(ratio * 100)}%`;

const formatDuration = (seconds: number) => {
  const m = Math.floor(seconds / 60);
  const s = seconds % 60;
  return m > 0 ? `${m}m ${s.toString().padStart(2, '0')}s` : `${s}s`;
};

const formatDate = (iso: string) =>
  new Date(iso).toLocaleString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });

const passRateColor = (rate: number): 'success' | 'warning' | 'error' => {
  if (rate >= 0.7) return 'success';
  if (rate >= 0.4) return 'warning';
  return 'error';
};

const compareActivityIds = (a: string, b: string) =>
  a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' });

export default function ReportsPage() {
  const [stats, setStats] = useState<ActivityStats[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [itinerary, setItinerary] = useState<string>(ALL_ITINERARIES);

  const fetchStats = async () => {
    setLoading(true);
    setError(null);
    try {
      const response = await reportsClient.get<ActivityStats[]>('/activities');
      setStats(response.data);
    } catch {
      setError('Could not load the activity reports. Make sure the reporting service is running.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchStats();
  }, []);

  const itineraries = useMemo(
    () => Array.from(new Set(stats.map((s) => s.itinerary))).sort(),
    [stats]
  );

  const rows = useMemo(() => {
    const filtered = itinerary === ALL_ITINERARIES
      ? stats
      : stats.filter((s) => s.itinerary === itinerary);
    return [...filtered].sort(
      (a, b) => a.itinerary.localeCompare(b.itinerary) || compareActivityIds(a.activity_id, b.activity_id)
    );
  }, [stats, itinerary]);

  const totals = useMemo(() => {
    const attempts = rows.reduce((sum, r) => sum + r.attempts_count, 0);
    const passed = rows.reduce((sum, r) => sum + r.passed_count, 0);
    return { activities: rows.length, attempts, passed };
  }, [rows]);

  return (
    <Box sx={{ minHeight: '100vh', bgcolor: '#f5f7fa' }}>
      <Box sx={{ bgcolor: 'secondary.main', color: 'white', py: 1.5, px: 3, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <Typography fontWeight={700} fontSize={18}>
          Innovamat — Activity reports
        </Typography>
        <Button component={RouterLink} to="/" size="small" color="inherit" startIcon={<ArrowBackIcon />}>
          Back to login
        </Button>
      </Box>

      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: 2, mb: 3 }}>
          <Box>
            <Typography variant="h5" fontWeight={700} color="secondary.main">
              Activity statistics
            </Typography>
            <Typography variant="body2" color="text.secondary">
              Aggregated results of every attempt recorded by the reporting service.
            </Typography>
          </Box>
          <Box sx={{ display: 'flex', gap: 2, alignItems: 'center' }}>
            <FormControl size="small" sx={{ minWidth: 180 }}>
              <InputLabel>Itinerary</InputLabel>
              <Select
                value={itinerary}
                label="Itinerary"
                onChange={(e) => setItinerary(e.target.value)}
              >
                <MenuItem value={ALL_ITINERARIES}>All itineraries</MenuItem>
                {itineraries.map((i) => (
                  <MenuItem key={i} value={i}>{i}</MenuItem>
                ))}
              </Select>
            </FormControl>
            <Button variant="outlined" startIcon={<RefreshIcon />} onClick={fetchStats} disabled={loading}>
              Refresh
            </Button>
          </Box>
        </Box>

        {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

        {loading && (
          <Box sx={{ display: 'flex', justifyContent: 'center', mt: 8 }}>
            <CircularProgress />
          </Box>
        )}

        {!loading && !error && (
          <>
            <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap', mb: 3 }}>
              <SummaryCard label="Activities" value={totals.activities} />
              <SummaryCard label="Attempts" value={totals.attempts} />
              <SummaryCard label="Passed" value={totals.passed} />
              <SummaryCard
                label="Pass rate"
                value={totals.attempts > 0 ? formatPercent(totals.passed / totals.attempts) : '—'}
              />
            </Box>

            <TableContainer component={Paper} sx={{ borderRadius: 2 }}>
              <Table size="small">
                <TableHead>
                  <TableRow sx={{ '& th': { fontWeight: 700, bgcolor: '#eef1f5' } }}>
                    <TableCell>Activity</TableCell>
                    <TableCell>Itinerary</TableCell>
                    <TableCell align="right">Attempts</TableCell>
                    <TableCell align="right">Passed</TableCell>
                    <TableCell align="right">Pass rate</TableCell>
                    <TableCell align="right">Avg. score</TableCell>
                    <TableCell align="right">Avg. time</TableCell>
                    <TableCell>Last attempt</TableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {rows.length === 0 && (
                    <TableRow>
                      <TableCell colSpan={8} align="center" sx={{ py: 6, color: 'text.secondary' }}>
                        No attempts recorded yet. Complete an activity in the student platform to see it here.
                      </TableCell>
                    </TableRow>
                  )}
                  {rows.map((row) => {
                    const passRate = row.attempts_count > 0 ? row.passed_count / row.attempts_count : 0;
                    return (
                      <TableRow key={`${row.itinerary}-${row.activity_id}`} hover>
                        <TableCell sx={{ fontWeight: 600 }}>{row.activity_id}</TableCell>
                        <TableCell>{row.itinerary}</TableCell>
                        <TableCell align="right">{row.attempts_count}</TableCell>
                        <TableCell align="right">{row.passed_count}</TableCell>
                        <TableCell align="right">
                          <Chip label={formatPercent(passRate)} color={passRateColor(passRate)} size="small" />
                        </TableCell>
                        <TableCell align="right">{formatPercent(row.average_score)}</TableCell>
                        <TableCell align="right">{formatDuration(row.average_time_spent)}</TableCell>
                        <TableCell>{formatDate(row.last_attempt_at)}</TableCell>
                      </TableRow>
                    );
                  })}
                </TableBody>
              </Table>
            </TableContainer>
          </>
        )}
      </Container>
    </Box>
  );
}

function SummaryCard({ label, value }: { label: string; value: number | string }) {
  return (
    <Paper elevation={0} sx={{ px: 3, py: 2, borderRadius: 2, minWidth: 140, border: '1px solid #e0e4ea' }}>
      <Typography variant="caption" color="text.secondary" sx={{ textTransform: 'uppercase', letterSpacing: 0.5 }}>
        {label}
      </Typography>
      <Typography variant="h5" fontWeight={700} color="secondary.main">
        {value}
      </Typography>
    </Paper>
  );
}
