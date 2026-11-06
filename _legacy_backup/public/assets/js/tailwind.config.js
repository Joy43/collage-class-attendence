// Tailwind Play CDN configuration. Must load AFTER the CDN <script>.
// Colours resolve to the CSS variables in assets/css/app.css, so the
// light/dark switch is just `html.dark` — no `dark:` variants needed.
const token = (name) => `rgb(var(--c-${name}) / <alpha-value>)`;

tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
      },
      colors: {
        canvas:  token('canvas'),
        surface: token('surface'),
        subtle:  token('subtle'),
        line:    { DEFAULT: token('line'), hover: token('line-hover') },
        fg:      token('fg'),
        muted:   token('muted'),
        faint:   token('faint'),
        primary: { DEFAULT: token('primary'), hover: token('primary-hover'), soft: token('primary-soft') },
        present: { DEFAULT: token('present'), soft: token('present-soft') },
        absent:  { DEFAULT: token('absent'),  soft: token('absent-soft') },
        late:    { DEFAULT: token('late'),    soft: token('late-soft') },
        leave:   { DEFAULT: token('leave'),   soft: token('leave-soft') },
        holiday: { DEFAULT: token('holiday'), soft: token('holiday-soft') },
      },
      keyframes: {
        'fade-in':    { from: { opacity: 0 }, to: { opacity: 1 } },
        'slide-up':   { from: { transform: 'translateY(20px)', opacity: 0 }, to: { transform: 'translateY(0)', opacity: 1 } },
        'slide-toast':{ from: { transform: 'translateX(100%)', opacity: 0 }, to: { transform: 'translateX(0)', opacity: 1 } },
      },
      animation: {
        'fade-in':     'fade-in .2s ease',
        'slide-up':    'slide-up .22s ease',
        'slide-toast': 'slide-toast .3s ease',
      },
    },
  },
};
