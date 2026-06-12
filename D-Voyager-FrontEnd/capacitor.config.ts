import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.dvoyager.app',
  appName: 'D-Voyager',
  webDir: 'www',
  plugins: {
    StatusBar: {
      overlaysWebView: true,
      backgroundColor: '#ffffff00',
      style: 'LIGHT'
    }
  }
};

export default config;
