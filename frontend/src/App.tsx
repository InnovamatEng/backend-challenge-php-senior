import { BrowserRouter, Route, Routes, Navigate } from 'react-router-dom';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import LoginPage from './pages/LoginPage';
import ItineraryPage from './pages/ItineraryPage';
import CompletedPage from './pages/CompletedPage';

const theme = createTheme({
  palette: {
    primary: {
      main: '#e31e46',
    },
    secondary: {
      main: '#0a2240',
    },
  },
  typography: {
    fontFamily: '"Inter", "Roboto", "Helvetica", "Arial", sans-serif',
  },
});

function RequireAuth({ children }: { children: React.ReactNode }) {
  const token = localStorage.getItem('jwt_token');
  if (!token) return <Navigate to="/" replace />;
  return <>{children}</>;
}

export default function App() {
  return (
    <ThemeProvider theme={theme}>
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<LoginPage />} />
          <Route
            path="/itinerary"
            element={
              <RequireAuth>
                <ItineraryPage />
              </RequireAuth>
            }
          />
          <Route
            path="/completed"
            element={
              <RequireAuth>
                <CompletedPage />
              </RequireAuth>
            }
          />
        </Routes>
      </BrowserRouter>
    </ThemeProvider>
  );
}
