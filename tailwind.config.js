export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        // Text colors
        'text': {
          DEFAULT: '#1f2937', // gray-800
          'muted': '#6b7280', // gray-500
          'light': '#9ca3af', // gray-400
        },
        'dark-text': {
          DEFAULT: '#f3f4f6', // gray-100
          'muted': '#9ca3af', // gray-400
          'light': '#d1d5db', // gray-300
        },
        // Background/Surface colors
        'surface': {
          'hover': '#f3f4f6', // gray-100
        },
        // Accent colors
        'accent': {
          DEFAULT: '#3b82f6', // blue-500
          'hover': '#2563eb', // blue-600
        },
        // Danger/error colors
        'danger': '#ef4444', // red-500
      },
    },
  },
  plugins: [],
}