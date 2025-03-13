const { app, BrowserWindow } = require('electron');
const path = require('path');

let win;

function createWindow() {
  win = new BrowserWindow({
    width: 800,
    height: 600,
    webPreferences: {
      // Disable nodeIntegration for security purposes
      nodeIntegration: false,
      contextIsolation: true, // This is more secure
      preload: path.join(__dirname, 'preload.js')  // Use preload.js if needed
    },
  });

  // Load the specified URL (or use loadFile for local assets)
  win.loadURL('https://feng37.fwh.is/Eroll3Feng37/');
}

app.whenReady().then(createWindow);

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

app.on('activate', () => {
  if (BrowserWindow.getAllWindows().length === 0) {
    createWindow();
  }
});
