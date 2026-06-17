import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.dvoyager.app',
  appName: 'D-Voyager',
  webDir: 'www',
  plugins: {
    SplashScreen: {
      launchShowDuration: 3000,
      launchAutoHide: false,
      backgroundColor: "#ffd214",
      androidScaleType: "CENTER_CROP",
      showSpinner: false,
    },
  },
};

export default config;

