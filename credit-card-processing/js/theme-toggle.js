function toggleTheme() {
  const styleSheet = document.getElementById('theme-style');
  if (styleSheet.href.includes('light')) {
    styleSheet.href = 'css/dark.css';
  } else {
    styleSheet.href = 'css/style.css';
  }
}
