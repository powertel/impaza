export const colors = {
  // Brand Colors
  primary: '#0a7ea4', // Main brand color
  primaryTint: '#0a7ea4',
  
  // Status Colors
  success: '#22C55E', // Resolved
  warning: '#F59E0B', // Pending
  danger: '#EF4444', // Critical/Escalated
  info: '#3B82F6', // Info/Assigned
  assigned: '#3B82F6',
  pending: '#F59E0B',
  rectified: '#22C55E',
  resolved: '#10B981',
  escalated: '#EF4444',
  referred: '#8B5CF6',
  
  // Neutral Palette (Dark Mode Default)
  background: '#151718',
  surface: '#1e2022',
  card: '#1e2022',
  text: '#ECEDEE',
  secondaryText: '#9BA1A6',
  muted: '#687076',
  border: '#334155',
  
  // Legacy mappings for compatibility
  white: '#ECEDEE', // Map white to light text in dark mode context
  black: '#000',
  gray: '#9BA1A6',
  lightGray: '#334155',
  veryLightGray: '#1e2022',
  dark: '#ECEDEE',
  light: '#1e2022',
  
  // Input
  input: '#1e2022',
  inputDisabled: '#2C2E33',
};

export const fontSizes = {
  xs: 12,
  sm: 14,
  md: 16,
  lg: 18,
  xl: 20,
  xxl: 24,
  xxxl: 30,
};

export const spacing = {
  xs: 4,
  sm: 8,
  md: 12,
  lg: 16,
  xl: 20,
  xxl: 24,
};

export const theme = {
  colors,
  fontSizes,
  spacing,
  borderRadius: {
    sm: 6,
    md: 8,
    lg: 12,
    xl: 16,
    circle: 9999,
  },
  shadows: {
    sm: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 1 },
      shadowOpacity: 0.18,
      shadowRadius: 1.0,
      elevation: 1,
    },
    md: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 2 },
      shadowOpacity: 0.25,
      shadowRadius: 3.84,
      elevation: 3,
    },
  },
};