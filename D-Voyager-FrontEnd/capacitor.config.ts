import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.dvoyager.app',
  appName: 'D-Voyager',
  webDir: 'www',
  plugins: {
    SplashScreen: {
      launchShowDuration: 500,
      launchAutoHide: true,
      backgroundColor: "#d5ab1f",
      androidScaleType: "CENTER_CROP"
    }
  }
};

export default config;
