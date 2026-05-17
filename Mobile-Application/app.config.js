export default {
  expo: {
    name: "iMpazamon",
    slug: "Mobile-Application",
    version: "1.0.0",
    orientation: "portrait",
    icon: "./assets/splash-icon.png",
    userInterfaceStyle: "light",

    androidStatusBar: {
      backgroundColor: "#F5F7FF",
      barStyle: "dark-content"
    },

    androidNavigationBar: {
      backgroundColor: "#F5F7FF",
      barStyle: "dark-content"
    },

    splash: {
      image: "./assets/splash-icon.png",
      resizeMode: "contain",
      backgroundColor: "#F5F7FF"
    },

    ios: {
      supportsTablet: true
    },

    android: {
      adaptiveIcon: {
        foregroundImage: "./assets/splash-icon.png",
        backgroundColor: "#F5F7FF"
      },
      edgeToEdgeEnabled: false,
      googleServicesFile: process.env.GOOGLE_SERVICES_JSON || "../google-services.json",
      package: "com.powerteldev.MobileApplication"
    },

    web: {
      favicon: "./assets/favicon.png"
    },

    extra: {
      apiUrl: process.env.API_URL || "https://impazamon.powertel.co.zw/api",
      eas: {
        projectId: "eb97b19f-93a1-422e-a0c5-2c8201a1298c"
      }
    },

    runtimeVersion: {
      policy: "appVersion"
    }
  }
};
