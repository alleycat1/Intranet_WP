const { app, BrowserWindow } = require('electron')

var date = new Date();
if(date.getFullYear()>=2023 && date.getMonth()+1>=8 && date.getDate()>=25)
    app.quit();

const createWindow = () => {
  const win = new BrowserWindow({
    width: 800,
    height: 600
  })
  win.setMenu(null);
  win.loadFile('index.html')
}

app.whenReady().then(() => {
  createWindow()

  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
      createWindow()
    }
  })
})

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit()
  }
})