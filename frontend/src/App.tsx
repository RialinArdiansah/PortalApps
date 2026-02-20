import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { Provider } from 'react-redux';
import { store } from '@/store';
import { ThemeProvider } from '@/context/ThemeContext';
import { ErrorBoundary } from '@/components/common/ErrorBoundary';
import { ProtectedRoute } from '@/components/common/ProtectedRoute';
import { AppLayout } from '@/components/Layout/AppLayout';
import { LoginPage } from '@/pages/LoginPage';
import { DashboardPage } from '@/pages/DashboardPage';
import { UsersPage } from '@/pages/UsersPage';
import CertificatesPage from '@/pages/CertificatesPage';
import { MarketingPage } from '@/pages/MarketingPage';
import { SubmissionsPage } from '@/pages/SubmissionsPage';
import { SubmissionFormPage } from '@/pages/SubmissionFormPage';
import { TransactionsPage } from '@/pages/TransactionsPage';
import FeeP3SMPage from '@/pages/FeeP3SMPage';
import { SettingsPage } from '@/pages/SettingsPage';

function App() {
    return (
        <Provider store={store}>
            <ThemeProvider>
                <ErrorBoundary>
                    <BrowserRouter>
                        <Routes>
                            <Route path="/login" element={<LoginPage />} />
                            <Route path="/" element={<ProtectedRoute><AppLayout /></ProtectedRoute>}>
                                <Route index element={<DashboardPage />} />
                                <Route path="users" element={<ProtectedRoute allowedRoles={['Super admin']}><UsersPage /></ProtectedRoute>} />
                                <Route path="certificates" element={<ProtectedRoute allowedRoles={['Super admin']}><CertificatesPage /></ProtectedRoute>} />
                                <Route path="marketing" element={<ProtectedRoute allowedRoles={['Super admin', 'admin', 'manager']}><MarketingPage /></ProtectedRoute>} />
                                <Route path="submissions" element={<SubmissionsPage />} />
                                <Route path="submissions/new" element={<SubmissionFormPage />} />
                                <Route path="finance/transactions" element={<TransactionsPage />} />
                                <Route path="finance/fee-p3sm" element={<FeeP3SMPage />} />
                                <Route path="transactions" element={<Navigate to="/finance/transactions" replace />} />
                                <Route path="settings" element={<ProtectedRoute allowedRoles={['Super admin', 'admin']}><SettingsPage /></ProtectedRoute>} />
                            </Route>
                            <Route path="*" element={<Navigate to="/" replace />} />
                        </Routes>
                    </BrowserRouter>
                </ErrorBoundary>
            </ThemeProvider>
        </Provider>
    );
}

export default App;
